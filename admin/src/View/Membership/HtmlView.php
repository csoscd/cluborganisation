<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\View\Membership;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

class HtmlView extends BaseHtmlView
{
    protected $form;
    protected $item;
    protected $state;
    protected $membershipTypes;
    protected $dependentMembershipsCount = 0;

    public function display($tpl = null)
    {
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->state = $this->get('State');

        // Load membership type dependency info for JS
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select(['id', 'is_dependent', 'depends_on_type'])
            ->from($db->quoteName('#__cluborganisation_membershiptypes'))
            ->where($db->quoteName('published') . ' = 1');
        $db->setQuery($query);
        $this->membershipTypes = $db->loadObjectList();

        // Determine which types are "parent types" (i.e. other types depend on them)
        $query = $db->getQuery(true)
            ->select('DISTINCT ' . $db->quoteName('depends_on_type'))
            ->from($db->quoteName('#__cluborganisation_membershiptypes'))
            ->where($db->quoteName('is_dependent') . ' = 1')
            ->where($db->quoteName('depends_on_type') . ' IS NOT NULL')
            ->where($db->quoteName('published') . ' = 1');
        $db->setQuery($query);
        $parentTypeIds = array_map('intval', $db->loadColumn());

        foreach ($this->membershipTypes as $mt) {
            $mt->has_dependents = in_array((int) $mt->id, $parentTypeIds, true);
        }

        // For existing items: count how many memberships depend on this one
        if (!empty($this->item->id)) {
            $query = $db->getQuery(true)
                ->select('COUNT(*)')
                ->from($db->quoteName('#__cluborganisation_memberships'))
                ->where($db->quoteName('depends_on_membership_id') . ' = ' . (int) $this->item->id);
            $db->setQuery($query);
            $this->dependentMembershipsCount = (int) $db->loadResult();
        }

        // JS-Strings für Joomla.Text._() registrieren
        Text::script('COM_CLUBORGANISATION_ONGOING');
        Text::script('COM_CLUBORGANISATION_SELECT_PARENT_MEMBERSHIP');

        if (!$this->form) {
            throw new \Exception('Form not found for Membership', 500);
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
        $isNew = ($this->item->id == 0);

        ToolbarHelper::title(
            Text::_('COM_CLUBORGANISATION_MEMBERSHIP') . ': ' . 
            ($isNew ? Text::_('JNEW') : Text::_('JEDIT')),
            'list'
        );

        ToolbarHelper::apply('membership.apply');
        ToolbarHelper::save('membership.save');
        ToolbarHelper::cancel('membership.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }
}
