<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\View\Salutations;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
class HtmlView extends BaseHtmlView {
    protected $items; protected $pagination; protected $state;
    public function display($tpl = null) {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->addToolbar();
        parent::display($tpl);
    }
    protected function addToolbar() {
        ToolbarHelper::title(Text::_('COM_CLUBORGANISATION_SALUTATIONS'), 'list');
        ToolbarHelper::addNew('salutation.add');
        ToolbarHelper::editList('salutation.edit');
        ToolbarHelper::deleteList('', 'salutations.delete');
    }
}
