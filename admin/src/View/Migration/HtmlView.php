<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\View\Migration;
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

class HtmlView extends BaseHtmlView
{
    protected $checkResult;
    protected $sourceStats;
    protected $targetStats;

    public function display($tpl = null)
    {
        $model = $this->getModel();
        
        // Get check result from user state
        $app = Factory::getApplication();
        $this->checkResult = $app->getUserState('com_cluborganisation.migration.check', null);
        
        // Get statistics
        $this->sourceStats = $model->getSourceStatistics();
        $this->targetStats = $model->getTargetStatistics();

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        ToolbarHelper::title(Text::_('COM_CLUBORGANISATION_MIGRATION_TITLE'), 'upload');
        ToolbarHelper::cancel('migration.cancel', 'JTOOLBAR_CLOSE');
    }
}
