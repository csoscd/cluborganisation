<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\View\Membershipbank;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use CSOSCD\Component\ClubOrganisation\Administrator\Helper\EncryptionHelper;

/**
 * View für das Bankverbindungs-Formular (Anlegen/Bearbeiten/Anzeigen)
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected $form;
    protected $item;
    protected $state;

    public function display($tpl = null)
    {
        if (!EncryptionHelper::hasEncryptionKey()) {
            $app = Factory::getApplication();
            $app->enqueueMessage(Text::_('COM_CLUBORGANISATION_MEMBERSHIPBANK_NO_KEY_INFO'), 'warning');
            $app->redirect(Route::_('index.php?option=com_cluborganisation&view=membershipbanks', false));
            return;
        }

        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        $this->state = $this->get('State');

        if (!$this->form) {
            throw new \Exception('Form not found for Membershipbank', 500);
        }

        $errors = $this->get('Errors');
        if (!empty($errors) && count($errors)) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        Factory::getApplication()->input->set('hidemainmenu', true);

        $isNew  = ($this->item->id == 0);
        $layout = $this->getLayout();

        if ($layout === 'view') {
            ToolbarHelper::title(
                Text::_('COM_CLUBORGANISATION_MEMBERSHIPBANK') . ': ' . Text::_('COM_CLUBORGANISATION_VIEW'),
                'lock'
            );
            ToolbarHelper::cancel('membershipbank.cancel', 'JTOOLBAR_CLOSE');
            return;
        }

        ToolbarHelper::title(
            Text::_('COM_CLUBORGANISATION_MEMBERSHIPBANK') . ': ' .
            ($isNew ? Text::_('JNEW') : Text::_('JEDIT')),
            'lock'
        );
        ToolbarHelper::apply('membershipbank.apply');
        ToolbarHelper::save('membershipbank.save');
        ToolbarHelper::cancel('membershipbank.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}
