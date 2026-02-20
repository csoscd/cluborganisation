<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use CSOSCD\Component\ClubOrganisation\Administrator\Helper\EncryptionHelper;

/**
 * MembershipBanks List Controller
 *
 * @since  1.0.0
 */
class MembershipbanksController extends AdminController
{
    /**
     * @inheritDoc
     */
    public function getModel($name = 'Membershipbank', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Nimmt den Verschlüsselungsschlüssel entgegen, validiert ihn und speichert
     * ihn in der PHP-Session für die Dauer der Browser-Sitzung.
     *
     * Der Schlüssel wird bewusst in der Server-seitigen PHP-Session gespeichert
     * (nicht in der Datenbank oder in Konfigurationsdateien), sodass er nach
     * dem Schließen des Browsers automatisch verfällt.
     *
     * @return  void
     *
     * @since   1.9.0
     */
    public function unlock()
    {
        $this->checkToken();

        $app   = $this->app;
        $input = $app->input;
        $key   = $input->post->getString('encryption_key', '');

        if (empty($key)) {
            $app->enqueueMessage(Text::_('COM_CLUBORGANISATION_ERROR_NO_ENCRYPTION_KEY'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_cluborganisation&view=membershipbanks', false));
            return;
        }

        // Schlüssel prüfen: Versuch, vorhandene Daten zu entschlüsseln
        $model = $this->getModel('Membershipbanks', 'Administrator', ['ignore_request' => true]);

        if (!$model->verifyEncryptionKey($key)) {
            $app->enqueueMessage(Text::_('COM_CLUBORGANISATION_ERROR_INVALID_ENCRYPTION_KEY'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_cluborganisation&view=membershipbanks', false));
            return;
        }

        // Schlüssel erst nach erfolgreicher Validierung in Session speichern
        EncryptionHelper::setEncryptionKey($key);

        $app->enqueueMessage(Text::_('COM_CLUBORGANISATION_ENCRYPTION_KEY_SET'), 'message');
        $this->setRedirect(Route::_('index.php?option=com_cluborganisation&view=membershipbanks', false));
    }

    /**
     * Löscht den Verschlüsselungsschlüssel aus der Session.
     * Nach diesem Aufruf sind Bankdaten wieder gesperrt.
     *
     * @return  void
     *
     * @since   1.9.0
     */
    public function lock()
    {
        $this->checkToken();

        EncryptionHelper::clearEncryptionKey();

        $this->app->enqueueMessage(Text::_('COM_CLUBORGANISATION_ENCRYPTION_KEY_CLEARED'), 'message');
        $this->setRedirect(Route::_('index.php?option=com_cluborganisation&view=membershipbanks', false));
    }

    /**
     * Verschlüsselt alle Bankdaten mit einem neuen Schlüssel neu (Key Rotation).
     * Erfordert den aktuellen und den neuen Schlüssel.
     * Nach erfolgreicher Rotation ist der neue Schlüssel in der Session aktiv.
     *
     * @return  void
     *
     * @since   1.9.0
     */
    public function reencrypt()
    {
        $this->checkToken();

        $app            = $this->app;
        $input          = $app->input;
        $oldKey         = $input->post->getString('old_encryption_key', '');
        $newKey         = $input->post->getString('new_encryption_key', '');
        $newKeyConfirm  = $input->post->getString('new_encryption_key_confirm', '');

        $redirectUrl = Route::_('index.php?option=com_cluborganisation&view=membershipbanks', false);

        if (empty($oldKey) || empty($newKey)) {
            $app->enqueueMessage(Text::_('COM_CLUBORGANISATION_ERROR_REENCRYPT_KEYS_REQUIRED'), 'error');
            $this->setRedirect($redirectUrl);
            return;
        }

        if ($newKey !== $newKeyConfirm) {
            $app->enqueueMessage(Text::_('COM_CLUBORGANISATION_ERROR_KEYS_DO_NOT_MATCH'), 'error');
            $this->setRedirect($redirectUrl);
            return;
        }

        $model = $this->getModel('Membershipbanks', 'Administrator', ['ignore_request' => true]);

        // Alten Schlüssel validieren
        if (!$model->verifyEncryptionKey($oldKey)) {
            $app->enqueueMessage(Text::_('COM_CLUBORGANISATION_ERROR_INVALID_ENCRYPTION_KEY'), 'error');
            $this->setRedirect($redirectUrl);
            return;
        }

        // Neuverschlüsselung durchführen
        $count = $model->reencryptAll($oldKey, $newKey);

        if ($count === false) {
            $error = $model->getError();
            $app->enqueueMessage(Text::_('COM_CLUBORGANISATION_ERROR_REENCRYPT_FAILED') . ($error ? ': ' . $error : ''), 'error');
            $this->setRedirect($redirectUrl);
            return;
        }

        // Neuen Schlüssel in Session setzen
        EncryptionHelper::setEncryptionKey($newKey);

        $app->enqueueMessage(Text::sprintf('COM_CLUBORGANISATION_REENCRYPT_SUCCESS', $count), 'message');
        $this->setRedirect($redirectUrl);
    }
}
