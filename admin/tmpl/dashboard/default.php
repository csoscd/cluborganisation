<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

/** @var \CSOSCD\Component\ClubOrganisation\Administrator\View\Dashboard\HtmlView $this */

$monthName = (new \DateTime())->format('F'); // Englisch; für DE via Text::_ oder date_create
$monthDE   = [
    1=>'Januar',2=>'Februar',3=>'März',4=>'April',5=>'Mai',6=>'Juni',
    7=>'Juli',8=>'August',9=>'September',10=>'Oktober',11=>'November',12=>'Dezember'
][(int)date('n')];
$currentYear = (int) date('Y');

$datacheckTotal = array_sum($this->datacheckSummary);
$datacheckItems = [
    'birthday'    => Text::_('COM_CLUBORGANISATION_DATACHECK_MISSING_BIRTHDAY'),
    'email'       => Text::_('COM_CLUBORGANISATION_DATACHECK_MISSING_EMAIL'),
    'mobile'      => Text::_('COM_CLUBORGANISATION_DATACHECK_MISSING_MOBILE'),
    'user'        => Text::_('COM_CLUBORGANISATION_DATACHECK_NO_USER'),
    'noMembership'=> Text::_('COM_CLUBORGANISATION_DATACHECK_NO_MEMBERSHIP'),
    'activeNoActive'=> Text::_('COM_CLUBORGANISATION_DATACHECK_ACTIVE_NO_ACTIVE_MEMBERSHIP'),
];
?>
<style>
/* Icon-Grid */
.co-icon-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.co-icon-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .5rem;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: .5rem;
    padding: 1rem .5rem .75rem;
    text-decoration: none;
    color: #132d6a;
    transition: border-color .15s, box-shadow .15s, transform .1s;
}
.co-icon-btn:hover {
    border-color: #f29838;
    box-shadow: 0 2px 8px rgba(242,152,56,.25);
    transform: translateY(-2px);
    color: #132d6a;
    text-decoration: none;
}
.co-icon-img {
    width: 52px;
    height: 52px;
    object-fit: contain;
}
.co-icon-label {
    font-size: .75rem;
    font-weight: 600;
    text-align: center;
    line-height: 1.2;
    color: #132d6a;
}

/* ── Dashboard Layout ──────────────────────────────────────────── */
.co-dashboard { font-family: inherit; }

