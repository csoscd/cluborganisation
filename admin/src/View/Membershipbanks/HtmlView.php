<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\View\Membershipbanks;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use CSOSCD\Component\ClubOrganisation\Administrator\Helper\EncryptionHelper;

/**
 * View für die Bankverbindungs-Übersicht
 *
 * Ohne gültigen Schlüssel in der Session wird das Unlock-Template eingebunden.
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $pagination;
    protected $state;
    protected $hasRecords;

    public function display($tpl = null)
    {
        if (!EncryptionHelper::hasEncryptionKey()) {
            $model            = $this->getModel();
            $this->hasRecords = $model->hasRecords();
            $this->addUnlockToolbar();

            // Template direkt einbinden – den Joomla-Layout-Mechanismus umgehen,
            // da dieser 'default_unlock' konstruieren würde.
            $unlockTmpl = JPATH_COMPONENT_ADMINISTRATOR . '/tmpl/membershipbanks/unlock.php';
            if (!file_exists($unlockTmpl)) {
                throw new \RuntimeException('Unlock template not found: ' . $unlockTmpl, 500);
            }
            include $unlockTmpl;
            return;
        }

        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state      = $this->get('State');

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        ToolbarHelper::title(Text::_('COM_CLUBORGANISATION_MEMBERSHIPBANKS'), 'lock-open');
        ToolbarHelper::addNew('membershipbank.add');
        ToolbarHelper::custom('membershipbank.view', 'eye', 'eye', 'COM_CLUBORGANISATION_VIEW', true);
        ToolbarHelper::editList('membershipbank.edit');
        ToolbarHelper::deleteList('', 'membershipbanks.delete');
    }

    protected function addUnlockToolbar()
    {
        ToolbarHelper::title(Text::_('COM_CLUBORGANISATION_MEMBERSHIPBANKS'), 'lock');
    }
}
