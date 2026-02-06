<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cluborganisation
 *
 * @copyright   Copyright (C) 2025 Christian Schulz
 * @license     GPL-2.0-or-later
 */

declare(strict_types=1);

namespace Joomla\Component\Cluborganisation\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Component\Cluborganisation\Administrator\Service\CryptoHelper;

/**
 * Model for a membership bank entry.
 */
class MembershipBankModel extends AdminModel
{
    /**
     * @inheritDoc
     */
    public function getTable($name = 'MembershipBank', $prefix = 'Administrator\\', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    /**
     * @inheritDoc
     */
    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm('com_cluborganisation.membershipbank', 'membershipbank', ['control' => 'jform', 'load_data' => $loadData]);
    }

    /**
     * @inheritDoc
     */
    protected function loadFormData()
    {
        $data = parent::loadFormData();
        $key = Factory::getApplication()->input->getString('encryption_key');
        if ($key) {
            $data['encryption_key'] = $key;
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);
        if (!$item) {
            return $item;
        }

        $key = Factory::getApplication()->input->getString('encryption_key');
        if (!$key) {
            $item->accountname = '';
            $item->iban = '';
            $item->bic = '';
            return $item;
        }

        $item->accountname = CryptoHelper::decrypt((string) $item->accountname, $key);
        $item->iban = CryptoHelper::decrypt((string) $item->iban, $key);
        $item->bic = $item->bic ? CryptoHelper::decrypt((string) $item->bic, $key) : '';

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function save($data)
    {
        $key = $data['encryption_key'] ?? '';
        if (!$key) {
            $this->setError(Text::_('COM_CLUBORGANISATION_ERROR_BANK_KEY_REQUIRED'));
            return false;
        }

        $data['accountname'] = CryptoHelper::encrypt($data['accountname'] ?? '', $key);
        $data['iban'] = CryptoHelper::encrypt($data['iban'] ?? '', $key);
        $data['bic'] = !empty($data['bic']) ? CryptoHelper::encrypt($data['bic'], $key) : null;

        $user = Factory::getApplication()->getIdentity();
        $now = Factory::getDate()->toSql();

        if (empty($data['id'])) {
            $data['createdby'] = $user->id;
            $data['createddate'] = $now;
        }

        $data['modifiedby'] = $user->id;
        $data['modifieddate'] = $now;

        unset($data['encryption_key']);

        return parent::save($data);
    }
}
