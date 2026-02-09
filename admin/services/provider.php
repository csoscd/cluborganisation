<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @copyright   2026 Christian Schulz
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use CSOSCD\Component\ClubOrganisation\Administrator\Extension\ClubOrganisationComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * Service Provider für die ClubOrganisation Komponente
 *
 * @since  1.0.0
 */
return new class implements ServiceProviderInterface {
    /**
     * Registriert die Service Provider
     *
     * @param   Container  $container  Der DI Container
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function register(Container $container): void
    {
        // Kategorie Factory registrieren
        $container->registerServiceProvider(new CategoryFactory('\\CSOSCD\\Component\\ClubOrganisation'));
        
        // MVC Factory registrieren
        $container->registerServiceProvider(new MVCFactory('\\CSOSCD\\Component\\ClubOrganisation'));
        
        // Component Dispatcher Factory registrieren
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\CSOSCD\\Component\\ClubOrganisation'));

        // Component Interface registrieren
        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new ClubOrganisationComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                $component->setRegistry($container->get(Registry::class));

                return $component;
            }
        );
    }
};
