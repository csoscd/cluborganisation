<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Model;
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

class DsgvocleanupModel extends ListModel
{
    /**
     * Constructor.
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     */
    protected function populateState($ordering = 'last_membership_end', $direction = 'ASC')
    {
        parent::populateState($ordering, $direction);
    }

    /**
     * Get persons with ended memberships older than configured years
     */
    public function getItems()
    {
        $db = $this->getDatabase();
        $params = ComponentHelper::getParams('com_cluborganisation');
        $yearsThreshold = (int) $params->get('dsgvo_years_threshold', 3);
        
        // Calculate cutoff date
        $cutoffDate = date('Y-m-d', strtotime("-{$yearsThreshold} years"));
        
        $query = $db->getQuery(true);
        
        // Subquery for last membership end date
        $subQuery = $db->getQuery(true);
        $subQuery->select('MAX(m2.end)')
            ->from($db->quoteName('#__cluborganisation_memberships', 'm2'))
            ->where('m2.person_id = p.id');
        
        // Subquery to check if person has any active membership
        $activeSubQuery = $db->getQuery(true);
        $activeSubQuery->select('COUNT(*)')
            ->from($db->quoteName('#__cluborganisation_memberships', 'm3'))
            ->where('m3.person_id = p.id')
            ->where('m3.end IS NULL');
        
        $query->select([
            'p.id',
            'p.firstname',
            'p.lastname',
            '(' . $subQuery . ') AS last_membership_end'
        ])
        ->from($db->quoteName('#__cluborganisation_persons', 'p'))
        ->where('(' . $subQuery . ') IS NOT NULL')  // Has at least one ended membership
        ->where('(' . $activeSubQuery . ') = 0')    // Has NO active memberships
        ->where('(' . $subQuery . ') < ' . $db->quote($cutoffDate))  // Last membership ended before cutoff
        ->where($db->quoteName('p.firstname') . ' != ' . $db->quote('Anonymisiert'))  // NOT already anonymized
        ->where($db->quoteName('p.email') . ' NOT LIKE ' . $db->quote('anonymisiert_%@deleted.local'))  // NOT already anonymized
        ->order('last_membership_end ASC');
        
        $db->setQuery($query);
        
        try {
            $items = $db->loadObjectList();
            return $items;
        } catch (\RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }
    
    /**
     * Anonymize selected persons
     */
    public function anonymizePersons($personIds)
    {
        if (empty($personIds)) {
            return [
                'success' => false,
                'message' => Text::_('COM_CLUBORGANISATION_DSGVO_ERROR_NO_SELECTION')
            ];
        }
        
        $db = $this->getDatabase();
        
        try {
            // Start transaction
            $db->transactionStart();
            
            $anonymizedCount = 0;
            $deletedBankAccounts = 0;
            
            foreach ($personIds as $personId) {
                $personId = (int) $personId;
                
                // Anonymize person data
                $query = $db->getQuery(true);
                $query->update($db->quoteName('#__cluborganisation_persons'))
                    ->set($db->quoteName('firstname') . ' = ' . $db->quote('Anonymisiert'))
                    ->set($db->quoteName('middlename') . ' = ' . $db->quote(''))
                    ->set($db->quoteName('lastname') . ' = ' . $db->quote('Person ' . $personId))
                    ->set($db->quoteName('birthname') . ' = ' . $db->quote(''))
                    ->set($db->quoteName('address') . ' = ' . $db->quote('Anonymisiert'))
                    ->set($db->quoteName('city') . ' = ' . $db->quote('Anonymisiert'))
                    ->set($db->quoteName('zip') . ' = ' . $db->quote('00000'))
                    ->set($db->quoteName('telephone') . ' = ' . $db->quote(''))
                    ->set($db->quoteName('mobile') . ' = ' . $db->quote(''))
                    ->set($db->quoteName('email') . ' = ' . $db->quote('anonymisiert_' . $personId . '@deleted.local'))
                    ->set($db->quoteName('birthday') . ' = ' . $db->quote('1970-01-01'))
                    ->set($db->quoteName('deceased') . ' = CASE WHEN ' . $db->quoteName('deceased') . ' IS NOT NULL THEN ' . $db->quote('1970-01-01') . ' ELSE NULL END')
                    ->set($db->quoteName('image') . ' = ' . $db->quote(''))
                    ->set($db->quoteName('active') . ' = 0')  // Set person as inactive
                    ->set($db->quoteName('modified') . ' = ' . $db->quote(date('Y-m-d H:i:s')))
                    ->set($db->quoteName('modified_by') . ' = ' . $db->quote(Factory::getApplication()->getIdentity()->id))
                    ->where($db->quoteName('id') . ' = ' . $db->quote($personId));
                
                $db->setQuery($query);
                $db->execute();
                $anonymizedCount++;
                
                // Delete bank accounts for this person's memberships
                // First, get all membership IDs for this person
                $query = $db->getQuery(true);
                $query->select('id')
                    ->from($db->quoteName('#__cluborganisation_memberships'))
                    ->where($db->quoteName('person_id') . ' = ' . $db->quote($personId));
                
                $db->setQuery($query);
                $membershipIds = $db->loadColumn();
                
                if (!empty($membershipIds)) {
                    // Delete bank accounts
                    $query = $db->getQuery(true);
                    $query->delete($db->quoteName('#__cluborganisation_membershipbanks'))
                        ->where($db->quoteName('membership_id') . ' IN (' . implode(',', array_map('intval', $membershipIds)) . ')');
                    
                    $db->setQuery($query);
                    $db->execute();
                    $deletedBankAccounts += $db->getAffectedRows();
                }
            }
            
            // Commit transaction
            $db->transactionCommit();
            
            return [
                'success' => true,
                'message' => Text::sprintf(
                    'COM_CLUBORGANISATION_DSGVO_SUCCESS',
                    $anonymizedCount,
                    $deletedBankAccounts
                )
            ];
            
        } catch (\Exception $e) {
            // Rollback on error
            $db->transactionRollback();
            
            return [
                'success' => false,
                'message' => Text::sprintf('COM_CLUBORGANISATION_DSGVO_ERROR', $e->getMessage())
            ];
        }
    }
    
    /**
     * Get the years threshold from configuration
     */
    public function getYearsThreshold()
    {
        $params = ComponentHelper::getParams('com_cluborganisation');
        return (int) $params->get('dsgvo_years_threshold', 3);
    }
}
