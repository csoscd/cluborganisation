<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use CSOSCD\Component\ClubOrganisation\Administrator\Helper\EncryptionHelper;

// Bootstrap-Modal-Support explizit laden
HTMLHelper::_('bootstrap.modal');

/**
 * IBAN für Anzeige maskieren: Ländercode + ** + *** ... *** + letzte 4
 */
function maskIban(string $iban): string
{
    $iban = strtoupper(str_replace(' ', '', $iban));
    $len  = strlen($iban);
    if ($len < 6) {
        return str_repeat('*', $len);
    }
    $country = substr($iban, 0, 2);
    $last4   = substr($iban, -4);
    $stars   = str_repeat('*', max(0, $len - 6));
    return $country . '** ' . trim(chunk_split($stars . $last4, 4, ' '));
}

$encKey = EncryptionHelper::getEncryptionKey();
?>

<!-- Statusleiste -->
<div class="alert alert-success d-flex justify-content-between align-items-center mb-3 py-2">
    <span>
        <span class="icon-lock-open me-2"></span>
        <strong><?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIPBANKS_ENCRYPTION_ACTIVE'); ?></strong>
    </span>
    <div class="d-flex gap-2">
        <button
            type="button"
            class="btn btn-warning btn-sm"
            id="btn-reencrypt-open"
        >
            <span class="icon-refresh me-1"></span>
            <?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIPBANKS_REENCRYPT_BUTTON'); ?>
        </button>

        <form
            action="<?php echo Route::_('index.php?option=com_cluborganisation'); ?>"
            method="post"
            class="d-inline"
        >
            <input type="hidden" name="task" value="membershipbanks.lock" />
            <?php echo HTMLHelper::_('form.token'); ?>
            <button type="submit" class="btn btn-outline-secondary btn-sm">
                <span class="icon-lock me-1"></span>
                <?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIPBANKS_LOCK_BUTTON'); ?>
            </button>
        </form>
    </div>
</div>

<!-- Datenliste -->
<form
    action="<?php echo Route::_('index.php?option=com_cluborganisation&view=membershipbanks'); ?>"
    method="post"
    name="adminForm"
    id="adminForm"
>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <td class="w-1"><?php echo HTMLHelper::_('grid.checkall'); ?></td>
                <th class="w-5"><?php echo Text::_('COM_CLUBORGANISATION_FIELD_ID'); ?></th>
                <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_PERSON'); ?></th>
                <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_ACCOUNTNAME'); ?></th>
                <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_IBAN'); ?></th>
                <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_BIC'); ?></th>
                <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_BEGIN'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($this->items as $i => $item) : ?>
            <?php
                $accountname = EncryptionHelper::decrypt($item->accountname ?? '', $encKey);
                $iban        = EncryptionHelper::decrypt($item->iban ?? '', $encKey);
                $bic         = !empty($item->bic) ? EncryptionHelper::decrypt($item->bic, $encKey) : '';

                $accDisplay  = ($accountname !== false && $accountname !== '') ? htmlspecialchars($accountname, ENT_QUOTES, 'UTF-8') : '<span class="text-danger">⚠ ' . Text::_('COM_CLUBORGANISATION_ERROR_NO_ENCRYPTION_KEY') . '</span>';
                $ibanDisplay = ($iban !== false && $iban !== '')               ? '<code>' . htmlspecialchars(maskIban($iban), ENT_QUOTES, 'UTF-8') . '</code>' : '<span class="text-danger">⚠</span>';
                $bicDisplay  = ($bic !== false && $bic !== '')                 ? htmlspecialchars($bic, ENT_QUOTES, 'UTF-8') : '–';

                $personName = '';
                if (!empty($item->lastname) || !empty($item->firstname)) {
                    $personName = htmlspecialchars(trim($item->lastname . ', ' . $item->firstname), ENT_QUOTES, 'UTF-8');
                    if (!empty($item->member_no)) {
                        $personName .= ' <small class="text-muted">(' . htmlspecialchars($item->member_no, ENT_QUOTES, 'UTF-8') . ')</small>';
                    }
                }
            ?>
            <tr>
                <td><?php echo HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                <td>
                    <a href="<?php echo Route::_('index.php?option=com_cluborganisation&task=membershipbank.edit&id=' . (int) $item->id); ?>">
                        <?php echo (int) $item->id; ?>
                    </a>
                </td>
                <td><?php echo $personName ?: '<span class="text-muted">–</span>'; ?></td>
                <td><?php echo $accDisplay; ?></td>
                <td><?php echo $ibanDisplay; ?></td>
                <td><?php echo $bicDisplay; ?></td>
                <td><?php echo !empty($item->begin) ? HTMLHelper::_('date', $item->begin, 'd.m.Y') : '–'; ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($this->items)) : ?>
            <tr>
                <td colspan="7" class="text-center text-muted py-3">
                    <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

<!-- ===================================================
     Schlüssel rotieren – Overlay-Dialog (kein Bootstrap-Modal,
     um Konflikte mit Joomla-JS zu vermeiden)
     =================================================== -->
<div
    id="reencrypt-overlay"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
           background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center;"
