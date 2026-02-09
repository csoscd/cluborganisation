<?php
namespace CSOSCD\Component\ClubOrganisation\Site\View\Membermovements;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Text;

class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $params;

    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        
        // Get params from menu item or component
        $app = \Joomla\CMS\Factory::getApplication();
        $this->params = $app->getParams();

        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        parent::display($tpl);
    }
}
