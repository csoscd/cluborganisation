<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cluborganisation
 *
 * @copyright   Copyright (C) 2025 Christian Schulz
 * @license     GPL-2.0-or-later
 */

declare(strict_types=1);

namespace Joomla\Component\Cluborganisation\Administrator\View\Persons;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * HTML view for Persons list.
 */
class HtmlView extends BaseHtmlView
{
    /**
     * @var  array
     */
    protected  = [];

    /**
     * @var  object
     */
    protected ;

    /**
     * @var  object
     */
    protected ;

    /**
     * Display the view.
     *
     * @param   string    Template name.
     *
     * @return  void
     */
    public function display( = null): void
    {
        ->items = ->get('Items');
        ->pagination = ->get('Pagination');
        ->state = ->get('State');

        parent::display();
    }
}
