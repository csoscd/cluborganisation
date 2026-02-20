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
use Joomla\CMS\Factory;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

// Mitgliedschaftsdaten für JS laden (Namens-Vorbelegen + Datums-Validierung)
$db    = Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
$query = $db->getQuery(true)
    ->select([
        $db->quoteName('m.id'),
        $db->quoteName('m.begin', 'mbegin'),
        $db->quoteName('m.end',   'mend'),
        $db->quoteName('p.firstname'),
        $db->quoteName('p.lastname'),
    ])
    ->from($db->quoteName('#__cluborganisation_memberships', 'm'))
    ->join('LEFT', $db->quoteName('#__cluborganisation_persons', 'p') . ' ON p.id = m.person_id');
$db->setQuery($query);
$membershipDataRaw = $db->loadObjectList('id');

$membershipDataJs = [];
foreach ($membershipDataRaw as $mid => $m) {
    $membershipDataJs[$mid] = [
        'name'  => trim($m->firstname . ' ' . $m->lastname),
        'begin' => $m->mbegin ?? '',
        'end'   => $m->mend ?? '',
    ];
}
?>
<form
    action="<?php echo Route::_('index.php?option=com_cluborganisation&view=membershipbank&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post"
    name="adminForm"
    id="membershipbank-form"
    class="form-validate"
>
    <div class="alert alert-success">
        <span class="icon-lock-open me-2"></span>
        <?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIPBANK_ENCRYPTION_INFO'); ?>
    </div>

    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <?php echo $this->form->renderField('membership_id'); ?>

                    <hr>
                    <h4><?php echo Text::_('COM_CLUBORGANISATION_BANK_DATA'); ?></h4>
                    <?php echo $this->form->renderField('accountname'); ?>
                    <?php echo $this->form->renderField('iban'); ?>
                    <?php echo $this->form->renderField('bic'); ?>

                    <div id="bank-date-hint" class="alert alert-info d-none mt-2" role="alert">
                        <span class="icon-info-circle me-1"></span>
                        <span id="bank-date-hint-text"></span>
                    </div>

                    <?php echo $this->form->renderField('begin'); ?>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <?php echo Text::_('COM_CLUBORGANISATION_ENCRYPTION'); ?>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2">
                        <span class="icon-lock-open text-success fs-4"></span>
                        <div>
                            <div class="fw-semibold text-success">
                                <?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIPBANKS_ENCRYPTION_ACTIVE'); ?>
                            </div>
                            <small class="text-muted">
                                <?php echo Text::_('COM_CLUBORGANISATION_FIELD_ENCRYPTION_KEY_DESC'); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value="" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

<script>
(function () {
    'use strict';

    // Mitgliedschaftsdaten aus PHP
    var membershipData = <?php echo json_encode($membershipDataJs); ?>;

    // Aktuell gespeicherter Datensatz (zum Erkennen ob accountname vorbelegt werden soll)
    var currentId     = <?php echo (int) $this->item->id; ?>;
    var currentAccn   = <?php echo json_encode((string) ($this->item->accountname ?? '')); ?>;

    function formatDateDe(iso) {
        if (!iso || iso.length < 10) return iso;
        var p = iso.split('-');
        return p[2] + '.' + p[1] + '.' + p[0];
    }

    function onMembershipChange(membershipId) {
        var data = membershipData[membershipId];
        if (!data) return;

        // Kontoinhaber vorbelegen: nur bei neuem Eintrag oder wenn Feld leer
        var accountnameField = document.querySelector('[name="jform[accountname]"]');
        if (accountnameField && (currentId === 0 || accountnameField.value === '')) {
            if (data.name && data.name.trim() !== '') {
                accountnameField.value = data.name.trim();
            }
        }

        // Datumshinweis anzeigen
        var hint     = document.getElementById('bank-date-hint');
        var hintText = document.getElementById('bank-date-hint-text');
        if (hint && hintText) {
            var parts = [];
            if (data.begin) {
                parts.push('<?php echo Text::_('COM_CLUBORGANISATION_BANK_DATE_HINT_FROM'); ?>: ' + formatDateDe(data.begin));
            }
            if (data.end) {
                parts.push('<?php echo Text::_('COM_CLUBORGANISATION_BANK_DATE_HINT_TO'); ?>: ' + formatDateDe(data.end));
            } else {
                parts.push('<?php echo Text::_('COM_CLUBORGANISATION_BANK_DATE_HINT_ONGOING'); ?>');
            }
            hintText.textContent = parts.join(' | ');
            hint.classList.remove('d-none');
        }
    }

    // Auf Seitenlade: wenn Mitgliedschaft bereits ausgewählt
    document.addEventListener('DOMContentLoaded', function () {
        var sel = document.querySelector('[name="jform[membership_id]"]');
        if (!sel) return;

        if (sel.value) {
            onMembershipChange(sel.value);
        }

        sel.addEventListener('change', function () {
            onMembershipChange(this.value);
        });
    });
}());
</script>
