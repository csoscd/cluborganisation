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
        }

        return $data;
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
