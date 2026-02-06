<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_cluborganisation
 *
 * @copyright   Copyright (C) 2025 Christian Schulz
 * @license     GPL-2.0-or-later
 */

declare(strict_types=1);

use Joomla\CMS\Factory;
use Joomla\CMS\Loader\JLoader;

defined('_JEXEC') or die;

$app = Factory::getApplication();
$component = $app->bootComponent('com_cluborganisation');

if (method_exists($component, 'dispatch')) {
    $component->dispatch($app->input);
    return;
}

if (method_exists($component, 'execute')) {
    $component->execute($app->input->getCmd('task'));
    $component->redirect();
    return;
}

$controller = $app->input->getCmd('controller', $app->input->getCmd('view', ''));
$task = $app->input->getCmd('task', $controller);

$namespaceRoot = __DIR__ . '/src';
JLoader::registerNamespace('Joomla\\Component\\Cluborganisation\\Site', $namespaceRoot);

$controllerClass = $controller ? ucfirst($controller) : 'Display';
$className = 'Joomla\\\\Component\\\\Cluborganisation\\\\Site\\\\Controller\\\\' . $controllerClass . 'Controller';

if (!class_exists($className)) {
    throw new RuntimeException('Controller not found');
}

$controllerObject = new $className();
$controllerObject->execute($task);
$controllerObject->redirect();
