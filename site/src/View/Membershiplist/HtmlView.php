<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Site
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Site\View\Membershiplist;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;

class HtmlView extends BaseHtmlView
{
    protected $newMemberships;
    protected $endedMemberships;
    protected $yearOptions;
    protected $selectedYear;

    public function display($tpl = null)
    {
        $app = Factory::getApplication();
        $this->selectedYear = $app->input->getInt('year', date('Y'));
        
        $this->newMemberships = $this->get('NewMemberships');
        $this->endedMemberships = $this->get('EndedMemberships');
        $this->yearOptions = $this->get('YearOptions');

        parent::display($tpl);
    }
}
