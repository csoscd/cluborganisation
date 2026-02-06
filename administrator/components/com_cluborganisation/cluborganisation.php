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
$component->dispatch($app->input);
