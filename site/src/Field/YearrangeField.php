<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Site
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Site\Field;
defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;

class YearrangeField extends ListField
{
    protected $type = 'Yearrange';

    protected function getOptions()
    {
        $options = parent::getOptions();
        
        $currentYear = (int) date('Y');
        $startYear = $currentYear - 5;
        $endYear = $currentYear + 1;
        
        for ($year = $endYear; $year >= $startYear; $year--) {
            $options[] = (object) [
                'value' => $year,
                'text'  => $year
            ];
        }
        
        return $options;
    }
}
