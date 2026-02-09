<?php
namespace CSOSCD\Component\ClubOrganisation\Administrator\Controller;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\AdminController;
class SalutationsController extends AdminController {
    public function getModel($name = 'Salutation', $prefix = 'Administrator', $config = ['ignore_request' => true]) {
        return parent::getModel($name, $prefix, $config);
    }
}
