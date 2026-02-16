<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\View\Bwpostmansync;
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

class HtmlView extends BaseHtmlView
{
    protected $mailinglists;
    protected $items;
    protected $salutations;
    protected $memberType;
    protected $mailinglistId;
    protected $mailinglist;
    protected $bwPostmanInstalled;

    public function display($tpl = null)
    {
        $model = $this->getModel();
        $app = Factory::getApplication();
        $layout = $this->getLayout();
        
        // If no layout or 'default' layout, set to step1
        if ($layout === 'default' || empty($layout)) {
            $this->setLayout('step1');
            $layout = 'step1';
        }
        
        // Check if BwPostman is installed
        $this->bwPostmanInstalled = $model->isBwPostmanInstalled();
        
        // Prepare data based on layout
        switch ($layout) {
            case 'step1':
                if ($this->bwPostmanInstalled) {
                    $this->mailinglists = $model->getMailinglists();
                } else {
                    $this->mailinglists = [];
                }
                break;
                
            case 'step2_inactive':
                $this->memberType = 'inactive';
                $this->mailinglistId = $app->getUserState('com_cluborganisation.bwpostmansync.mailinglist_id', 0);
                $this->mailinglist = $model->getMailinglist($this->mailinglistId);
                $this->items = $model->getInactiveMembers();
                break;
                
            case 'step2_active':
                $this->memberType = 'active';
                $this->mailinglistId = $app->getUserState('com_cluborganisation.bwpostmansync.mailinglist_id', 0);
                $this->mailinglist = $model->getMailinglist($this->mailinglistId);
                $this->items = $model->getActiveMembersForSync($this->mailinglistId);
                $this->salutations = $model->getSalutations();
                break;
                
            case 'step3':
                // Success page
                break;
        }

        // Check for errors
        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->addToolbar($layout);

        parent::display($tpl);
    }

    protected function addToolbar($layout)
    {
        switch ($layout) {
            case 'step1':
                ToolbarHelper::title(Text::_('COM_CLUBORGANISATION_BWPOSTMAN_SYNC_TITLE') . ': ' . Text::_('COM_CLUBORGANISATION_BWPOSTMAN_STEP1_TITLE'), 'loop');
                ToolbarHelper::cancel('bwpostmansync.cancel', 'JTOOLBAR_CLOSE');
                break;
                
            case 'step2_inactive':
                ToolbarHelper::title(Text::_('COM_CLUBORGANISATION_BWPOSTMAN_SYNC_TITLE') . ': ' . Text::_('COM_CLUBORGANISATION_BWPOSTMAN_STEP2_INACTIVE_TITLE'), 'loop');
                ToolbarHelper::custom('bwpostmansync.sync', 'archive', 'archive', 'COM_CLUBORGANISATION_BWPOSTMAN_ARCHIVE_BUTTON', true);
                ToolbarHelper::cancel('bwpostmansync.cancel', 'JTOOLBAR_CANCEL');
                break;
                
            case 'step2_active':
                ToolbarHelper::title(Text::_('COM_CLUBORGANISATION_BWPOSTMAN_SYNC_TITLE') . ': ' . Text::_('COM_CLUBORGANISATION_BWPOSTMAN_STEP2_ACTIVE_TITLE'), 'loop');
                ToolbarHelper::custom('bwpostmansync.sync', 'upload', 'upload', 'COM_CLUBORGANISATION_BWPOSTMAN_SYNC_BUTTON', true);
                ToolbarHelper::cancel('bwpostmansync.cancel', 'JTOOLBAR_CANCEL');
                break;
                
            case 'step3':
                ToolbarHelper::title(Text::_('COM_CLUBORGANISATION_BWPOSTMAN_SYNC_TITLE') . ': ' . Text::_('COM_CLUBORGANISATION_BWPOSTMAN_STEP3_TITLE'), 'loop');
                ToolbarHelper::custom('bwpostmansync.cancel', 'back', 'back', 'COM_CLUBORGANISATION_BWPOSTMAN_BACK_TO_START', false);
                break;
        }
    }
}
