<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/** @var \CSOSCD\Component\ClubOrganisation\Administrator\View\Datacheck\HtmlView $this */

/**
 * Gibt eine Personen-Tabelle mit Edit-Link aus.
 *
 * @param  array   $rows     Ergebnis-Array
 * @param  string  $emptyKey Sprachkonstante für "leer"-Meldung
 *
 * @return void
 */
function co_datacheck_table(array $rows, string $emptyKey): void
{
    if (empty($rows)) : ?>
        <p class="text-success fw-semibold">
            <span class="icon-check" aria-hidden="true"></span>
            <?php echo Text::_($emptyKey); ?>
        </p>
    <?php return;
    endif; ?>
    <table class="table co-dc-table">
        <thead>
            <tr>
                <th><?php echo Text::_('COM_CLUBORGANISATION_DATACHECK_COL_NAME'); ?></th>
                <th class="text-end" style="width:6rem"><?php echo Text::_('COM_CLUBORGANISATION_DATACHECK_COL_EDIT'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row) : ?>
            <tr>
                <td><?php echo htmlspecialchars($row['lastname'] . ', ' . $row['firstname'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="text-end">
                    <a href="index.php?option=com_cluborganisation&view=person&layout=edit&id=<?php echo (int) $row['id']; ?>"
                       class="btn btn-sm btn-outline-secondary" title="<?php echo Text::_('COM_CLUBORGANISATION_DATACHECK_COL_EDIT'); ?>">
                        <span class="icon-edit" aria-hidden="true"></span>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php
}
?>

<style>
.co-dc-card {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: .375rem;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}
.co-dc-card h3 {
    font-size: 1.05rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: #132d6a;
    border-bottom: 2px solid #f29838;
    padding-bottom: .35rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.co-dc-badge {
    display: inline-block;
    background: #f29838;
    color: #132d6a;
    font-weight: 700;
    border-radius: 1rem;
    padding: .1rem .75rem;
    font-size: .8rem;
}
.co-dc-badge-ok {
    background: #d1e7dd;
    color: #0a3622;
}
.co-dc-table thead tr {
    background-color: #f29838;
    color: #132d6a;
}
.co-dc-table thead th {
    font-weight: 700;
    border-color: #f29838;
}
</style>

<?php
$groups = [
    [
        'label'   => Text::_('COM_CLUBORGANISATION_DATACHECK_MISSING_BIRTHDAY'),
        'rows'    => $this->missingBirthday,
        'emptyKey'=> 'COM_CLUBORGANISATION_DATACHECK_COMPLETE',
    ],
    [
        'label'   => Text::_('COM_CLUBORGANISATION_DATACHECK_MISSING_EMAIL'),
        'rows'    => $this->missingEmail,
        'emptyKey'=> 'COM_CLUBORGANISATION_DATACHECK_COMPLETE',
    ],
    [
        'label'   => Text::_('COM_CLUBORGANISATION_DATACHECK_MISSING_MOBILE'),
        'rows'    => $this->missingMobile,
        'emptyKey'=> 'COM_CLUBORGANISATION_DATACHECK_COMPLETE',
    ],
    [
        'label'   => Text::_('COM_CLUBORGANISATION_DATACHECK_NO_MEMBERSHIP'),
        'rows'    => $this->noMembership,
        'emptyKey'=> 'COM_CLUBORGANISATION_DATACHECK_COMPLETE',
    ],
    [
        'label'   => Text::_('COM_CLUBORGANISATION_DATACHECK_NO_USER'),
        'rows'    => $this->missingUser,
        'emptyKey'=> 'COM_CLUBORGANISATION_DATACHECK_COMPLETE',
    ],
];
?>

<div class="container-fluid px-0">

    <?php foreach ($groups as $group) :
        $count     = count($group['rows']);
        $badgeCss  = $count === 0 ? 'co-dc-badge co-dc-badge-ok' : 'co-dc-badge';
        $badgeText = $count === 0
            ? Text::_('COM_CLUBORGANISATION_DATACHECK_OK')
            : Text::sprintf('COM_CLUBORGANISATION_DATACHECK_COUNT', $count);
    ?>
    <div class="co-dc-card">
        <h3>
            <span><?php echo $group['label']; ?></span>
            <span class="<?php echo $badgeCss; ?>"><?php echo $badgeText; ?></span>
        </h3>
        <?php co_datacheck_table($group['rows'], $group['emptyKey']); ?>
    </div>
    <?php endforeach; ?>

</div><!-- /.container-fluid -->
