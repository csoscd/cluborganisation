<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cluborganisation
 *
 * @copyright   Copyright (C) 2025 Christian Schulz
 * @license     GPL-2.0-or-later
 */

declare(strict_types=1);

namespace Joomla\Component\Cluborganisation\Administrator\View\Person;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * HTML view for Person form.
 */
class HtmlView extends BaseHtmlView
{
    /**
     * @var  \JForm
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
        ->form = ->get('Form');
        ->item = ->get('Item');

        parent::display();
    }
}
