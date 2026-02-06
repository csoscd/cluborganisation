<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cluborganisation
 *
 * @copyright   Copyright (C) 2025 Christian Schulz
 * @license     GPL-2.0-or-later
 */

declare(strict_types=1);

namespace Joomla\Component\Cluborganisation\Administrator\Service;

use Joomla\CMS\Component\ComponentInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Component\Cluborganisation\Administrator\Extension\CluborganisationComponent;

/**
 * Service provider for com_cluborganisation.
 */
class Provider implements ServiceProviderInterface
{
    /**
     * Registers the service provider.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     */
    public function register(Container $container): void
    {
        $container->registerServiceProvider(new MVCFactory('Joomla\\Component\\Cluborganisation'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('Joomla\\Component\\Cluborganisation'));
        $container->registerServiceProvider(new CategoryFactory('Joomla\\Component\\Cluborganisation'));

        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new CluborganisationComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                return $component;
            }
        );
    }
}
