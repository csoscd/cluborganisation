<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @copyright   2026 Christian Schulz
 * @license     GNU General Public License version 2 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;

/**
 * Person Edit Model
 *
 * @since  1.0.0
 */
class PersonModel extends AdminModel
{
    /**
     * Der Typ-Alias
     *
     * @var    string
     * @since  1.0.0
     */
    public $typeAlias = 'com_cluborganisation.person';

    /**
     * Methode zum Abrufen des Formulars
     *
     * @param   array    $data      Daten für das Formular
     * @param   boolean  $loadData  True wenn Daten geladen werden sollen
     *
     * @return  Form|boolean  Form-Objekt bei Erfolg, false bei Fehler
     *
     * @since   1.0.0
     */
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_cluborganisation.person',
            'person',
            [
                'control' => 'jform',
                'load_data' => $loadData
            ]
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Methode zum Laden der Formulardaten
     *
     * @return  mixed  Die Formulardaten
     *
     * @since   1.0.0
     */
    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $data = $app->getUserState('com_cluborganisation.edit.person.data', []);

        if (empty($data)) {
            $data = $this->getItem();
            
            // Automatische Mitgliedsnummern-Vergabe für neue Personen
            if (empty($data->id)) {
                $memberNo = $this->generateMemberNumber();
                if ($memberNo !== null) {
                    $data->member_no = $memberNo;
                }
            }
        }

        return $data;
    }

    /**
     * Generiert die nächste Mitgliedsnummer basierend auf dem konfigurierten Pattern
     *
     * @return  string|null  Die generierte Mitgliedsnummer oder null wenn deaktiviert
     *
     * @since   1.2.0
     */
    protected function generateMemberNumber()
    {
        $params = \Joomla\CMS\Component\ComponentHelper::getParams('com_cluborganisation');
        
        // Prüfe ob automatische Vergabe aktiviert ist
        if (!$params->get('auto_generate_member_no', 0)) {
            return null;
        }
        
        $pattern = $params->get('member_no_pattern', '[No]');
        
        // Wenn kein [No] im Pattern, dann Pattern direkt zurückgeben
        if (strpos($pattern, '[No]') === false) {
            return $pattern;
        }
        
        // Extrahiere Präfix und Suffix aus dem Pattern
        $parts = explode('[No]', $pattern);
        $prefix = $parts[0] ?? '';
        $suffix = $parts[1] ?? '';
        
        // Finde die höchste existierende Nummer mit diesem Pattern
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        
        // Baue REGEX für MySQL - escaped für REGEXP
        $prefixEscaped = preg_quote($prefix, '/');
        $suffixEscaped = preg_quote($suffix, '/');
        $regexPattern = '^' . $prefixEscaped . '([0-9]+)' . $suffixEscaped . '$';
        
        $query->select('member_no')
            ->from($db->quoteName('#__cluborganisation_persons'))
            ->where($db->quoteName('member_no') . ' REGEXP ' . $db->quote($regexPattern))
            ->order($db->quoteName('member_no') . ' DESC');
        
        $db->setQuery($query);
        $existingNumbers = $db->loadColumn();
        
        $maxNumber = 0;
        
        // Extrahiere Nummern aus den gefundenen Mitgliedsnummern
        foreach ($existingNumbers as $memberNo) {
            // Entferne Präfix und Suffix und extrahiere die Nummer
            if (strlen($prefix) > 0) {
                $memberNo = substr($memberNo, strlen($prefix));
            }
            if (strlen($suffix) > 0 && strlen($memberNo) >= strlen($suffix)) {
                $memberNo = substr($memberNo, 0, -strlen($suffix));
            }
            
            // Prüfe ob es eine Nummer ist
            if (is_numeric($memberNo)) {
                $number = (int)$memberNo;
                if ($number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
        }
        
        // Generiere die nächste Nummer
        $nextNumber = $maxNumber + 1;
        
        // Baue die neue Mitgliedsnummer
        $newMemberNo = $prefix . $nextNumber . $suffix;
        
        return $newMemberNo;
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
        // Leere Datumswerte auf NULL setzen
        if (empty($table->deceased)) {
            $table->deceased = null;
        }
        
        // Leere Integer-Werte auf NULL setzen
        if (empty($table->user_id)) {
            $table->user_id = null;
        }
    }
}
