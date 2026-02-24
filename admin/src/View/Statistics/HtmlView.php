<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\View\Statistics;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View for Statistics
 *
 * @since  2.1.0
 */
class HtmlView extends BaseHtmlView
{
    protected $lastYear;
    protected $currentYear;
    protected $yearlyDevelopment;
    protected $memberStructure;
    protected $ageStructure;
    protected $durationStructure;
    protected $avgAgeCurrent;
    protected $avgAgePrev;
    protected $avgDurationCurrent;
    protected $avgDurationPrev;
    protected $joinsPerYear;
    protected $leavesPerYear;
    protected $anniversariesCurrent;
    protected $anniversariesNext;
    protected $startYear;
    protected $currentYearNum;

    public function display($tpl = null)
    {
        /** @var \CSOSCD\Component\ClubOrganisation\Administrator\Model\StatisticsModel $model */
        $model = $this->getModel();

        $this->currentYearNum    = (int) date('Y');
        $this->startYear         = $model->getStatisticsStartYear();

        $this->lastYear          = $model->getMemberDevelopmentForYear($this->currentYearNum - 1);
        $this->currentYear       = $model->getMemberDevelopmentForYear($this->currentYearNum);
        $this->yearlyDevelopment = $model->getMemberDevelopmentByYear($this->startYear);
        $this->memberStructure   = $model->getMemberStructure();
        $this->ageStructure      = $model->getAgeStructure();
        $this->durationStructure = $model->getMembershipDuration();
        $this->joinsPerYear      = $model->getMemberJoinsPerYear($this->startYear);
        $this->leavesPerYear     = $model->getMemberLeavesPerYear($this->startYear);

        $this->anniversariesCurrent = $model->getMemberAnniversaries($this->currentYearNum, true);
        $this->anniversariesNext    = $model->getMemberAnniversaries($this->currentYearNum + 1, true);

        $today   = date('Y-m-d');
        $refPrev = ($this->currentYearNum - 1) . '-12-31';

        $this->avgAgeCurrent      = $model->getAverageAgeAtDate($today);
        $this->avgAgePrev         = $model->getAverageAgeAtDate($refPrev);
        $this->avgDurationCurrent = $model->getAverageDurationAtDate($today);
        $this->avgDurationPrev    = $model->getAverageDurationAtDate($refPrev);

        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        // ── Chart-Daten aufbereiten ───────────────────────────────────────
        $lyLabels = $lyCounts = $cyLabels = $cyCounts = [];
        foreach ($this->lastYear as $row) {
            $lyLabels[] = $row['label'];
            $lyCounts[] = $row['count'];
        }
        foreach ($this->currentYear as $row) {
            $cyLabels[] = $row['label'];
            $cyCounts[] = $row['count'];
        }

        $yrLabels = $yrCounts = [];
        foreach ($this->yearlyDevelopment as $row) {
            $yrLabels[] = (string) $row['year'];
            $yrCounts[] = (int) $row['count'];
        }

        $structLabels = $structCurrent = $structPrev = [];
        foreach ($this->memberStructure as $row) {
            $structLabels[]  = $row['type_title'];
            $structCurrent[] = (int) $row['current'];
            $structPrev[]    = (int) $row['prev'];
        }

        // Fluktuation
        $flucLabels = $flucJoins = $flucLeaves = $netLabels = $netValues = $netColors = [];
        $joinsByYear  = [];
        $leavesByYear = [];
        foreach ($this->joinsPerYear as $row) {
            $joinsByYear[(int)$row['year']] = (int)$row['count'];
        }
        foreach ($this->leavesPerYear as $row) {
            $leavesByYear[(int)$row['year']] = (int)$row['count'];
        }
        foreach ($this->joinsPerYear as $row) {
            $y          = (int) $row['year'];
            $flucLabels[] = (string) $y;
            $flucJoins[]  = (int) $row['count'];
            $flucLeaves[] = $leavesByYear[$y] ?? 0;
            $net          = (int) $row['count'] - ($leavesByYear[$y] ?? 0);
            $netLabels[]  = (string) $y;
            $netValues[]  = $net;
            $netColors[]  = $net >= 0 ? '#f29838' : '#132d6a';
        }

        $doc = Factory::getApplication()->getDocument();
        $doc->addScriptOptions('com_cluborganisation.statistics', [
            'prevYear'      => $this->currentYearNum - 1,
            'currentYear'   => $this->currentYearNum,
            'lyLabels'      => $lyLabels,
            'lyCounts'      => $lyCounts,
            'cyLabels'      => $cyLabels,
            'cyCounts'      => $cyCounts,
            'yrLabels'      => $yrLabels,
            'yrCounts'      => $yrCounts,
            'structLabels'  => $structLabels,
            'structCurrent' => $structCurrent,
            'structPrev'    => $structPrev,
            'flucLabels'    => $flucLabels,
            'flucJoins'     => $flucJoins,
            'flucLeaves'    => $flucLeaves,
            'netLabels'     => $netLabels,
            'netValues'     => $netValues,
            'netColors'     => $netColors,
            'labelMembers'  => Text::_('COM_CLUBORGANISATION_STATS_MEMBERS'),
            'labelJoins'    => Text::_('COM_CLUBORGANISATION_STATS_JOINS'),
            'labelLeaves'   => Text::_('COM_CLUBORGANISATION_STATS_LEAVES'),
            'labelNet'      => Text::_('COM_CLUBORGANISATION_STATS_NET_CHANGE'),
        ]);

        // Chart.js laden
        $wa = $doc->getWebAssetManager();
        try {
            $wa->useScript('chartjs');
        } catch (\Exception $e) {
            $wa->registerAndUseScript(
                'com_cluborganisation.chartjs.cdn',
                'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js',
                ['version' => '4.4.1']
            );
        }

        $doc->addScriptDeclaration($this->buildInitScript());

        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Erzeugt das Chart.js-Initialisierungs-JavaScript.
     *
     * @return  string
     *
     * @since   2.1.0
     */
    protected function buildInitScript(): string
    {
        return <<<'JS'
(function () {
    'use strict';

    function initCharts() {
        if (typeof Chart === 'undefined') {
            document.querySelectorAll('.co-chart-wrap').forEach(function (el) {
                el.innerHTML = '<p class="text-warning p-2">Chart.js nicht verfügbar.</p>';
            });
            return;
        }

        var opts = (typeof Joomla !== 'undefined' && typeof Joomla.getOptions === 'function')
            ? Joomla.getOptions('com_cluborganisation.statistics')
            : null;
        if (!opts) { return; }

        var COLOR_ORANGE = '#f29838';
        var COLOR_BLUE   = '#132d6a';

        function prepData(arr) {
            return (arr || []).map(function (v) {
                return (v === null || v === undefined || v === '') ? null : Number(v);
            });
        }
        function lineOpts(showLegend) {
            return {
                responsive: true, maintainAspectRatio: false, spanGaps: false,
                plugins: { legend: { display: !!showLegend, position: 'top' } },
                scales: { y: { beginAtZero: false, ticks: { precision: 0 } } }
            };
        }
        function barOpts(showLegend, beginAtZero) {
            return {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: !!showLegend, position: 'top' } },
                scales: { y: { beginAtZero: !!beginAtZero, ticks: { precision: 0 } } }
            };
        }

        var el;

        // 1. Monatsentwicklung letztes Jahr
        el = document.getElementById('coChartLastYear');
        if (el) new Chart(el, { type: 'line', data: { labels: opts.lyLabels, datasets: [{
            label: String(opts.prevYear), data: prepData(opts.lyCounts),
            borderColor: COLOR_BLUE, backgroundColor: COLOR_BLUE,
            fill: false, tension: 0.3, pointRadius: 4, pointHoverRadius: 6
        }]}, options: lineOpts(false) });

        // 2. Monatsentwicklung aktuelles Jahr
        el = document.getElementById('coChartCurrentYear');
        if (el) new Chart(el, { type: 'line', data: { labels: opts.cyLabels, datasets: [{
            label: String(opts.currentYear), data: prepData(opts.cyCounts),
            borderColor: COLOR_ORANGE, backgroundColor: COLOR_ORANGE,
            fill: false, tension: 0.3, pointRadius: 4, pointHoverRadius: 6
        }]}, options: lineOpts(false) });

        // 3. Jahresentwicklung
        el = document.getElementById('coChartYearly');
        if (el) new Chart(el, { type: 'bar', data: { labels: opts.yrLabels, datasets: [{
            label: opts.labelMembers, data: (opts.yrCounts || []).map(Number),
            backgroundColor: COLOR_ORANGE, borderColor: COLOR_ORANGE, borderWidth: 1
        }]}, options: barOpts(false, false) });

        // 4. Mitgliederstruktur
        el = document.getElementById('coChartStructure');
        if (el) new Chart(el, { type: 'bar', data: { labels: opts.structLabels, datasets: [
            { label: String(opts.currentYear), data: (opts.structCurrent || []).map(Number),
              backgroundColor: COLOR_ORANGE, borderColor: COLOR_ORANGE, borderWidth: 1 },
            { label: String(opts.prevYear), data: (opts.structPrev || []).map(Number),
              backgroundColor: COLOR_BLUE, borderColor: COLOR_BLUE, borderWidth: 1 }
        ]}, options: barOpts(true, false) });

        // 5. Fluktuation: Eintritte & Austritte
        el = document.getElementById('coChartFluctuation');
        if (el) new Chart(el, { type: 'line', data: { labels: opts.flucLabels, datasets: [
            { label: opts.labelJoins, data: (opts.flucJoins || []).map(Number),
              borderColor: COLOR_ORANGE, backgroundColor: COLOR_ORANGE,
              fill: false, tension: 0.3, pointRadius: 4, pointHoverRadius: 6 },
            { label: opts.labelLeaves, data: (opts.flucLeaves || []).map(Number),
              borderColor: COLOR_BLUE, backgroundColor: COLOR_BLUE,
              fill: false, tension: 0.3, pointRadius: 4, pointHoverRadius: 6 }
        ]}, options: lineOpts(true) });

        // 6. Netto-Veränderung
        el = document.getElementById('coChartNetChange');
        if (el) new Chart(el, { type: 'bar', data: { labels: opts.netLabels, datasets: [{
            label: opts.labelNet,
            data: (opts.netValues || []).map(Number),
            backgroundColor: opts.netColors || COLOR_ORANGE,
            borderColor: opts.netColors || COLOR_ORANGE,
            borderWidth: 1
        }]}, options: barOpts(false, true) });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCharts);
    } else {
        initCharts();
    }
}());
JS;
    }

    protected function addToolbar()
    {
        ToolbarHelper::title(Text::_('COM_CLUBORGANISATION_STATISTICS'), 'chart');
        ToolbarHelper::preferences('com_cluborganisation');
    }
}
