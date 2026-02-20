<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use CSOSCD\Component\ClubOrganisation\Administrator\Helper\EncryptionHelper;

/**
 * Model für das Bankverbindungs-Formular
 *
 * @since  1.0.0
 */
class MembershipbankModel extends AdminModel
{
    public $typeAlias = 'com_cluborganisation.membershipbank';

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_cluborganisation.membershipbank',
            'membershipbank',
            ['control' => 'jform', 'load_data' => $loadData]
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $app  = Factory::getApplication();
        $data = $app->getUserState('com_cluborganisation.edit.membershipbank.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    public function getTable(
        $type   = 'Membershipbank',
        $prefix = 'CSOSCD\\Component\\ClubOrganisation\\Administrator\\Table\\',
        $config = []
    ) {
        return parent::getTable($type, $prefix, $config);
    }


    /**
     * Speichert den Datensatz.
     * Der Schlüssel wird ausschließlich aus der Session gelesen.
     *
     * @param   array  $data  Formulardaten
     *
     * @return  bool
     *
     * @since   1.0.0
     */
    public function save($data)
    {
        if (!EncryptionHelper::hasEncryptionKey()) {
            $this->setError(Text::_('COM_CLUBORGANISATION_ERROR_NO_ENCRYPTION_KEY'));
            return false;
        }

        unset($data['encryption_key']);

        return parent::save($data);
    }
}
