<?php
namespace CSOSCD\Component\ClubOrganisation\Administrator\Controller;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\AdminController;

class MembershipsController extends AdminController
{
    protected $text_prefix = 'COM_CLUBORGANISATION_MEMBERSHIPS';

    public function getModel($name = 'Membership', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
}
