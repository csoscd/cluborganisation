<?php
namespace CSOSCD\Component\ClubOrganisation\Administrator\View\Membership;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

class HtmlView extends BaseHtmlView
{
    protected $form;
    protected $item;
    protected $state;

    public function display($tpl = null)
    {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->state = $this->get('State');

        if (!$this->form) {
            throw new \Exception('Form not found for Membership', 500);
        }

        $errors = $this->get('Errors');
        if (!empty($errors) && count($errors)) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        Factory::getApplication()->input->set('hidemainmenu', true);
        $isNew = ($this->item->id == 0);

        ToolbarHelper::title(
            Text::_('COM_CLUBORGANISATION_MEMBERSHIP') . ': ' . 
            ($isNew ? Text::_('JNEW') : Text::_('JEDIT')),
            'list'
        );

        ToolbarHelper::apply('membership.apply');
        ToolbarHelper::save('membership.save');
        ToolbarHelper::cancel('membership.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}
