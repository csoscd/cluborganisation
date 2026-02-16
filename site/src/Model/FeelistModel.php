<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Site
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;

/**
 * Feelist Model
 *
 * @since  1.7.0
 */
class FeelistModel extends BaseDatabaseModel
{
    /**
     * Get current fees for all membership types
     *
     * @return  array
     *
     * @since   1.7.0
     */
    public function getCurrentFees()
    {
        $db = $this->getDbo();
        $today = Factory::getDate()->toSql(false);
        
        // Hole alle Membership Types
        $query = $db->getQuery(true);
        $query->select('id, title')
            ->from($db->quoteName('#__cluborganisation_membershiptypes'))
            ->where($db->quoteName('published') . ' = 1')
            ->order($db->quoteName('title') . ' ASC');
        
        $db->setQuery($query);
        $membershiptypes = $db->loadObjectList();
        
        $result = [
            'current' => [],
            'future' => []
        ];
        
        foreach ($membershiptypes as $mt) {
            // Hole aktuell gültige Fee
            $currentFee = $this->getCurrentFeeForType($mt->id, $today);
            
            if ($currentFee) {
                $currentFee->membershiptype_title = $mt->title;
                $result['current'][] = $currentFee;
                
                // Hole zukünftige Fees
                $futureFees = $this->getFutureFees($mt->id, $today);
                if (!empty($futureFees)) {
                    $result['future'][$mt->id] = $futureFees;
                }
            }
        }
        
        return $result;
    }

    /**
     * Get current valid fee for a membership type
     *
     * @param   int     $membershiptypeId  The membership type ID
     * @param   string  $date              The reference date
     *
     * @return  object|null
     *
     * @since   1.7.0
     */
    protected function getCurrentFeeForType($membershiptypeId, $date)
    {
        $db = $this->getDbo();
        
        $query = $db->getQuery(true);
        $query->select('id, membershiptype_id, begin, amount')
            ->from($db->quoteName('#__cluborganisation_membershiptype_fees'))
            ->where($db->quoteName('membershiptype_id') . ' = ' . (int) $membershiptypeId)
            ->where($db->quoteName('begin') . ' <= ' . $db->quote($date))
            ->where($db->quoteName('published') . ' = 1')
            ->order($db->quoteName('begin') . ' DESC')
            ->setLimit(1);
        
        $db->setQuery($query);
        return $db->loadObject();
    }

    /**
     * Get future fees for a membership type
     *
     * @param   int     $membershiptypeId  The membership type ID
     * @param   string  $date              The reference date
     *
     * @return  array
     *
     * @since   1.7.0
     */
    protected function getFutureFees($membershiptypeId, $date)
    {
        $db = $this->getDbo();
        
        $query = $db->getQuery(true);
        $query->select('id, membershiptype_id, begin, amount')
            ->from($db->quoteName('#__cluborganisation_membershiptype_fees'))
            ->where($db->quoteName('membershiptype_id') . ' = ' . (int) $membershiptypeId)
            ->where($db->quoteName('begin') . ' > ' . $db->quote($date))
            ->where($db->quoteName('published') . ' = 1')
            ->order($db->quoteName('begin') . ' ASC');
        
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}
