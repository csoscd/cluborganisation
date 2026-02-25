<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\View\Dashboard;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View for Dashboard
 *
 * @since  2.2.0
 */
class HtmlView extends BaseHtmlView
{
    /** @var string  Installierte Version */
    protected $installedVersion;

    /** @var string|null  Verfügbares Update der Komponente */
    protected $availableUpdate;

    /** @var int  Aktive Personen */
    protected $activePersons;

    /** @var int  Aktive Mitgliedschaften */
    protected $activeMemberships;

    /** @var int  Mitgliedschaften, die im aktuellen Jahr enden (ohne Folgemitgliedschaft) */
    protected $endingThisYear;

    /** @var int  Neue Mitglieder im laufenden Monat */
    protected $newThisMonth;

    /** @var array  Jubiläen im laufenden Monat */
    protected $anniversariesThisMonth;

    /** @var array  Datacheck-Zusammenfassung */
    protected $datacheckSummary;

    /** @var array  Status der ClubOrganisation-Erweiterungen */
    protected $extensions;

    public function display($tpl = null)
    {
        /** @var \CSOSCD\Component\ClubOrganisation\Administrator\Model\DashboardModel $model */
        $model = $this->getModel();

        $this->installedVersion       = $model->getInstalledVersion();
        $this->availableUpdate        = $model->getAvailableUpdate();
        $this->activePersons          = $model->getActivePersonsCount();
        $this->activeMemberships      = $model->getActiveMembershipsCount();
        $this->endingThisYear         = $model->getMembershipsEndingThisYear();
        $this->newThisMonth           = $model->getNewMembersThisMonth();
        $this->anniversariesThisMonth = $model->getAnniversariesThisMonth();
        $this->datacheckSummary       = $model->getDatacheckSummary();
        $this->extensions             = $model->getExtensionsStatus();

        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        ToolbarHelper::title(Text::_('COM_CLUBORGANISATION_DASHBOARD'), 'home');
        ToolbarHelper::preferences('com_cluborganisation');
    }
}
