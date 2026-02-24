<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\View\Datacheck;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View for Datacheck
 *
 * @since  2.1.0
 */
class HtmlView extends BaseHtmlView
{
    /** @var array  Mitglieder ohne Geburtsdatum */
    protected $missingBirthday;

    /** @var array  Mitglieder ohne E-Mail */
    protected $missingEmail;

    /** @var array  Mitglieder ohne Mobilfunknummer */
    protected $missingMobile;

    /** @var array  Personen ohne jede Mitgliedschaft */
    protected $noMembership;

    /** @var array  Mitglieder ohne Joomla-User */
    protected $missingUser;

    public function display($tpl = null)
    {
        /** @var \CSOSCD\Component\ClubOrganisation\Administrator\Model\DatacheckModel $model */
        $model = $this->getModel();

        $this->missingBirthday = $model->getMissingBirthday();
        $this->missingEmail    = $model->getMissingEmail();
        $this->missingMobile   = $model->getMissingMobile();
        $this->noMembership    = $model->getNoMembership();
        $this->missingUser     = $model->getMissingUser();

        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        ToolbarHelper::title(Text::_('COM_CLUBORGANISATION_DATACHECK'), 'search');
    }
}