>
    <div style="background:#fff; border-radius:8px; max-width:480px; width:94%; padding:0; box-shadow:0 8px 32px rgba(0,0,0,.35);">
        <div style="background:#ffc107; border-radius:8px 8px 0 0; padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
            <strong style="font-size:1.05rem;">
                <span class="icon-refresh me-2"></span>
                <?php echo Text::_('COM_CLUBORGANISATION_REENCRYPT_TITLE'); ?>
            </strong>
            <button type="button" id="btn-reencrypt-close" style="background:none;border:none;font-size:1.4rem;cursor:pointer;line-height:1;">&times;</button>
        </div>

        <form
            action="<?php echo Route::_('index.php?option=com_cluborganisation'); ?>"
            method="post"
            id="reencryptForm"
            style="padding:20px;"
        >
            <p class="text-muted small mb-3"><?php echo Text::_('COM_CLUBORGANISATION_REENCRYPT_DESC'); ?></p>

            <div class="alert alert-danger py-2 small">
                <span class="icon-exclamation-triangle me-1"></span>
                <?php echo Text::_('COM_CLUBORGANISATION_REENCRYPT_WARNING'); ?>
            </div>

            <div class="mb-3">
                <label for="old_encryption_key" class="form-label fw-semibold">
                    <?php echo Text::_('COM_CLUBORGANISATION_FIELD_OLD_ENCRYPTION_KEY'); ?> <span class="text-danger">*</span>
                </label>
                <input type="password" class="form-control" id="old_encryption_key" name="old_encryption_key" autocomplete="off" required />
                <div class="form-text"><?php echo Text::_('COM_CLUBORGANISATION_FIELD_OLD_ENCRYPTION_KEY_DESC'); ?></div>
            </div>

            <div class="mb-3">
                <label for="new_encryption_key" class="form-label fw-semibold">
                    <?php echo Text::_('COM_CLUBORGANISATION_FIELD_NEW_ENCRYPTION_KEY'); ?> <span class="text-danger">*</span>
                </label>
                <input type="password" class="form-control" id="new_encryption_key" name="new_encryption_key" autocomplete="new-password" required />
                <div class="form-text"><?php echo Text::_('COM_CLUBORGANISATION_FIELD_NEW_ENCRYPTION_KEY_DESC'); ?></div>
            </div>

            <div class="mb-3">
                <label for="new_encryption_key_confirm" class="form-label fw-semibold">
                    <?php echo Text::_('COM_CLUBORGANISATION_FIELD_NEW_ENCRYPTION_KEY_CONFIRM'); ?> <span class="text-danger">*</span>
                </label>
                <input type="password" class="form-control" id="new_encryption_key_confirm" name="new_encryption_key_confirm" autocomplete="new-password" required />
                <div class="form-text"><?php echo Text::_('COM_CLUBORGANISATION_FIELD_NEW_ENCRYPTION_KEY_CONFIRM_DESC'); ?></div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:8px;">
                <button type="button" class="btn btn-secondary" id="btn-reencrypt-cancel">
                    <?php echo Text::_('JCANCEL'); ?>
                </button>
                <button type="submit" class="btn btn-warning" id="btn-reencrypt-submit">
                    <span class="icon-refresh me-1"></span>
                    <?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIPBANKS_REENCRYPT_BUTTON'); ?>
                </button>
            </div>

            <input type="hidden" name="task" value="membershipbanks.reencrypt" />
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>
    </div>
</div>

<script>
(function () {
    'use strict';

    var overlay    = document.getElementById('reencrypt-overlay');
    var btnOpen    = document.getElementById('btn-reencrypt-open');
    var btnClose   = document.getElementById('btn-reencrypt-close');
    var btnCancel  = document.getElementById('btn-reencrypt-cancel');
    var form       = document.getElementById('reencryptForm');

    function openOverlay()  { overlay.style.display = 'flex'; }
    function closeOverlay() {
        overlay.style.display = 'none';
        form.reset();
    }

    if (btnOpen)   btnOpen.addEventListener('click',  openOverlay);
    if (btnClose)  btnClose.addEventListener('click',  closeOverlay);
    if (btnCancel) btnCancel.addEventListener('click', closeOverlay);

    // Klick außerhalb des Dialogs schließt ihn
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) { closeOverlay(); }
    });

    // Escape-Taste schließt den Dialog
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.style.display === 'flex') { closeOverlay(); }
    });

    // Client-seitige Validierung
    if (form) {
        form.addEventListener('submit', function (e) {
            var newKey     = document.getElementById('new_encryption_key').value;
            var confirmKey = document.getElementById('new_encryption_key_confirm').value;

            if (newKey !== confirmKey) {
                e.preventDefault();
                alert('<?php echo Text::_('COM_CLUBORGANISATION_ERROR_KEYS_DO_NOT_MATCH'); ?>');
                return;
            }

            if (newKey.length < 8) {
                e.preventDefault();
                alert('<?php echo Text::_('COM_CLUBORGANISATION_ERROR_KEY_TOO_SHORT'); ?>');
            }
        });
    }
}());
</script>
