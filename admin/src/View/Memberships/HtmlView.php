<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\View\Memberships;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $pagination;
    protected $state;
    public $filterForm;
    public $activeFilters;

    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        
        // FilterForm laden - mit Fehlerbehandlung
        try {
            $this->filterForm = $this->get('FilterForm');
        } catch (\Exception $e) {
            // Falls FilterForm nicht geladen werden kann, auf null setzen
            $this->filterForm = null;
        }
        
        $this->activeFilters = $this->get('ActiveFilters');

        $errors = $this->get('Errors');
        if (!empty($errors) && count($errors)) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        ToolbarHelper::title(Text::_('COM_CLUBORGANISATION_MEMBERSHIPS'), 'list');
        ToolbarHelper::addNew('membership.add');
        ToolbarHelper::editList('membership.edit');
        ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'memberships.delete');
        ToolbarHelper::preferences('com_cluborganisation');
    }
}
