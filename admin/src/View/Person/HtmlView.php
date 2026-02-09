<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @copyright   2026 Christian Schulz
 * @license     GNU General Public License version 2 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\View\Person;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;

/**
 * View-Klasse für das Person-Bearbeitungsformular
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * Das Formular-Objekt
     *
     * @var    Form
     * @since  1.0.0
     */
    protected $form;

    /**
     * Das Item-Objekt
     *
     * @var    object
     * @since  1.0.0
     */
    protected $item;

    /**
     * Der Model-State
     *
     * @var    object
     * @since  1.0.0
     */
    protected $state;

    /**
     * Display-Methode
     *
     * @param   string  $tpl  Template-Name
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function display($tpl = null)
    {
        // Hole Daten vom Model
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->state = $this->get('State');

        // Prüfe auf Fehler
        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        // Toolbar hinzufügen
        $this->addToolbar();

        // Template anzeigen
        parent::display($tpl);
    }

    /**
     * Toolbar hinzufügen
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function addToolbar()
    {
        Factory::getApplication()->input->set('hidemainmenu', true);

        $user = Factory::getApplication()->getIdentity();
        $isNew = ($this->item->id == 0);

        // Titel setzen
        ToolbarHelper::title(
            Text::_('COM_CLUBORGANISATION_PERSON') . ': ' . 
            ($isNew ? Text::_('JNEW') : Text::_('JEDIT')),
            'user'
        );

        // Buttons hinzufügen
        ToolbarHelper::apply('person.apply');
        ToolbarHelper::save('person.save');
        
        if ($isNew) {
            ToolbarHelper::save2new('person.save2new');
        } else {
            ToolbarHelper::save2copy('person.save2copy');
        }

        ToolbarHelper::cancel('person.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}
