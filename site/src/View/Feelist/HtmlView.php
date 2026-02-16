<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Site
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Site\View\Feelist;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;

/**
 * Feelist View
 *
 * @since  1.7.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The fees data
     *
     * @var    array
     * @since  1.7.0
     */
    protected $fees;

    /**
     * The page parameters
     *
     * @var    \Joomla\Registry\Registry
     * @since  1.7.0
     */
    protected $params;

    /**
     * The page class suffix
     *
     * @var    string
     * @since  1.7.0
     */
    protected $pageclass_sfx = '';

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
        $this->fees = $model->getCurrentFees();
        
        $app = Factory::getApplication();
        $this->params = $app->getParams();
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx', ''));

        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors), 500);
        }

        $this->prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepares the document
     *
     * @return  void
     *
     * @since   1.7.0
     */
    protected function prepareDocument()
    {
        $app = Factory::getApplication();
        $menus = $app->getMenu();
        $menu = $menus->getActive();

        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', \Joomla\CMS\Language\Text::_('COM_CLUBORGANISATION_FEELIST'));
        }

        $title = $this->params->get('page_title', '');

        if (empty($title)) {
            $title = $app->get('sitename');
        } elseif ($app->get('sitename_pagetitles', 0) == 1) {
            $title = \Joomla\CMS\Language\Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        } elseif ($app->get('sitename_pagetitles', 0) == 2) {
            $title = \Joomla\CMS\Language\Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        $this->document->setTitle($title);

        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }
    }
}
