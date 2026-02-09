<?php
namespace CSOSCD\Component\ClubOrganisation\Site\Controller;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\BaseController;

class DisplayController extends BaseController
{
    protected $default_view = 'activemembers';

    public function display($cachable = false, $urlparams = [])
    {
        return parent::display($cachable, $urlparams);
    }
}
