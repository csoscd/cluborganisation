<?php
namespace CSOSCD\Component\ClubOrganisation\Administrator\View\Dsgvocleanup;
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $yearsThreshold;

    public function display($tpl = null)
    {
        $model = $this->getModel();
        
        $this->items = $model->getItems();
        $this->yearsThreshold = $model->getYearsThreshold();

        // Check for errors
        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar()
    {
        ToolbarHelper::title(Text::_('COM_CLUBORGANISATION_DSGVO_CLEANUP_TITLE'), 'shield');
        ToolbarHelper::custom('dsgvocleanup.anonymize', 'delete', 'delete', 'COM_CLUBORGANISATION_DSGVO_ANONYMIZE_BUTTON', true);
        ToolbarHelper::cancel('dsgvocleanup.cancel', 'JTOOLBAR_CLOSE');
    }
}
