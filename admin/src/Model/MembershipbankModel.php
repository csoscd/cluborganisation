<?php
namespace CSOSCD\Component\ClubOrganisation\Administrator\Model;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;

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
        $app = Factory::getApplication();
        $data = $app->getUserState('com_cluborganisation.edit.membershipbank.data', []);
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }

    public function getTable($type = 'Membershipbank', $prefix = 'CSOSCD\\Component\\ClubOrganisation\\Administrator\\Table\\', $config = [])
    {
        return parent::getTable($type, $prefix, $config);
    }

    /**
     * Method to save the form data
     * Speichert den Encryption Key in der Session vor dem Speichern
     *
     * @param   array  $data  The form data
     *
     * @return  boolean  True on success
     *
     * @since   1.0.0
     */
    public function save($data)
    {
        $app = Factory::getApplication();
        
        // Hole encryption_key aus den Formulardaten
        if (!empty($data['encryption_key'])) {
            // Speichere Key in Session (wird von EncryptionHelper verwendet)
            $session = $app->getSession();
            $session->set('cluborganisation.encryption_key', $data['encryption_key']);
            
            // Entferne Key aus den zu speichernden Daten (soll nicht in DB)
            unset($data['encryption_key']);
        } else {
            // Kein Key angegeben
            $this->setError('COM_CLUBORGANISATION_ERROR_NO_ENCRYPTION_KEY');
            return false;
        }

        return parent::save($data);
    }
}
