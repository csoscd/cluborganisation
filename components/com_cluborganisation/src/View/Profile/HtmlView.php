<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_cluborganisation
 *
 * @copyright   Copyright (C) 2025 Christian Schulz
 * @license     GPL-2.0-or-later
 */

declare(strict_types=1);

namespace Joomla\Component\Cluborganisation\Site\View\Profile;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * HTML view for profile.
 */
class HtmlView extends BaseHtmlView
{
    /**
     * @var object|null
     */
    protected $person;

    /**
     * @var array
     */
    protected $memberships = [];

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
        $model = $this->getModel();
        $this->person = $model->getPerson();
        $this->memberships = $this->person ? $model->getMemberships((int) $this->person->id) : [];

        $params = $this->get('State')->get('params');
        $this->fields = (array) $params->get('profile_view_fields', []);

        parent::display($tpl);
    }
}
