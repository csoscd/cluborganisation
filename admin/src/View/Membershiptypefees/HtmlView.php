<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\View\Membershiptypefees;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;

/**
 * View for Membership Type Fees List
 *
 * @since  1.7.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The items
     *
     * @var    array
     * @since  1.7.0
     */
    public $items;

    /**
     * The pagination object
     *
     * @var    \Joomla\CMS\Pagination\Pagination
     * @since  1.7.0
     */
    public $pagination;

    /**
     * The model state
     *
     * @var    object
     * @since  1.7.0
     */
    public $state;

    /**
     * The filter form
     *
     * @var    \Joomla\CMS\Form\Form
     * @since  1.7.0
     */
    public $filterForm;

    /**
     * The active filters
     *
     * @var    array
     * @since  1.7.0
     */
    public $activeFilters;

    /**
     * Display method
     *
     * @param   string  $tpl  The template name
     *
     * @return  void
     *
     * @since   1.7.0
     */
    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add toolbar
     *
     * @return  void
     *
     * @since   1.7.0
     */
    protected function addToolbar()
    {
        $canDo = \Joomla\CMS\Helper\ContentHelper::getActions('com_cluborganisation');

        ToolbarHelper::title(Text::_('COM_CLUBORGANISATION_MEMBERSHIPTYPEFEES'), 'coins');

        if ($canDo->get('core.create')) {
            ToolbarHelper::addNew('membershiptypefee.add');
        }

        if ($canDo->get('core.edit.state')) {
            ToolbarHelper::publish('membershiptypefees.publish', 'JTOOLBAR_PUBLISH', true);
            ToolbarHelper::unpublish('membershiptypefees.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        }

        if ($canDo->get('core.delete')) {
            ToolbarHelper::deleteList('', 'membershiptypefees.delete');
        }

        if ($canDo->get('core.admin')) {
            ToolbarHelper::preferences('com_cluborganisation');
        }
    }
}