/* KPI-Kacheln */
.co-kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.co-kpi {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: .5rem;
    padding: 1.25rem 1.5rem;
    display: flex;
    flex-direction: column;
    gap: .25rem;
    border-top: 4px solid #f29838;
}
.co-kpi.co-kpi-alert { border-top-color: #dc3545; }
.co-kpi.co-kpi-ok    { border-top-color: #198754; }
.co-kpi-value {
    font-size: 2.2rem;
    font-weight: 800;
    color: #132d6a;
    line-height: 1;
}
.co-kpi-label {
    font-size: .78rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #6c757d;
}

/* Cards */
.co-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: .5rem;
    margin-bottom: 1.25rem;
    overflow: hidden;
}
.co-card-header {
    background: #132d6a;
    color: #fff;
    padding: .65rem 1.25rem;
    font-size: .88rem;
    font-weight: 700;
    letter-spacing: .03em;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: .5rem;
}
.co-card-header .icon-small {
    font-size: 1rem;
    opacity: .8;
}
.co-card-body { padding: 1.25rem; }

/* Tabellen */
.co-dash-table { margin-bottom: 0; }
.co-dash-table td, .co-dash-table th { padding: .55rem .75rem; }
.co-dash-table thead th {
    background: #f8f9fa;
    color: #132d6a;
    font-weight: 700;
    font-size: .83rem;
    text-transform: uppercase;
    letter-spacing: .03em;
    border-bottom: 2px solid #f29838;
}

/* Version-Badge */
.co-version-badge {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    background: #132d6a;
    color: #fff;
    border-radius: 2rem;
    padding: .25rem .9rem;
    font-size: .85rem;
    font-weight: 700;
}
.co-version-num { color: #f29838; }

/* Update-Alert */
.co-update-alert {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-left: 4px solid #ffc107;
    border-radius: .375rem;
    padding: .75rem 1rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: .5rem;
    font-size: .9rem;
}

/* Erweiterungs-Status */
.co-ext-row { display: flex; align-items: center; justify-content: space-between; padding: .6rem 0; border-bottom: 1px solid #f0f0f0; }
.co-ext-row:last-child { border-bottom: none; }
.co-ext-name { font-weight: 500; font-size: .9rem; }
.co-badge-installed { background:#d1e7dd; color:#0a3622; border-radius:1rem; padding:.2rem .7rem; font-size:.78rem; font-weight:700; }
.co-badge-disabled  { background:#fff3cd; color:#856404; border-radius:1rem; padding:.2rem .7rem; font-size:.78rem; font-weight:700; }
.co-badge-missing   { background:#f8d7da; color:#842029; border-radius:1rem; padding:.2rem .7rem; font-size:.78rem; font-weight:700; }
.co-menu-plugin-notice { margin-top:.75rem; padding:.55rem .75rem; background:#fff3cd; color:#856404; border-radius:.4rem; font-size:.82rem; border-left:3px solid #ffc107; }

/* Datacheck-Zeilen */
.co-dc-item { display:flex; align-items:center; justify-content:space-between; padding:.45rem 0; border-bottom:1px solid #f5f5f5; }
.co-dc-item:last-child { border-bottom:none; }
.co-dc-count { min-width:2rem; text-align:right; font-weight:700; }
.co-dc-count.has-issues { color:#dc3545; }
.co-dc-count.no-issues  { color:#198754; }

/* Jubiläen */
.co-jubilee-badge {
    background: #f29838;
    color: #132d6a;
    font-weight: 800;
    border-radius: 1rem;
    padding: .1rem .6rem;
    font-size: .8rem;
    white-space: nowrap;
}
</style>

<div class="co-dashboard">

    <?php if ($this->availableUpdate) : ?>
    <div class="co-update-alert">
        <span class="icon-warning" aria-hidden="true"></span>
        <span><?php echo Text::sprintf('COM_CLUBORGANISATION_DASHBOARD_UPDATE_AVAILABLE', $this->availableUpdate); ?></span>
        <a href="index.php?option=com_installer&view=update" class="btn btn-sm btn-warning ms-auto">
            <?php echo Text::_('COM_CLUBORGANISATION_DASHBOARD_GO_TO_UPDATES'); ?>
        </a>
    </div>
    <?php endif; ?>

    <!-- ── Zeile 1: KPI-Kacheln ─────────────────────────────────────────── -->
    <div class="co-kpi-grid">
        <div class="co-kpi">
            <div class="co-kpi-value"><?php echo $this->activePersons; ?></div>
            <div class="co-kpi-label"><?php echo Text::_('COM_CLUBORGANISATION_DASHBOARD_KPI_ACTIVE_PERSONS'); ?></div>
        </div>
        <div class="co-kpi">
            <div class="co-kpi-value"><?php echo $this->activeMemberships; ?></div>
            <div class="co-kpi-label"><?php echo Text::_('COM_CLUBORGANISATION_DASHBOARD_KPI_ACTIVE_MEMBERSHIPS'); ?></div>
        </div>
        <div class="co-kpi <?php echo $this->newThisMonth > 0 ? 'co-kpi-ok' : ''; ?>">
            <div class="co-kpi-value"><?php echo $this->newThisMonth; ?></div>
            <div class="co-kpi-label"><?php echo Text::sprintf('COM_CLUBORGANISATION_DASHBOARD_KPI_NEW_THIS_MONTH', $monthDE); ?></div>
        </div>
        <div class="co-kpi <?php echo $this->endingThisYear > 0 ? 'co-kpi-alert' : 'co-kpi-ok'; ?>">
            <div class="co-kpi-value"><?php echo $this->endingThisYear; ?></div>
            <div class="co-kpi-label"><?php echo Text::sprintf('COM_CLUBORGANISATION_DASHBOARD_KPI_ENDING_THIS_YEAR', $currentYear); ?></div>
        </div>
        <?php if ($datacheckTotal > 0) : ?>
        <div class="co-kpi co-kpi-alert">
            <div class="co-kpi-value"><?php echo $datacheckTotal; ?></div>
            <div class="co-kpi-label"><?php echo Text::_('COM_CLUBORGANISATION_DASHBOARD_KPI_DATA_ISSUES'); ?></div>
        </div>
        <?php endif; ?>
    </div>


    <!-- ── Icon-Grid: Schnellzugriff ────────────────────────────────────────── -->
    <div class="co-icon-grid">
        <?php
        $coParams = \Joomla\CMS\Component\ComponentHelper::getParams('com_cluborganisation');

        // Alle Icons – 'show' steuert ob der Eintrag angezeigt wird.
        // null = immer sichtbar, sonst Parametername aus der Komponentenkonfiguration.
        $coIcons = [
            [
                'img'   => 'co_dash_person.png',
                'label' => Text::_('COM_CLUBORGANISATION_MENU_PERSONS'),
                'href'  => 'index.php?option=com_cluborganisation&view=persons',
                'show'  => null,
            ],
            [
                'img'   => 'co_dash_membership.png',
                'label' => Text::_('COM_CLUBORGANISATION_MENU_MEMBERSHIPS'),
                'href'  => 'index.php?option=com_cluborganisation&view=memberships',
                'show'  => null,
            ],
            [
                'img'   => 'co_dash_bank.png',
                'label' => Text::_('COM_CLUBORGANISATION_MENU_MEMBERSHIPBANKS'),
                'href'  => 'index.php?option=com_cluborganisation&view=membershipbanks',
                'show'  => null,
            ],
            [
                'img'   => 'co_dash_fee.png',
                'label' => Text::_('COM_CLUBORGANISATION_MENU_MEMBERSHIPTYPEFEES'),
                'href'  => 'index.php?option=com_cluborganisation&view=membershiptypefees',
                'show'  => null,
            ],
            [
                'img'   => 'co_dash_feeview.png',
                'label' => Text::_('COM_CLUBORGANISATION_MENU_FEEREPORT'),
                'href'  => 'index.php?option=com_cluborganisation&view=feereport',
                'show'  => null,
            ],
            [
                'img'   => 'co_dash_statistics.png',
                'label' => Text::_('COM_CLUBORGANISATION_MENU_STATISTICS'),
                'href'  => 'index.php?option=com_cluborganisation&view=statistics',
                'show'  => null,
            ],
            [
                'img'   => 'co_dash_check.png',
                'label' => Text::_('COM_CLUBORGANISATION_MENU_DATACHECK'),
                'href'  => 'index.php?option=com_cluborganisation&view=datacheck',
                'show'  => null,
            ],
            [
                'img'   => 'co_dash_dsgvo.png',
                'label' => Text::_('COM_CLUBORGANISATION_MENU_DSGVO_CLEANUP'),
                'href'  => 'index.php?option=com_cluborganisation&view=dsgvocleanup',
                'show'  => 'show_dsgvo',
            ],
        ];
        $mediaBase = \Joomla\CMS\Uri\Uri::root() . 'media/com_cluborganisation/images/';
        foreach ($coIcons as $icon) :
            // Icon ausblenden wenn der zugehörige Menüpunkt deaktiviert ist
            if ($icon['show'] !== null && !(int) $coParams->get($icon['show'], 1)) {
                continue;
            }
        ?>
        <a href="<?php echo $icon['href']; ?>" class="co-icon-btn">
            <img src="<?php echo $mediaBase . $icon['img']; ?>"
                 alt="<?php echo htmlspecialchars($icon['label'], ENT_QUOTES, 'UTF-8'); ?>"
                 class="co-icon-img">
            <span class="co-icon-label"><?php echo $icon['label']; ?></span>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- ── Zeile 2: Version + Erweiterungen | Datacheck ─────────────────── -->
    <div class="row g-3">

        <!-- Version & Erweiterungen -->
        <div class="col-xl-6">

            <!-- Version -->
            <div class="co-card">
                <div class="co-card-header">
                    <span class="icon-info-circle icon-small" aria-hidden="true"></span>
                    <?php echo Text::_('COM_CLUBORGANISATION_DASHBOARD_VERSION_TITLE'); ?>
                </div>
                <div class="co-card-body">
                    <table class="table co-dash-table">
                        <tbody>
                            <tr>
                                <td><?php echo Text::_('COM_CLUBORGANISATION_DASHBOARD_VERSION_INSTALLED'); ?></td>
                                <td class="text-end">
                                    <span class="co-version-badge">
                                        ClubOrganisation <span class="co-version-num">v<?php echo htmlspecialchars($this->installedVersion, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Erweiterungen -->
            <div class="co-card">
                <div class="co-card-header">
                    <span class="icon-puzzle-piece icon-small" aria-hidden="true"></span>
                    <?php echo Text::_('COM_CLUBORGANISATION_DASHBOARD_EXTENSIONS_TITLE'); ?>
                </div>
                <div class="co-card-body">
                    <?php foreach ($this->extensions as $ext) : ?>
                    <div class="co-ext-row">
                        <span class="co-ext-name"><?php echo htmlspecialchars($ext['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php if ($ext['installed'] && $ext['enabled']) : ?>
                            <span class="co-badge-installed">✓ <?php echo Text::_('COM_CLUBORGANISATION_DASHBOARD_EXT_INSTALLED'); ?></span>
                        <?php elseif ($ext['installed'] && !$ext['enabled']) : ?>
                            <?php $managerUrl = ($ext['type'] === 'plugin')
                                ? 'index.php?option=com_plugins'
                                : 'index.php?option=com_modules'; ?>
                            <a href="<?php echo $managerUrl; ?>" class="co-badge-disabled">
                                ⚠ <?php echo Text::_('COM_CLUBORGANISATION_DASHBOARD_EXT_DISABLED'); ?>
                            </a>
                        <?php else : ?>
                            <a href="<?php echo $ext['url']; ?>" target="_blank" rel="noopener" class="co-badge-missing">
                                ↓ <?php echo Text::_('COM_CLUBORGANISATION_DASHBOARD_EXT_DOWNLOAD'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>

                    <?php
                    // Hinweis wenn Menü-Plugin fehlt oder deaktiviert
                    $menuPlugin = null;
                    foreach ($this->extensions as $ext) {
                        if (strpos($ext['name'], 'ClubOrganisation Menu') !== false) {
                            $menuPlugin = $ext;
                            break;
                        }
                    }
                    if ($menuPlugin && (!$menuPlugin['installed'] || !$menuPlugin['enabled'])) : ?>
                    <div class="co-menu-plugin-notice">
                        <span class="icon-warning icon-small" aria-hidden="true"></span>
                        <?php echo Text::_('COM_CLUBORGANISATION_DASHBOARD_MENU_PLUGIN_NOTICE'); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- Datacheck -->
        <div class="col-xl-6">
            <div class="co-card" style="height:calc(100% - 0px)">
                <div class="co-card-header">
                    <span class="icon-search icon-small" aria-hidden="true"></span>
                    <?php echo Text::_('COM_CLUBORGANISATION_DASHBOARD_DATACHECK_TITLE'); ?>
                    <?php if ($datacheckTotal > 0) : ?>
                        <a href="index.php?option=com_cluborganisation&view=datacheck"
                           class="btn btn-sm btn-outline-light ms-auto" style="font-size:.75rem;padding:.15rem .6rem;">
                            <?php echo Text::_('COM_CLUBORGANISATION_DASHBOARD_DATACHECK_LINK'); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="co-card-body">
                    <?php foreach ($datacheckItems as $key => $label) :
                        $count = $this->datacheckSummary[$key] ?? 0;
                    ?>
                    <div class="co-dc-item">
                        <span style="font-size:.9rem;"><?php echo $label; ?></span>
                        <span class="co-dc-count <?php echo $count > 0 ? 'has-issues' : 'no-issues'; ?>">
                            <?php echo $count > 0 ? $count : '✓'; ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- ── Zeile 3: Jubiläen im laufenden Monat ─────────────────────────── -->
    <?php if (!empty($this->anniversariesThisMonth)) : ?>
    <div class="co-card">
        <div class="co-card-header">
            <span class="icon-star icon-small" aria-hidden="true"></span>
            <?php echo Text::sprintf('COM_CLUBORGANISATION_DASHBOARD_ANNIVERSARIES_THIS_MONTH', $monthDE, $currentYear); ?>
        </div>
        <div class="co-card-body" style="padding-top:.5rem;padding-bottom:.5rem;">
            <table class="table co-dash-table">
                <thead>
                    <tr>
                        <th><?php echo Text::_('COM_CLUBORGANISATION_STATS_COL_NAME'); ?></th>
                        <th class="text-center"><?php echo Text::_('COM_CLUBORGANISATION_STATS_COL_ENTRY_YEAR'); ?></th>
                        <th class="text-center"><?php echo Text::_('COM_CLUBORGANISATION_STATS_COL_ANNIVERSARY'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($this->anniversariesThisMonth as $row) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['lastname'] . ', ' . $row['firstname'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="text-center"><?php echo $row['entry_year']; ?></td>
                        <td class="text-center">
                            <span class="co-jubilee-badge"><?php echo $row['years']; ?> <?php echo Text::_('COM_CLUBORGANISATION_STATS_YEARS'); ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

</div><!-- /.co-dashboard -->
