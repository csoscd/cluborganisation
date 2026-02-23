<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Api
 * @author      Christian Schulz
 * @license     GNU General Public License version 3 or later
 *
 * Service Provider für die Joomla REST-API Applikation.
 * Registriert MVC-Factory und Dispatcher für das api/-Verzeichnis.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use CSOSCD\Component\ClubOrganisation\Api\Extension\ClubOrganisationComponent;

return new class implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container->registerServiceProvider(new MVCFactory('\\CSOSCD\\Component\\ClubOrganisation'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\CSOSCD\\Component\\ClubOrganisation'));

        $container->set(
            ComponentInterface::class,
            static function (Container $container) {
                $component = new ClubOrganisationComponent(
                    $container->get(MVCFactoryInterface::class)
                );
                return $component;
            }
        );
    }
};
