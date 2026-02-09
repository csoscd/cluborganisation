<?php
namespace CSOSCD\Component\ClubOrganisation\Administrator\Model;
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

class MigrationModel extends BaseDatabaseModel
{
    /**
     * Check if all salutations and membership types exist
     * Returns mapping options for missing entries
     */
    public function checkPrerequisites()
    {
        $db = $this->getDatabase();
        $messages = [];

        // Check if source tables exist
        $tables = $db->getTableList();
        $prefix = $db->getPrefix();
        
        if (!in_array($prefix . 'nokCM_persons', $tables)) {
            return [
                'success' => false,
                'message' => Text::_('COM_CLUBORGANISATION_MIGRATION_ERROR_SOURCE_PERSONS_NOT_FOUND'),
                'missing_salutations' => [],
                'missing_types' => [],
                'available_salutations' => [],
                'available_types' => []
            ];
        }
        
        if (!in_array($prefix . 'nokCM_memberships', $tables)) {
            return [
                'success' => false,
                'message' => Text::_('COM_CLUBORGANISATION_MIGRATION_ERROR_SOURCE_MEMBERSHIPS_NOT_FOUND'),
                'missing_salutations' => [],
                'missing_types' => [],
                'available_salutations' => [],
                'available_types' => []
            ];
        }

        // Check salutations
        $query = $db->getQuery(true);
        $query->select('DISTINCT ' . $db->quoteName('salutation'))
            ->from($db->quoteName('#__nokCM_persons'))
            ->where($db->quoteName('salutation') . ' IS NOT NULL')
            ->where($db->quoteName('salutation') . ' != ' . $db->quote(''));
        
        $db->setQuery($query);
        $sourceSalutations = $db->loadColumn();

        // Get existing salutations with IDs
        $query = $db->getQuery(true);
        $query->select($db->quoteName(['id', 'title']))
            ->from($db->quoteName('#__cluborganisation_salutations'))
            ->order($db->quoteName('title') . ' ASC');
        
        $db->setQuery($query);
        $availableSalutations = $db->loadObjectList();

        $existingSalutationTitles = array_column($availableSalutations, 'title');
        $missingSalutations = array_diff($sourceSalutations, $existingSalutationTitles);

        if (!empty($missingSalutations)) {
            $messages[] = Text::sprintf(
                'COM_CLUBORGANISATION_MIGRATION_FOUND_UNMAPPED_SALUTATIONS',
                count($missingSalutations)
            );
        }

        // Check membership types
        $query = $db->getQuery(true);
        $query->select('DISTINCT ' . $db->quoteName('type'))
            ->from($db->quoteName('#__nokCM_memberships'))
            ->where($db->quoteName('type') . ' IS NOT NULL')
            ->where($db->quoteName('type') . ' != ' . $db->quote(''));
        
        $db->setQuery($query);
        $sourceTypes = $db->loadColumn();

        // Get existing types with IDs
        $query = $db->getQuery(true);
        $query->select($db->quoteName(['id', 'title']))
            ->from($db->quoteName('#__cluborganisation_membershiptypes'))
            ->order($db->quoteName('title') . ' ASC');
        
        $db->setQuery($query);
        $availableTypes = $db->loadObjectList();

        $existingTypeTitles = array_column($availableTypes, 'title');
        $missingTypes = array_diff($sourceTypes, $existingTypeTitles);

        if (!empty($missingTypes)) {
            $messages[] = Text::sprintf(
                'COM_CLUBORGANISATION_MIGRATION_FOUND_UNMAPPED_TYPES',
                count($missingTypes)
            );
        }

        // Always success, but provide mapping options
        if (empty($missingSalutations) && empty($missingTypes)) {
            $messages[] = Text::_('COM_CLUBORGANISATION_MIGRATION_CHECK_SUCCESS');
        } else {
            $messages[] = Text::_('COM_CLUBORGANISATION_MIGRATION_CHECK_MAPPING_REQUIRED');
        }

        return [
            'success' => true,
            'message' => implode('<br>', $messages),
            'missing_salutations' => array_values($missingSalutations),
            'missing_types' => array_values($missingTypes),
            'available_salutations' => $availableSalutations,
            'available_types' => $availableTypes
        ];
    }

