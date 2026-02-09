<?php
namespace CSOSCD\Component\ClubOrganisation\Administrator\Controller;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\AdminController;
class MembershipbanksController extends AdminController {
    public function getModel($name = 'Membershipbank', $prefix = 'Administrator', $config = ['ignore_request' => true]) {
        return parent::getModel($name, $prefix, $config);
    }
}
