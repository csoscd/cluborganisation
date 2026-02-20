<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz
 * @license     GNU General Public License version 3 or later
 *
 * Schreibgeschützte Detailansicht einer Bankverbindung.
 * Die Bankdaten sind bereits durch MembershipbankTable::load() entschlüsselt.
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

// Felder sind bereits entschlüsselt (MembershipbankTable::load() hat das erledigt)
$accountname = htmlspecialchars((string) ($this->item->accountname ?? ''), ENT_QUOTES, 'UTF-8');
$iban        = htmlspecialchars((string) ($this->item->iban ?? ''), ENT_QUOTES, 'UTF-8');
$bic         = htmlspecialchars((string) ($this->item->bic ?? ''), ENT_QUOTES, 'UTF-8');

// Zugehörige Mitgliedschaft ermitteln
$membershipLabel = '';
if (!empty($this->item->membership_id)) {
    try {
        $db    = \Joomla\CMS\Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->select([
                $db->quoteName('p.lastname'),
                $db->quoteName('p.firstname'),
                $db->quoteName('p.member_no'),
                $db->quoteName('m.begin', 'mbegin'),
                $db->quoteName('m.end',   'mend'),
                $db->quoteName('t.title',  'type_title'),
            ])
            ->from($db->quoteName('#__cluborganisation_memberships', 'm'))
            ->join('LEFT', $db->quoteName('#__cluborganisation_persons', 'p') . ' ON p.id = m.person_id')
            ->join('LEFT', $db->quoteName('#__cluborganisation_membershiptypes', 't') . ' ON t.id = m.type')
            ->where($db->quoteName('m.id') . ' = ' . (int) $this->item->membership_id);
        $db->setQuery($query);
        $ms = $db->loadObject();
        if ($ms) {
            $endLabel        = $ms->mend ? HTMLHelper::_('date', $ms->mend, 'd.m.Y') : Text::_('COM_CLUBORGANISATION_ONGOING');
            $membershipLabel = htmlspecialchars(
                $ms->lastname . ', ' . $ms->firstname . ' (' . $ms->member_no . ')' .
                ' — ' . $ms->type_title .
                ' (' . HTMLHelper::_('date', $ms->mbegin, 'd.m.Y') . ' – ' . $endLabel . ')',
                ENT_QUOTES, 'UTF-8'
            );
        }
    } catch (\Exception $e) {
        $membershipLabel = (string) $this->item->membership_id;
    }
}
?>

<div class="row justify-content-center mt-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <span class="icon-lock-open me-2 text-success"></span>
                    <strong><?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIPBANK_DETAILS'); ?></strong>
                </span>
                <span class="badge bg-secondary">ID: <?php echo (int) $this->item->id; ?></span>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th class="w-35 text-muted"><?php echo Text::_('COM_CLUBORGANISATION_FIELD_MEMBERSHIP'); ?></th>
                            <td><?php echo $membershipLabel ?: '–'; ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted"><?php echo Text::_('COM_CLUBORGANISATION_FIELD_ACCOUNTNAME'); ?></th>
                            <td><?php echo $accountname ?: '–'; ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted"><?php echo Text::_('COM_CLUBORGANISATION_FIELD_IBAN'); ?></th>
                            <td><code><?php echo $iban ?: '–'; ?></code></td>
                        </tr>
                        <tr>
                            <th class="text-muted"><?php echo Text::_('COM_CLUBORGANISATION_FIELD_BIC'); ?></th>
                            <td><?php echo $bic ?: '–'; ?></td>
                        </tr>
                        <tr>
                            <th class="text-muted"><?php echo Text::_('COM_CLUBORGANISATION_FIELD_BEGIN'); ?></th>
                            <td><?php echo !empty($this->item->begin) ? HTMLHelper::_('date', $this->item->begin, 'd.m.Y') : '–'; ?></td>
                        </tr>
                    </tbody>
                </table>

                <hr>
                <h6 class="text-muted"><?php echo Text::_('COM_CLUBORGANISATION_TAB_PUBLISHING'); ?></h6>
                <table class="table table-borderless table-sm text-muted small">
                    <tbody>
                        <tr>
                            <th class="w-35"><?php echo Text::_('COM_CLUBORGANISATION_FIELD_CREATED'); ?></th>
                            <td><?php echo !empty($this->item->created) ? HTMLHelper::_('date', $this->item->created, 'd.m.Y H:i') : '–'; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Text::_('COM_CLUBORGANISATION_FIELD_MODIFIED'); ?></th>
                            <td><?php echo !empty($this->item->modified) ? HTMLHelper::_('date', $this->item->modified, 'd.m.Y H:i') : '–'; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                <a href="<?php echo Route::_('index.php?option=com_cluborganisation&view=membershipbanks'); ?>"
                   class="btn btn-secondary">
                    <span class="icon-arrow-left me-1"></span>
                    <?php echo Text::_('COM_CLUBORGANISATION_CLOSE'); ?>
                </a>
                <a href="<?php echo Route::_('index.php?option=com_cluborganisation&view=membershipbank&layout=edit&id=' . (int) $this->item->id); ?>"
                   class="btn btn-primary ms-2">
                    <span class="icon-edit me-1"></span>
                    <?php echo Text::_('JEDIT'); ?>
                </a>
            </div>
        </div>
    </div>
</div>
