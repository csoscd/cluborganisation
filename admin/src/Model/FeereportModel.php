<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;

/**
 * Fee Report Model
 *
 * @since  1.7.0
 */
class FeereportModel extends BaseDatabaseModel
{
    /**
     * Get report for current year
     *
     * @return  array
     *
     * @since   1.7.0
     */
    public function getCurrentYearReport()
    {
        $year = date('Y');
        return $this->getYearReport($year);
    }

    /**
     * Get report for next year
     *
     * @return  array
     *
     * @since   1.7.0
     */
    public function getNextYearReport()
    {
        $year = date('Y') + 1;
        return $this->getYearReport($year);
    }

    /**
     * Get report for specific year
     *
     * @param   int  $year  The year
     *
     * @return  array
     *
     * @since   1.7.0
     */
    protected function getYearReport($year)
    {
        $db = $this->getDbo();
        
        // Zeitraum für das Jahr
        $yearStart = $year . '-01-01';
        $yearEnd = $year . '-12-31';
        
        // Hole alle Membership Types
        $query = $db->getQuery(true);
        $query->select('id, title')
            ->from($db->quoteName('#__cluborganisation_membershiptypes'))
            ->where($db->quoteName('published') . ' = 1')
            ->order($db->quoteName('title') . ' ASC');
        
        $db->setQuery($query);
        $membershiptypes = $db->loadObjectList();
        
        $items = [];
        $totalAmount = 0;
        $totalMemberships = 0;
        
        foreach ($membershiptypes as $mt) {
            // Hole gültige Fee für das Jahr
            $feeAmount = $this->getValidFeeForYear($mt->id, $year);
            
            // Zähle aktive Memberships im Jahr
            $membershipCount = $this->countMembershipsForYear($mt->id, $year);
            
            $totalForType = $membershipCount * $feeAmount;
            
            $items[] = (object) [
                'membershiptype_id' => $mt->id,
                'membershiptype_title' => $mt->title,
                'membership_count' => $membershipCount,
                'fee_amount' => $feeAmount,
                'total_amount' => $totalForType,
            ];
            
            $totalAmount += $totalForType;
            $totalMemberships += $membershipCount;
        }
        
        return [
            'items' => $items,
            'total_amount' => $totalAmount,
            'total_memberships' => $totalMemberships,
            'year' => $year,
        ];
    }

    /**
     * Get valid fee amount for a membership type in a specific year
     *
     * @param   int  $membershiptypeId  The membership type ID
     * @param   int  $year              The year
     *
     * @return  float
     *
     * @since   1.7.0
     */
    protected function getValidFeeForYear($membershiptypeId, $year)
    {
        $db = $this->getDbo();
        $yearStart = $year . '-01-01';
        
        $query = $db->getQuery(true);
        $query->select('amount')
            ->from($db->quoteName('#__cluborganisation_membershiptype_fees'))
            ->where($db->quoteName('membershiptype_id') . ' = ' . (int) $membershiptypeId)
            ->where($db->quoteName('begin') . ' <= ' . $db->quote($yearStart))
            ->where($db->quoteName('published') . ' = 1')
            ->order($db->quoteName('begin') . ' DESC')
            ->setLimit(1);
        
        $db->setQuery($query);
        $result = $db->loadResult();
        
        return $result ? (float) $result : 0.0;
    }

    /**
     * Count memberships for a membership type in a specific year
     *
     * @param   int  $membershiptypeId  The membership type ID
     * @param   int  $year              The year
     *
     * @return  int
     *
     * @since   1.7.0
     */
    protected function countMembershipsForYear($membershiptypeId, $year)
    {
        $db = $this->getDbo();
        $yearStart = $year . '-01-01';
        $yearEnd = $year . '-12-31';
        
        $query = $db->getQuery(true);
        $query->select('COUNT(*)')
            ->from($db->quoteName('#__cluborganisation_memberships'))
            ->where($db->quoteName('type') . ' = ' . (int) $membershiptypeId)
            ->where($db->quoteName('begin') . ' <= ' . $db->quote($yearEnd))
            ->where('(' . $db->quoteName('end') . ' IS NULL OR ' . 
                    $db->quoteName('end') . ' >= ' . $db->quote($yearStart) . ')');
        
        $db->setQuery($query);
        return (int) $db->loadResult();
    }
}
