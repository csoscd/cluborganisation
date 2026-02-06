<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cluborganisation
 *
 * @copyright   Copyright (C) 2025 Christian Schulz
 * @license     GPL-2.0-or-later
 */

declare(strict_types=1);

use Joomla\CMS\Factory;

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

$controllerClass = $controller ? ucfirst($controller) : 'Display';
$className = 'Joomla\\\\Component\\\\Cluborganisation\\\\Administrator\\\\Controller\\\\' . $controllerClass . 'Controller';

if (!class_exists($className)) {
    $controllerPath = __DIR__ . '/src/Controller/' . $controllerClass . 'Controller.php';

    if (is_file($controllerPath)) {
        require_once $controllerPath;
    }
}

if (!class_exists($className)) {
    throw new RuntimeException('Controller not found');
}

$controllerObject = new $className();
$controllerObject->execute($task);
$controllerObject->redirect();
