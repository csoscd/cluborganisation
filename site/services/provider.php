<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Site
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use CSOSCD\Component\ClubOrganisation\Site\Extension\ClubOrganisationComponent;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        $container->registerServiceProvider(new MVCFactory('\\CSOSCD\\Component\\ClubOrganisation'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\CSOSCD\\Component\\ClubOrganisation'));

        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new ClubOrganisationComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                return $component;
            }
        );
    }
};