    /**
     * Perform the actual migration
     */
    public function performMigration($truncate = false, $salutationMappings = [], $typeMappings = [])
    {
        $db = $this->getDatabase();
        
        try {
            // Start transaction
            $db->transactionStart();

            // Truncate tables if requested
            if ($truncate) {
                $db->truncateTable('#__cluborganisation_membershipbanks');
                $db->truncateTable('#__cluborganisation_memberships');
                $db->truncateTable('#__cluborganisation_persons');
            }

            // Create mapping for salutations (existing + custom mappings)
            $query = $db->getQuery(true);
            $query->select($db->quoteName(['id', 'title']))
                ->from($db->quoteName('#__cluborganisation_salutations'));
            $db->setQuery($query);
            $salutations = $db->loadObjectList();
            
            $salutationMap = [];
            foreach ($salutations as $sal) {
                $salutationMap[$sal->title] = $sal->id;
            }
            
            // Add custom mappings (override)
            foreach ($salutationMappings as $sourceTitle => $targetId) {
                $salutationMap[$sourceTitle] = $targetId;
            }

            // Create mapping for membership types (existing + custom mappings)
            $query = $db->getQuery(true);
            $query->select($db->quoteName(['id', 'title']))
                ->from($db->quoteName('#__cluborganisation_membershiptypes'));
            $db->setQuery($query);
            $types = $db->loadObjectList();
            
            $typeMap = [];
            foreach ($types as $type) {
                $typeMap[$type->title] = $type->id;
            }
            
            // Add custom mappings (override)
            foreach ($typeMappings as $sourceTitle => $targetId) {
                $typeMap[$sourceTitle] = $targetId;
            }

            // Migrate persons
            $query = $db->getQuery(true);
            $query->select('*')
                ->from($db->quoteName('#__nokCM_persons'));
            $db->setQuery($query);
            $sourcePersons = $db->loadObjectList();

            $personIdMap = [];
            $migratedPersons = 0;

            foreach ($sourcePersons as $person) {
                $salutationId = isset($salutationMap[$person->salutation]) ? $salutationMap[$person->salutation] : null;
                
                $query = $db->getQuery(true);
                $query->insert($db->quoteName('#__cluborganisation_persons'))
                    ->columns([
                        $db->quoteName('salutation'),
                        $db->quoteName('firstname'),
                        $db->quoteName('middlename'),
                        $db->quoteName('lastname'),
                        $db->quoteName('birthname'),
                        $db->quoteName('address'),
                        $db->quoteName('city'),
                        $db->quoteName('zip'),
                        $db->quoteName('country'),
                        $db->quoteName('telephone'),
                        $db->quoteName('mobile'),
                        $db->quoteName('email'),
                        $db->quoteName('user_id'),
                        $db->quoteName('image'),
                        $db->quoteName('birthday'),
                        $db->quoteName('deceased'),
                        $db->quoteName('member_no'),
                        $db->quoteName('created_by'),
                        $db->quoteName('created'),
                        $db->quoteName('modified_by'),
                        $db->quoteName('modified'),
                        $db->quoteName('active')
                    ])
                    ->values(implode(',', [
                        $salutationId ? $db->quote($salutationId) : 'NULL',
                        $db->quote($person->firstname),
                        $db->quote($person->middlename),
                        $db->quote($person->name),
                        $db->quote($person->birthname),
                        $db->quote($person->address),
                        $db->quote($person->city),
                        $db->quote($person->zip),
                        $db->quote($person->country),
                        $db->quote($person->telephone),
                        $db->quote($person->mobile),
                        $db->quote($person->email),
                        $person->user_id ? $db->quote($person->user_id) : 'NULL',
                        $db->quote($person->image),
                        $person->birthday ? $db->quote($person->birthday) : 'NULL',
                        $person->deceased ? $db->quote($person->deceased) : 'NULL',
                        $db->quote($person->custom1),
                        $db->quote($person->createdby),
                        $person->createddate ? $db->quote($person->createddate) : 'NULL',
                        $db->quote($person->modifiedby),
                        $person->modifieddate ? $db->quote($person->modifieddate) : 'NULL',
                        '1'
                    ]));
                
                $db->setQuery($query);
                $db->execute();
                
                $newPersonId = $db->insertid();
                $personIdMap[$person->id] = $newPersonId;
                $migratedPersons++;
            }

            // Migrate memberships
            $query = $db->getQuery(true);
            $query->select('*')
                ->from($db->quoteName('#__nokCM_memberships'));
            $db->setQuery($query);
            $sourceMemberships = $db->loadObjectList();

            $migratedMemberships = 0;

            foreach ($sourceMemberships as $membership) {
                if (!isset($personIdMap[$membership->person_id])) {
                    continue; // Skip if person wasn't migrated
                }
                
                $typeId = isset($typeMap[$membership->type]) ? $typeMap[$membership->type] : null;
                
                if (!$typeId) {
                    continue; // Skip if type doesn't exist
                }
                
                $newPersonId = $personIdMap[$membership->person_id];
                
                $query = $db->getQuery(true);
                $query->insert($db->quoteName('#__cluborganisation_memberships'))
                    ->columns([
                        $db->quoteName('person_id'),
                        $db->quoteName('type'),
                        $db->quoteName('begin'),
                        $db->quoteName('end'),
                        $db->quoteName('catid'),
                        $db->quoteName('created_by'),
                        $db->quoteName('created'),
                        $db->quoteName('modified_by'),
                        $db->quoteName('modified')
                    ])
                    ->values(implode(',', [
                        $db->quote($newPersonId),
                        $db->quote($typeId),
                        $db->quote($membership->begin),
                        $membership->end ? $db->quote($membership->end) : 'NULL',
                        $db->quote($membership->catid),
                        $db->quote($membership->createdby),
                        $membership->createddate ? $db->quote($membership->createddate) : 'NULL',
                        $db->quote($membership->modifiedby),
                        $membership->modifieddate ? $db->quote($membership->modifieddate) : 'NULL'
                    ]));
                
                $db->setQuery($query);
                $db->execute();
                $migratedMemberships++;
            }

            // Commit transaction
            $db->transactionCommit();

            return [
                'success' => true,
                'message' => Text::sprintf(
                    'COM_CLUBORGANISATION_MIGRATION_SUCCESS',
                    $migratedPersons,
                    $migratedMemberships
                ),
                'persons' => $migratedPersons,
                'memberships' => $migratedMemberships
            ];

        } catch (\Exception $e) {
            // Rollback on error
            $db->transactionRollback();
            
            return [
                'success' => false,
                'message' => Text::sprintf('COM_CLUBORGANISATION_MIGRATION_ERROR', $e->getMessage()),
                'persons' => 0,
                'memberships' => 0
            ];
        }
    }

