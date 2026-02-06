<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_cluborganisation
 *
 * @copyright   Copyright (C) 2025 Christian Schulz
 * @license     GPL-2.0-or-later
 */

declare(strict_types=1);

namespace Joomla\Component\Cluborganisation\Site\View\Reports;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\Component\Cluborganisation\Site\Model\ReportsModel;

/**
 * HTML view for reports.
 */
class HtmlView extends BaseHtmlView
{
    /**
     * @var array
     */
    protected $begins = [];

    /**
     * @var array
     */
    protected $ends = [];

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var int
     */
    protected $year;

    /**
     * Display the view.
     *
     * @param   string  $tpl  Template name.
     *
     * @return  void
     */
    public function display($tpl = null): void
    {
        $params = $this->get('State')->get('params');
        $offset = $params->get('report_year', '');
        $this->year = (int) date('Y');
        if ($offset !== '') {
            $this->year += (int) $offset;
        }

        /** @var ReportsModel $model */
        $model = $this->getModel();
        $this->begins = $model->getBegins($this->year);
        $this->ends = $model->getEnds($this->year);
        $this->fields = (array) $params->get('report_fields', []);

        parent::display($tpl);
    }
}
