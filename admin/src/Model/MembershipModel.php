<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Model;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;

class MembershipModel extends AdminModel
{
    public $typeAlias = 'com_cluborganisation.membership';

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_cluborganisation.membership',
            'membership',
            ['control' => 'jform', 'load_data' => $loadData]
        );
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $data = $app->getUserState('com_cluborganisation.edit.membership.data', []);
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }

    public function getTable($type = 'Membership', $prefix = 'CSOSCD\\Component\\ClubOrganisation\\Administrator\\Table\\', $config = [])
    {
        return parent::getTable($type, $prefix, $config);
    }

    /**
     * Methode zum Vorbereiten der gespeicherten Daten
     *
     * @param   \Joomla\CMS\Table\Table  $table  Table-Objekt
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function prepareTable($table)
    {
        // Leeres end Datum auf NULL setzen (für laufende Mitgliedschaften)
        if (empty($table->end)) {
            $table->end = null;
        }

        // Leere depends_on_membership_id auf NULL setzen
        if (empty($table->depends_on_membership_id)) {
            $table->depends_on_membership_id = null;
        }
    }

    /**
     * Speichert die Mitgliedschaft und kaskadiert Enddatum-Änderungen an abhängige Mitgliedschaften.
     *
     * Regeln:
     * - Enddatum gesetzt/geändert: Abhängige ohne Enddatum oder mit späterem Enddatum werden mitgesetzt.
     *   Abhängige mit früherem Enddatum bleiben unverändert.
     * - Enddatum entfernt: Abhängige, die dasselbe Enddatum wie das bisherige Parent-Enddatum haben,
     *   werden ebenfalls auf "unbefristet" gesetzt. Abhängige mit einem anderen (früheren) Enddatum
     *   bleiben unverändert.
     *
     * @param   array  $data  Formulardaten
     *
     * @return  boolean  True bei Erfolg
     *
     * @since   2.3.0
     */
    public function save($data)
    {
        $itemId = !empty($data['id']) ? (int) $data['id'] : 0;
        $newEnd = !empty($data['end']) ? $data['end'] : null;
        $oldEnd = null;

        // Bisheriges Enddatum vor dem Speichern lesen (für Cascade-Entfernung)
        if ($itemId > 0) {
            $db    = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->quoteName('end'))
                ->from($db->quoteName('#__cluborganisation_memberships'))
                ->where($db->quoteName('id') . ' = ' . $itemId);
            $db->setQuery($query);
            $oldEnd = $db->loadResult(); // null wenn kein Enddatum gesetzt
        }

        $result = parent::save($data);

        if ($result) {
            $savedId = (int) $this->getState($this->getName() . '.id');
            if ($savedId > 0) {
                if ($newEnd !== null) {
                    // Enddatum gesetzt oder geändert
                    $this->cascadeEndDateToDependents($savedId, $newEnd);
                } elseif ($oldEnd !== null && $newEnd === null) {
                    // Enddatum entfernt: bei abhängigen Mitgliedschaften mit gleichem Enddatum entfernen
                    $this->cascadeRemoveEndDateFromDependents($savedId, $oldEnd);
                }
            }
        }

        return $result;
    }

    /**
     * Setzt das Enddatum abhängiger Mitgliedschaften, sofern ihr Enddatum
     * noch nicht gesetzt ist oder nach dem neuen Enddatum liegt.
     * Abhängige mit einem früheren Enddatum bleiben unverändert.
     *
     * @param   int     $parentMembershipId  ID der übergeordneten Mitgliedschaft
     * @param   string  $endDate             Neues Enddatum (YYYY-MM-DD)
     *
     * @return  void
     *
     * @since   2.3.0
     */
    private function cascadeEndDateToDependents(int $parentMembershipId, string $endDate): void
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__cluborganisation_memberships'))
            ->set($db->quoteName('end') . ' = ' . $db->quote($endDate))
            ->where($db->quoteName('depends_on_membership_id') . ' = ' . $parentMembershipId)
            ->where(
                '(' . $db->quoteName('end') . ' IS NULL'
                . ' OR ' . $db->quoteName('end') . ' > ' . $db->quote($endDate) . ')'
            );
        $db->setQuery($query);
        $db->execute();
    }

    /**
     * Entfernt das Enddatum abhängiger Mitgliedschaften, die dasselbe Enddatum
     * wie das bisherige Parent-Enddatum haben.
     * Abhängige mit einem anderen (früheren) Enddatum bleiben unverändert.
     *
     * @param   int     $parentMembershipId  ID der übergeordneten Mitgliedschaft
     * @param   string  $oldEndDate          Bisheriges Enddatum des Parents (YYYY-MM-DD)
     *
     * @return  void
     *
     * @since   2.3.0
     */
    private function cascadeRemoveEndDateFromDependents(int $parentMembershipId, string $oldEndDate): void
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__cluborganisation_memberships'))
            ->set($db->quoteName('end') . ' = NULL')
            ->where($db->quoteName('depends_on_membership_id') . ' = ' . $parentMembershipId)
            ->where($db->quoteName('end') . ' = ' . $db->quote($oldEndDate));
        $db->setQuery($query);
        $db->execute();
    }
}
