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
    }
}
