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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

class BwpostmansyncModel extends BaseDatabaseModel
{
    /**
     * Check if BwPostman tables exist
     *
     * @return bool
     */
    public function isBwPostmanInstalled()
    {
        $db = $this->getDatabase();
        
        try {
            // Check if bwpostman_subscribers table exists
            $tables = $db->getTableList();
            $prefix = $db->getPrefix();
            
            $requiredTables = [
                $prefix . 'bwpostman_subscribers',
                $prefix . 'bwpostman_mailinglists',
                $prefix . 'bwpostman_subscribers_mailinglists'
            ];
            
            foreach ($requiredTables as $table) {
                if (!in_array($table, $tables)) {
                    return false;
                }
            }
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get available mailinglists from BwPostman
     *
     * @return array|false
     */
    public function getMailinglists()
    {
        $db = $this->getDatabase();
        
        $query = $db->getQuery(true)
            ->select([
                $db->quoteName('id'),
                $db->quoteName('title'),
                $db->quoteName('description'),
                $db->quoteName('published')
            ])
            ->from($db->quoteName('#__bwpostman_mailinglists'))
            ->where($db->quoteName('archive_flag') . ' = 0')
            ->order($db->quoteName('title') . ' ASC');
        
        $db->setQuery($query);
        
        try {
            return $db->loadObjectList();
        } catch (\RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * Get inactive members (no active membership but still in BwPostman)
     *
     * @return array|false
     */
    public function getInactiveMembers()
    {
        $db = $this->getDatabase();
        $today = Factory::getDate()->toSql();
        
        $query = $db->getQuery(true);
        
        // Subquery: Check if person has any active membership
        $activeSubquery = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__cluborganisation_memberships', 'm'))
            ->where('m.person_id = p.id')
            ->where('(m.end IS NULL OR m.end >= ' . $db->quote($today) . ')');
        
        // Main query
        $query->select([
                'p.id',
                'p.member_no',
                'p.firstname',
                'p.lastname',
                'p.email',
                's.id AS subscriber_id',
                's.name AS subscriber_name',
                's.firstname AS subscriber_firstname'
            ])
            ->from($db->quoteName('#__cluborganisation_persons', 'p'))
            ->innerJoin(
                $db->quoteName('#__bwpostman_subscribers', 's') .
                ' ON ' . $db->quoteName('s.special') . ' = ' . $db->quoteName('p.member_no')
            )
            ->where('(' . $activeSubquery . ') = 0')  // No active membership
            ->where($db->quoteName('s.archive_flag') . ' = 0')  // Not yet archived in BwPostman
            ->order($db->quoteName('p.lastname') . ', ' . $db->quoteName('p.firstname'));
        
        $db->setQuery($query);
        
        try {
            return $db->loadObjectList();
        } catch (\RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * Get active members (with active membership) that need sync
     *
     * @param int $mailinglistId
     * @return array|false
     */
    public function getActiveMembersForSync($mailinglistId)
    {
        $db = $this->getDatabase();
        $today = Factory::getDate()->toSql();
        
        $query = $db->getQuery(true);
        
        // Subquery: Check if person has active membership
        $activeSubquery = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__cluborganisation_memberships', 'm'))
            ->where('m.person_id = p.id')
            ->where('(m.end IS NULL OR m.end >= ' . $db->quote($today) . ')');
        
        // Subquery: Check if already connected to mailinglist
        $connectedSubquery = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__bwpostman_subscribers_mailinglists', 'sml'))
            ->where('sml.subscriber_id = s.id')
            ->where('sml.mailinglist_id = ' . (int)$mailinglistId);
        
        $query->select([
                'p.id',
                'p.member_no',
                'p.firstname',
                'p.lastname',
                'p.email',
                'p.salutation',
                'p.user_id',
                'sal.title AS salutation_title',
                's.id AS subscriber_id',
                's.status AS subscriber_status',
                's.archive_flag AS subscriber_archive_flag',
                'CASE WHEN s.id IS NULL THEN 1 ELSE 0 END AS needs_creation',
                'CASE WHEN s.archive_flag = 1 OR s.status = 0 THEN 1 ELSE 0 END AS needs_reactivation',
                'CASE WHEN s.id IS NOT NULL AND (' . $connectedSubquery . ') = 0 THEN 1 ELSE 0 END AS needs_connection',
                'CASE WHEN p.email IS NULL OR p.email = ' . $db->quote('') . ' THEN 1 ELSE 0 END AS missing_email'
            ])
            ->from($db->quoteName('#__cluborganisation_persons', 'p'))
            ->leftJoin(
                $db->quoteName('#__cluborganisation_salutations', 'sal') .
                ' ON ' . $db->quoteName('sal.id') . ' = ' . $db->quoteName('p.salutation')
            )
            ->leftJoin(
                $db->quoteName('#__bwpostman_subscribers', 's') .
                ' ON ' . $db->quoteName('s.special') . ' = ' . $db->quoteName('p.member_no')
            )
            ->where('(' . $activeSubquery . ') > 0')  // Has active membership
            ->having(
                '(needs_creation = 1 OR needs_reactivation = 1 OR needs_connection = 1)'
            )
            ->order($db->quoteName('p.lastname') . ', ' . $db->quoteName('p.firstname'));
        
        $db->setQuery($query);
        
        try {
            return $db->loadObjectList();
        } catch (\RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * Get all salutations for gender mapping
     *
     * @return array|false
     */
    public function getSalutations()
    {
        $db = $this->getDatabase();
        
        $query = $db->getQuery(true)
            ->select([
                $db->quoteName('id'),
                $db->quoteName('title')
            ])
            ->from($db->quoteName('#__cluborganisation_salutations'))
            ->order($db->quoteName('title') . ' ASC');
        
        $db->setQuery($query);
        
        try {
            return $db->loadObjectList();
        } catch (\RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * Archive inactive members in BwPostman
     *
     * @param array $personIds
     * @return array
     */
    public function archiveInactiveMembers($personIds)
    {
        if (empty($personIds)) {
            return [
                'success' => false,
                'message' => Text::_('COM_CLUBORGANISATION_BWPOSTMAN_ERROR_NO_SELECTION')
            ];
        }
        
        $db = $this->getDatabase();
        $user = Factory::getApplication()->getIdentity();
        $now = Factory::getDate()->toSql();
        
        try {
            $db->transactionStart();
            
            // Get member numbers for selected persons
            $query = $db->getQuery(true)
                ->select($db->quoteName('member_no'))
                ->from($db->quoteName('#__cluborganisation_persons'))
                ->where($db->quoteName('id') . ' IN (' . implode(',', $personIds) . ')');
            
            $db->setQuery($query);
            $memberNumbers = $db->loadColumn();
            
            if (empty($memberNumbers)) {
                throw new \Exception(Text::_('COM_CLUBORGANISATION_BWPOSTMAN_ERROR_NO_MEMBERS_FOUND'));
            }
            
            // Update subscribers: set archive_flag = 1
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__bwpostman_subscribers'))
                ->set($db->quoteName('archive_flag') . ' = 1')
                ->set($db->quoteName('archive_date') . ' = ' . $db->quote($now))
                ->set($db->quoteName('archived_by') . ' = ' . (int)$user->id)
                ->where($db->quoteName('special') . ' IN (' . implode(',', array_map([$db, 'quote'], $memberNumbers)) . ')');
            
            $db->setQuery($query);
            $db->execute();
            
            $affected = $db->getAffectedRows();
            
            $db->transactionCommit();
            
            return [
                'success' => true,
                'message' => Text::sprintf('COM_CLUBORGANISATION_BWPOSTMAN_SUCCESS_ARCHIVED', $affected)
            ];
            
        } catch (\Exception $e) {
            $db->transactionRollback();
            return [
                'success' => false,
                'message' => Text::sprintf('COM_CLUBORGANISATION_BWPOSTMAN_ERROR_ARCHIVE_FAILED', $e->getMessage())
            ];
        }
    }

    /**
     * Sync active members to BwPostman
     *
     * @param array $personIds
     * @param int $mailinglistId
     * @param array $genderMapping
     * @return array
     */
    public function syncActiveMembers($personIds, $mailinglistId, $genderMapping)
    {
        if (empty($personIds) || $mailinglistId <= 0) {
            return [
                'success' => false,
                'message' => Text::_('COM_CLUBORGANISATION_BWPOSTMAN_ERROR_INVALID_INPUT')
            ];
        }
        
        $db = $this->getDatabase();
        $user = Factory::getApplication()->getIdentity();
        $now = Factory::getDate()->toSql();
        
        try {
            $db->transactionStart();
            
            // Get persons data
            $query = $db->getQuery(true)
                ->select([
                    'p.id',
                    'p.member_no',
                    'p.firstname',
                    'p.lastname',
                    'p.email',
                    'p.salutation',
                    'p.user_id',
                    's.id AS subscriber_id'
                ])
                ->from($db->quoteName('#__cluborganisation_persons', 'p'))
                ->leftJoin(
                    $db->quoteName('#__bwpostman_subscribers', 's') .
                    ' ON ' . $db->quoteName('s.special') . ' = ' . $db->quoteName('p.member_no')
                )
                ->where($db->quoteName('p.id') . ' IN (' . implode(',', $personIds) . ')');
            
            $db->setQuery($query);
            $persons = $db->loadObjectList();
            
            $created = 0;
            $updated = 0;
            $connected = 0;
            
            foreach ($persons as $person) {
                // Map gender from salutation
                $gender = isset($genderMapping[$person->salutation]) 
                    ? (int)$genderMapping[$person->salutation] 
                    : 2; // Default: not specified
                
                if ($person->subscriber_id) {
                    // Update existing subscriber
                    $query = $db->getQuery(true)
                        ->update($db->quoteName('#__bwpostman_subscribers'))
                        ->set($db->quoteName('user_id') . ' = ' . (int)$person->user_id)
                        ->set($db->quoteName('firstname') . ' = ' . $db->quote($person->firstname))
                        ->set($db->quoteName('name') . ' = ' . $db->quote($person->lastname))
                        ->set($db->quoteName('email') . ' = ' . $db->quote($person->email))
                        ->set($db->quoteName('gender') . ' = ' . $gender)
                        ->set($db->quoteName('status') . ' = 1')
                        ->set($db->quoteName('archive_flag') . ' = 0')
                        ->set($db->quoteName('archived_by') . ' = -1')
                        ->where($db->quoteName('id') . ' = ' . (int)$person->subscriber_id);
                    
                    $db->setQuery($query);
                    $db->execute();
                    $updated++;
                    
                    $subscriberId = $person->subscriber_id;
                } else {
                    // Create new subscriber
                    $query = $db->getQuery(true)
                        ->insert($db->quoteName('#__bwpostman_subscribers'))
                        ->columns([
                            $db->quoteName('user_id'),
                            $db->quoteName('firstname'),
                            $db->quoteName('name'),
                            $db->quoteName('email'),
                            $db->quoteName('emailformat'),
                            $db->quoteName('gender'),
                            $db->quoteName('special'),
                            $db->quoteName('status'),
                            $db->quoteName('registration_date'),
                            $db->quoteName('registered_by'),
                            $db->quoteName('registration_ip'),
                            $db->quoteName('confirmation_date'),
                            $db->quoteName('confirmed_by'),
                            $db->quoteName('confirmation_ip'),
                            $db->quoteName('archive_flag'),
                            $db->quoteName('archived_by')
                        ])
                        ->values(
                            (int)$person->user_id . ', ' .
                            $db->quote($person->firstname) . ', ' .
                            $db->quote($person->lastname) . ', ' .
                            $db->quote($person->email) . ', ' .
                            '1, ' .  // emailformat: always HTML
                            $gender . ', ' .
                            $db->quote($person->member_no) . ', ' .
                            '1, ' .  // status: confirmed
                            $db->quote($now) . ', ' .
                            (int)$user->id . ', ' .
                            $db->quote('0.0.0.0') . ', ' .
                            $db->quote($now) . ', ' .
                            (int)$user->id . ', ' .
                            $db->quote('0.0.0.0') . ', ' .
                            '0, ' .  // archive_flag
                            '-1'     // archived_by
                        );
                    
                    $db->setQuery($query);
                    $db->execute();
                    $subscriberId = $db->insertid();
                    $created++;
                }
                
                // Check if connection to mailinglist exists
                $query = $db->getQuery(true)
                    ->select('COUNT(*)')
                    ->from($db->quoteName('#__bwpostman_subscribers_mailinglists'))
                    ->where($db->quoteName('subscriber_id') . ' = ' . (int)$subscriberId)
                    ->where($db->quoteName('mailinglist_id') . ' = ' . (int)$mailinglistId);
                
                $db->setQuery($query);
                $exists = $db->loadResult();
                
                if (!$exists) {
                    // Create connection to mailinglist
                    $query = $db->getQuery(true)
                        ->insert($db->quoteName('#__bwpostman_subscribers_mailinglists'))
                        ->columns([
                            $db->quoteName('subscriber_id'),
                            $db->quoteName('mailinglist_id')
                        ])
                        ->values((int)$subscriberId . ', ' . (int)$mailinglistId);
                    
                    $db->setQuery($query);
                    $db->execute();
                    $connected++;
                }
            }
            
            $db->transactionCommit();
            
            $message = Text::sprintf(
                'COM_CLUBORGANISATION_BWPOSTMAN_SUCCESS_SYNCED',
                $created,
                $updated,
                $connected
            );
            
            return [
                'success' => true,
                'message' => $message
            ];
            
        } catch (\Exception $e) {
            $db->transactionRollback();
            return [
                'success' => false,
                'message' => Text::sprintf('COM_CLUBORGANISATION_BWPOSTMAN_ERROR_SYNC_FAILED', $e->getMessage())
            ];
        }
    }

    /**
     * Get mailinglist by ID
     *
     * @param int $id
     * @return object|false
     */
    public function getMailinglist($id)
    {
        $db = $this->getDatabase();
        
        $query = $db->getQuery(true)
            ->select([
                $db->quoteName('id'),
                $db->quoteName('title'),
                $db->quoteName('description')
            ])
            ->from($db->quoteName('#__bwpostman_mailinglists'))
            ->where($db->quoteName('id') . ' = ' . (int)$id);
        
        $db->setQuery($query);
        
        try {
            return $db->loadObject();
        } catch (\RuntimeException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }
}
