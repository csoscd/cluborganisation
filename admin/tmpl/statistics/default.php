<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var \CSOSCD\Component\ClubOrganisation\Administrator\View\Statistics\HtmlView $this */

$currentYear = $this->currentYearNum;
$prevYear    = $currentYear - 1;
$nextYear    = $currentYear + 1;
?>

<style>
.co-stats-card {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: .375rem;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}
.co-stats-card h3 {
    font-size: 1.05rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: #132d6a;
    border-bottom: 2px solid #f29838;
    padding-bottom: .35rem;
}
.co-stats-table thead tr {
    background-color: #f29838;
    color: #132d6a;
}
.co-stats-table thead th {
    font-weight: 700;
    border-color: #f29838;
}
.co-stats-table tbody td,
.co-stats-table tfoot td {
    color: #000;
}
.co-stats-table tfoot tr {
    background-color: #f5f5f5;
    font-weight: 600;
}
.co-diff-pos  { color: #198754; font-weight: 600; }
.co-diff-neg  { color: #dc3545; font-weight: 600; }
.co-diff-zero { color: #6c757d; }
.co-chart-wrap {
    position: relative;
    height: 300px;
}
.co-anniversary-badge {
    display: inline-block;
    background: #f29838;
    color: #132d6a;
    font-weight: 700;
    border-radius: 1rem;
    padding: .1rem .65rem;
    font-size: .85rem;
}
</style>

<div class="container-fluid px-0">

    <!-- ── Zeile 1: Monatsentwicklung letztes + aktuelles Jahr ─────────── -->
    <div class="row g-3">
        <div class="col-xl-6">
            <div class="co-stats-card">
                <h3><?php echo Text::sprintf('COM_CLUBORGANISATION_STATS_MEMBER_DEV_LAST_YEAR', $prevYear); ?></h3>
                <div class="co-chart-wrap"><canvas id="coChartLastYear"></canvas></div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="co-stats-card">
                <h3><?php echo Text::sprintf('COM_CLUBORGANISATION_STATS_MEMBER_DEV_CURRENT_YEAR', $currentYear); ?></h3>
                <div class="co-chart-wrap"><canvas id="coChartCurrentYear"></canvas></div>
            </div>
        </div>
    </div>

    <!-- ── Zeile 2: Jahresentwicklung ────────────────────────────────────── -->
    <div class="row g-3">
        <div class="col-12">
            <div class="co-stats-card">
                <h3><?php echo Text::sprintf('COM_CLUBORGANISATION_STATS_MEMBER_DEV_SINCE', $this->startYear); ?></h3>
                <div class="co-chart-wrap"><canvas id="coChartYearly"></canvas></div>
            </div>
        </div>
    </div>

    <!-- ── Zeile 3: Mitgliederstruktur Tabelle + Diagramm ───────────────── -->
    <div class="row g-3">
        <div class="col-xl-5">
            <div class="co-stats-card">
                <h3><?php echo Text::_('COM_CLUBORGANISATION_STATS_MEMBER_STRUCTURE'); ?></h3>
                <table class="table co-stats-table">
                    <thead>
                        <tr>
                            <th><?php echo Text::_('COM_CLUBORGANISATION_STATS_COL_MEMBERSHIP_TYPE'); ?></th>
                            <th class="text-end"><?php echo $currentYear; ?></th>
                            <th class="text-end"><?php echo $prevYear; ?></th>
                            <th class="text-end"><?php echo Text::_('COM_CLUBORGANISATION_STATS_COL_DIFF'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->memberStructure as $row) :
                        $diff    = $row['current'] - $row['prev'];
                        $diffStr = ($diff > 0) ? '+' . $diff : ($diff < 0 ? (string) $diff : '+/-0');
                        $diffCss = ($diff > 0) ? 'co-diff-pos' : ($diff < 0 ? 'co-diff-neg' : 'co-diff-zero');
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['type_title'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-end"><?php echo (int) $row['current']; ?></td>
                            <td class="text-end"><?php echo (int) $row['prev']; ?></td>
                            <td class="text-end <?php echo $diffCss; ?>"><?php echo $diffStr; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-xl-7">
            <div class="co-stats-card">
                <h3><?php echo Text::_('COM_CLUBORGANISATION_STATS_MEMBER_STRUCTURE_CHART'); ?></h3>
                <div class="co-chart-wrap"><canvas id="coChartStructure"></canvas></div>
            </div>
        </div>
    </div>

    <!-- ── Zeile 4: Altersstruktur & Mitgliedschaftsdauer ───────────────── -->
    <div class="row g-3">
        <!-- Altersstruktur -->
        <div class="col-xl-6">
            <div class="co-stats-card">
                <h3><?php echo Text::_('COM_CLUBORGANISATION_STATS_AGE_STRUCTURE'); ?></h3>
                <table class="table co-stats-table">
                    <thead>
                        <tr>
                            <th><?php echo Text::_('COM_CLUBORGANISATION_STATS_COL_AGE'); ?></th>
                            <th class="text-end"><?php echo $currentYear; ?></th>
                            <th class="text-end"><?php echo $prevYear; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->ageStructure as $row) : ?>
                        <tr>
                            <td><?php echo Text::_($row['label']); ?></td>
                            <td class="text-end"><?php echo (int) $row['current']; ?></td>
                            <td class="text-end"><?php echo (int) $row['prev']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td><?php echo Text::_('COM_CLUBORGANISATION_STATS_AGE_AVG'); ?></td>
                            <td class="text-end">
                                <?php echo ($this->avgAgeCurrent !== null)
                                    ? number_format($this->avgAgeCurrent, 1, ',', '.')
                                    : '–'; ?>
                            </td>
                            <td class="text-end">
                                <?php echo ($this->avgAgePrev !== null)
                                    ? number_format($this->avgAgePrev, 1, ',', '.')
                                    : '–'; ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Mitgliedschaftsdauer -->
        <div class="col-xl-6">
            <div class="co-stats-card">
                <h3><?php echo Text::_('COM_CLUBORGANISATION_STATS_DURATION_STRUCTURE'); ?></h3>
                <table class="table co-stats-table">
                    <thead>
                        <tr>
                            <th><?php echo Text::_('COM_CLUBORGANISATION_STATS_COL_DURATION'); ?></th>
                            <th class="text-end"><?php echo $currentYear; ?></th>
                            <th class="text-end"><?php echo $prevYear; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->durationStructure as $row) : ?>
                        <tr>
                            <td><?php echo Text::_($row['label']); ?></td>
                            <td class="text-end"><?php echo (int) $row['current']; ?></td>
                            <td class="text-end"><?php echo (int) $row['prev']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td><?php echo Text::_('COM_CLUBORGANISATION_STATS_DUR_AVG'); ?></td>
                            <td class="text-end">
                                <?php echo ($this->avgDurationCurrent !== null)
                                    ? number_format($this->avgDurationCurrent, 1, ',', '.')
                                    : '–'; ?>
                            </td>
                            <td class="text-end">
                                <?php echo ($this->avgDurationPrev !== null)
                                    ? number_format($this->avgDurationPrev, 1, ',', '.')
                                    : '–'; ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- ── Zeile 5: Fluktuation ──────────────────────────────────────────── -->
    <div class="row g-3">
        <div class="col-xl-7">
            <div class="co-stats-card">
                <h3><?php echo Text::_('COM_CLUBORGANISATION_STATS_JOINS_LEAVES_CHART'); ?></h3>
                <div class="co-chart-wrap"><canvas id="coChartFluctuation"></canvas></div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="co-stats-card">
                <h3><?php echo Text::_('COM_CLUBORGANISATION_STATS_NET_CHANGE_CHART'); ?></h3>
                <div class="co-chart-wrap"><canvas id="coChartNetChange"></canvas></div>
            </div>
        </div>
    </div>

    <!-- ── Zeile 6: Jubiläen ─────────────────────────────────────────────── -->
    <div class="row g-3">
        <!-- Jubiläen aktuelles Jahr -->
        <div class="col-xl-6">
            <div class="co-stats-card">
                <h3><?php echo Text::sprintf('COM_CLUBORGANISATION_STATS_ANNIVERSARIES_CURRENT', $currentYear); ?></h3>
                <?php if (empty($this->anniversariesCurrent)) : ?>
                    <p class="text-muted"><?php echo Text::_('COM_CLUBORGANISATION_STATS_NO_ANNIVERSARIES'); ?></p>
                <?php else : ?>
                <table class="table co-stats-table">
                    <thead>
                        <tr>
                            <th><?php echo Text::_('COM_CLUBORGANISATION_STATS_COL_NAME'); ?></th>
                            <th class="text-center"><?php echo Text::_('COM_CLUBORGANISATION_STATS_COL_ENTRY_YEAR'); ?></th>
                            <th class="text-center"><?php echo Text::_('COM_CLUBORGANISATION_STATS_COL_ANNIVERSARY'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->anniversariesCurrent as $row) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['lastname'] . ', ' . $row['firstname'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-center"><?php echo $row['entry_year']; ?></td>
                            <td class="text-center">
                                <span class="co-anniversary-badge"><?php echo $row['years']; ?> <?php echo Text::_('COM_CLUBORGANISATION_STATS_YEARS'); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Jubiläen nächstes Jahr (Vorschau) -->
        <div class="col-xl-6">
            <div class="co-stats-card">
                <h3><?php echo Text::sprintf('COM_CLUBORGANISATION_STATS_ANNIVERSARIES_NEXT', $nextYear); ?></h3>
                <?php if (empty($this->anniversariesNext)) : ?>
                    <p class="text-muted"><?php echo Text::_('COM_CLUBORGANISATION_STATS_NO_ANNIVERSARIES'); ?></p>
                <?php else : ?>
                <table class="table co-stats-table">
                    <thead>
                        <tr>
                            <th><?php echo Text::_('COM_CLUBORGANISATION_STATS_COL_NAME'); ?></th>
                            <th class="text-center"><?php echo Text::_('COM_CLUBORGANISATION_STATS_COL_ENTRY_YEAR'); ?></th>
                            <th class="text-center"><?php echo Text::_('COM_CLUBORGANISATION_STATS_COL_ANNIVERSARY'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($this->anniversariesNext as $row) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['lastname'] . ', ' . $row['firstname'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="text-center"><?php echo $row['entry_year']; ?></td>
                            <td class="text-center">
                                <span class="co-anniversary-badge"><?php echo $row['years']; ?> <?php echo Text::_('COM_CLUBORGANISATION_STATS_YEARS'); ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div><!-- /.container-fluid -->