    /**
     * Get statistics about source data
     */
    public function getSourceStatistics()
    {
        $db = $this->getDatabase();
        $stats = [];

        try {
            // Count persons
            $query = $db->getQuery(true);
            $query->select('COUNT(*)')
                ->from($db->quoteName('#__nokCM_persons'));
            $db->setQuery($query);
            $stats['persons'] = $db->loadResult();

            // Count memberships
            $query = $db->getQuery(true);
            $query->select('COUNT(*)')
                ->from($db->quoteName('#__nokCM_memberships'));
            $db->setQuery($query);
            $stats['memberships'] = $db->loadResult();

        } catch (\Exception $e) {
            $stats['persons'] = 0;
            $stats['memberships'] = 0;
        }

        return $stats;
    }

    /**
     * Get statistics about target data
     */
    public function getTargetStatistics()
    {
        $db = $this->getDatabase();
        $stats = [];

        try {
            // Count persons
            $query = $db->getQuery(true);
            $query->select('COUNT(*)')
                ->from($db->quoteName('#__cluborganisation_persons'));
            $db->setQuery($query);
            $stats['persons'] = $db->loadResult();

            // Count memberships
            $query = $db->getQuery(true);
            $query->select('COUNT(*)')
                ->from($db->quoteName('#__cluborganisation_memberships'));
            $db->setQuery($query);
            $stats['memberships'] = $db->loadResult();

        } catch (\Exception $e) {
            $stats['persons'] = 0;
            $stats['memberships'] = 0;
        }

        return $stats;
    }
}
