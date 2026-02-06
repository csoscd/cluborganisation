<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cluborganisation
 *
 * @copyright   Copyright (C) 2025 Christian Schulz
 * @license     GPL-2.0-or-later
 */

declare(strict_types=1);

use Joomla\CMS\Extension\ExtensionHelper;
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

$container = Factory::getContainer();
$component = ExtensionHelper::getComponent('com_cluborganisation');
$dispatcher = $component->getDispatcher($container);
$dispatcher->dispatch();
