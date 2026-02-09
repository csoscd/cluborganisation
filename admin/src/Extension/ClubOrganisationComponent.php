<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @copyright   2026 Christian Schulz
 * @license     GNU General Public License version 2 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Categories\CategoryInterface;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Psr\Container\ContainerInterface;

/**
 * Component-Klasse für ClubOrganisation
 *
 * @since  1.0.0
 */
class ClubOrganisationComponent extends MVCComponent implements 
    BootableExtensionInterface, 
    CategoryServiceInterface
{
    use CategoryServiceTrait;
    use HTMLRegistryAwareTrait;

    /**
     * Booting the extension. Wird beim Laden der Komponente ausgeführt.
     *
     * @param   ContainerInterface  $container  Der DI Container
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function boot(ContainerInterface $container)
    {
        // Hier können weitere Initialisierungen vorgenommen werden
    }

    /**
     * Gibt die Tabelle für Kategorien zurück
     *
     * @param   array   $options  Optionen für die Kategorieauswahl
     * @param   string  $section  Der Abschnitt der Komponente
     *
     * @return  CategoryInterface
     *
     * @since   1.0.0
     */
    public function getCategory(array $options = [], $section = ''): CategoryInterface
    {
        $options['table'] = '#__cluborganisation_memberships';
        $options['extension'] = 'com_cluborganisation';

        return parent::getCategory($options, $section);
    }

    /**
     * Gibt die verfügbaren Kategorien zurück
     *
     * @param   array   $options  Optionen für die Kategorieauswahl
     * @param   string  $section  Der Abschnitt der Komponente
     *
     * @return  CategoryInterface
     *
     * @since   1.0.0
     */
    public function getCategories(array $options = [], $section = ''): CategoryInterface
    {
        $options['table'] = '#__cluborganisation_memberships';
        $options['extension'] = 'com_cluborganisation';

        return parent::getCategories($options, $section);
    }
}
