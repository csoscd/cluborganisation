<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cluborganisation
 *
 * @copyright   Copyright (C) 2025 Christian Schulz
 * @license     GPL-2.0-or-later
 */

declare(strict_types=1);

namespace Joomla\Component\Cluborganisation\Administrator\Extension;

use Joomla\CMS\Extension\MvcComponent;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;

/**
 * Component class for com_cluborganisation.
 */
class CluborganisationComponent extends MvcComponent
{
    /**
     * Get the component's permissions.
     *
     * @return  \stdClass
     */
    public function getActions(): \stdClass
    {
        return ContentHelper::getActions('com_cluborganisation');
    }
}
