<?php
namespace CSOSCD\Component\ClubOrganisation\Site\View\Myprofile;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;

class HtmlView extends BaseHtmlView
{
    protected $myData;
    protected $myMemberships;

    public function display($tpl = null)
    {
        $user = Factory::getApplication()->getIdentity();
        if ($user->guest) {
            Factory::getApplication()->enqueueMessage('COM_CLUBORGANISATION_LOGIN_REQUIRED', 'warning');
            return;
        }

        $this->myData = $this->get('MyData');
        $this->myMemberships = $this->get('MyMemberships');

        if (!$this->myData) {
            Factory::getApplication()->enqueueMessage('COM_CLUBORGANISATION_NO_PROFILE_FOUND', 'warning');
            return;
        }

        parent::display($tpl);
    }
}
