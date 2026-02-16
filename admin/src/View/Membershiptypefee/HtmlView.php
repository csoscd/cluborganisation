<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\View\Membershiptypefee;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;

/**
 * View for Membership Type Fee Edit
 *
 * @since  1.7.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The Form object
     *
     * @var    Form
     * @since  1.7.0
     */
    protected $form;

    /**
     * The Item object
     *
     * @var    object
     * @since  1.7.0
     */
    protected $item;

    /**
     * The model state
     *
     * @var    object
     * @since  1.7.0
     */
    protected $state;

    /**
     * Display method
     *
     * @param   string  $tpl  The template name
     *
     * @return  void
     *
     * @since   1.7.0
     */
    public function display($tpl = null)
    {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->state = $this->get('State');

        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add toolbar
     *
     * @return  void
     *
     * @since   1.7.0
     */
    protected function addToolbar()
    {
        Factory::getApplication()->input->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);

        ToolbarHelper::title(
            Text::_('COM_CLUBORGANISATION_MEMBERSHIPTYPEFEE') . ': ' . 
            ($isNew ? Text::_('JNEW') : Text::_('JEDIT')),
            'coins'
        );

        ToolbarHelper::apply('membershiptypefee.apply');
        ToolbarHelper::save('membershiptypefee.save');

        if ($isNew) {
            ToolbarHelper::save2new('membershiptypefee.save2new');
        } else {
            ToolbarHelper::save2copy('membershiptypefee.save2copy');
        }

        ToolbarHelper::cancel('membershiptypefee.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}
