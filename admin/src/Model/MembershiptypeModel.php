<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Model;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\AdminModel;

class MembershiptypeModel extends AdminModel
{
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_cluborganisation.membershiptype',
            'membershiptype',
            ['control' => 'jform', 'load_data' => $loadData]
        );
        return $form ?: false;
    }

    protected function loadFormData()
    {
        return $this->getItem();
    }

    public function getTable($type = 'Membershiptype', $prefix = 'CSOSCD\\Component\\ClubOrganisation\\Administrator\\Table\\', $config = [])
    {
        return parent::getTable($type, $prefix, $config);
    }
}
