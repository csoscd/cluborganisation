<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Api
 * @author      Christian Schulz
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Api\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\ApiController;

/**
 * REST-API Controller für den Mitglieder-Export.
 *
 * Endpunkt: GET /api/index.php/v1/cluborganisation/members
 *
 * Query-Parameter:
 *   active_memberships  (0|1, default 1)  Nur aktive Mitgliedschaften
 *   include_banks       (0|1, default 0)  Bankdaten einschließen
 *   active_banks        (0|1, default 1)  Nur aktive Bankverbindungen
 *   encryption_key      (string)          Pflicht wenn include_banks=1
 *
 * @since  2.0.0
 */
class MembersController extends ApiController
{
    protected $contentType = 'members';
    protected $default_view = 'members';

    /**
     * Überschreibt displayList() des ApiControllers.
     * Gibt Mitgliederdaten direkt als JSON aus, ohne Joomla-View-Klasse.
     *
     * @return  static
     *
     * @since   2.0.0
     */
    public function displayList()
    {
        // Berechtigung prüfen
        $user = $this->app->getIdentity();

        if (!$user || $user->guest) {
            $this->sendError(401, 'Authentication required');
            return $this;
        }

        if (!$user->authorise('core.manage', 'com_cluborganisation')
            && !$user->authorise('core.admin')) {
            $this->sendError(403, 'Insufficient permissions. Requires core.manage on com_cluborganisation.');
            return $this;
        }

        // Parameter auslesen
        $input   = $this->input;
        // encryption_key kann wahlweise als Query-Parameter ODER als HTTP-Header
        // X-Encryption-Key übergeben werden. Der Header hat Vorrang, da er
        // nicht in Server-Logs landet und kein Shell-Quoting-Problem verursacht.
        $encryptionKeyFromHeader = trim((string) ($_SERVER['HTTP_X_ENCRYPTION_KEY'] ?? ''));
        $encryptionKeyFromQuery  = $input->getString('encryption_key', '');
        $encryptionKey           = $encryptionKeyFromHeader ?: $encryptionKeyFromQuery;

        $options = [
            'active_memberships' => (bool) $input->getInt('active_memberships', 1),
            'include_banks'      => (bool) $input->getInt('include_banks', 0),
            'active_banks'       => (bool) $input->getInt('active_banks', 1),
            'encryption_key'     => $encryptionKey,
        ];

        // Daten laden
        $model = new \CSOSCD\Component\ClubOrganisation\Api\Model\ExportModel();

        try {
            $members = $model->getMembers($options);
        } catch (\RuntimeException $e) {
            $this->sendError($e->getCode() ?: 500, $e->getMessage());
            return $this;
        } catch (\Exception $e) {
            $this->sendError(500, 'Internal server error: ' . $e->getMessage());
            return $this;
        }

        // Antwort senden
        $response = [
            'success'  => true,
            'exported' => date('c'),
            'options'  => [
                'active_memberships' => $options['active_memberships'],
                'include_banks'      => $options['include_banks'],
                'active_banks'       => $options['include_banks'] ? $options['active_banks'] : null,
            ],
            'count'   => count($members),
            'members' => $members,
        ];

        header('Content-Type: application/json; charset=utf-8');
        header('X-Export-Count: ' . count($members));

        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $this->app->close();

        return $this;
    }

    /**
     * Sendet eine JSON-Fehlerantwort und beendet die Ausgabe.
     *
     * @param   int     $code     HTTP-Statuscode
     * @param   string  $message  Fehlermeldung
     *
     * @return  void
     *
     * @since   2.0.0
     */
    private function sendError(int $code, string $message): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => false,
            'error'   => [
                'code'    => $code,
                'message' => $message,
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $this->app->close();
    }
}
