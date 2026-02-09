<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @copyright   2026 Christian Schulz
 * @license     GNU General Public License version 2 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\View\Persons;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Pagination\Pagination;

/**
 * View-Klasse für die Personen-Liste
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * Die anzuzeigenden Items
     *
     * @var    array
     * @since  1.0.0
     */
    protected $items;

    /**
     * Das Pagination-Objekt
     *
     * @var    Pagination
     * @since  1.0.0
     */
    protected $pagination;

    /**
     * Der Model-State
     *
     * @var    object
     * @since  1.0.0
     */
    protected $state;

    /**
     * Die Filter-Form
     *
     * @var    \Joomla\CMS\Form\Form
     * @since  1.0.0
     */
    public $filterForm;

    /**
     * Die aktiven Filter
     *
     * @var    array
     * @since  1.0.0
     */
    public $activeFilters;

    /**
     * Display-Methode
     *
     * @param   string  $tpl  Template-Name
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function display($tpl = null)
    {
        // Hole Daten vom Model
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        // Prüfe auf Fehler
        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        // Toolbar hinzufügen
        $this->addToolbar();

        // Sidebar anzeigen
        $this->sidebar = \JHtmlSidebar::render();

        // Template anzeigen
        parent::display($tpl);
    }

    /**
     * Toolbar hinzufügen
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function addToolbar()
    {
        $user = Factory::getApplication()->getIdentity();

        // Titel setzen
        ToolbarHelper::title(Text::_('COM_CLUBORGANISATION_PERSONS'), 'users');

        // Buttons hinzufügen - vereinfachte Version ohne ACL
        ToolbarHelper::addNew('person.add');
        ToolbarHelper::editList('person.edit');
        ToolbarHelper::publish('persons.publish', 'JTOOLBAR_PUBLISH', true);
        ToolbarHelper::unpublish('persons.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'persons.delete');
        ToolbarHelper::preferences('com_cluborganisation');
    }
}
