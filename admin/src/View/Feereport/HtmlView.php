<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\View\Feereport;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

/**
 * View for Fee Report
 *
 * @since  1.7.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * Current year report data
     *
     * @var    array
     * @since  1.7.0
     */
    protected $currentYear;

    /**
     * Next year report data
     *
     * @var    array
     * @since  1.7.0
     */
    protected $nextYear;

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
        $model = $this->getModel();
        $this->currentYear = $model->getCurrentYearReport();
        $this->nextYear = $model->getNextYearReport();

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
        ToolbarHelper::title(Text::_('COM_CLUBORGANISATION_FEEREPORT'), 'chart');
    }
}
