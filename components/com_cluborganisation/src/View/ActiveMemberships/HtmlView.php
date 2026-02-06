<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_cluborganisation
 *
 * @copyright   Copyright (C) 2025 Christian Schulz
 * @license     GPL-2.0-or-later
 */

declare(strict_types=1);

namespace Joomla\Component\Cluborganisation\Site\View\ActiveMemberships;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * HTML view for active memberships.
 */
class HtmlView extends BaseHtmlView
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * Display the view.
     *
     * @param   string  $tpl  Template name.
     *
     * @return  void
     */
    public function display($tpl = null): void
    {
        $this->items = $this->get('Items');
        $params = $this->get('State')->get('params');
        $this->fields = (array) $params->get('active_fields', []);

        parent::display($tpl);
    }
}
