<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;

/**
 * Person Edit Model
 *
 * @since  1.0.0
 */
class PersonModel extends AdminModel
{
    /**
     * Der Typ-Alias
     *
     * @var    string
     * @since  1.0.0
     */
    public $typeAlias = 'com_cluborganisation.person';

    /**
     * Methode zum Abrufen des Formulars
     *
     * @param   array    $data      Daten für das Formular
     * @param   boolean  $loadData  True wenn Daten geladen werden sollen
     *
     * @return  Form|boolean  Form-Objekt bei Erfolg, false bei Fehler
     *
     * @since   1.0.0
     */
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_cluborganisation.person',
            'person',
            [
                'control' => 'jform',
                'load_data' => $loadData
            ]
        );

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Methode zum Laden der Formulardaten
     *
     * @return  mixed  Die Formulardaten
     *
     * @since   1.0.0
     */
    protected function loadFormData()
    {
        $app = Factory::getApplication();
        $data = $app->getUserState('com_cluborganisation.edit.person.data', []);

        if (empty($data)) {
            $data = $this->getItem();
            
            // Automatische Mitgliedsnummern-Vergabe für neue Personen
            if (empty($data->id)) {
                $memberNo = $this->generateMemberNumber();
                if ($memberNo !== null) {
                    $data->member_no = $memberNo;
                }
            }
        }

        return $data;
    }

    /**
     * Generiert die nächste Mitgliedsnummer basierend auf dem konfigurierten Pattern
     *
     * @return  string|null  Die generierte Mitgliedsnummer oder null wenn deaktiviert
     *
     * @since   1.2.0
     */
    protected function generateMemberNumber()
    {
        $params = \Joomla\CMS\Component\ComponentHelper::getParams('com_cluborganisation');
        
        // Prüfe ob automatische Vergabe aktiviert ist
        if (!$params->get('auto_generate_member_no', 0)) {
            return null;
        }
        
        $pattern = $params->get('member_no_pattern', '[No]');
        
        // Wenn kein [No] im Pattern, dann Pattern direkt zurückgeben
        if (strpos($pattern, '[No]') === false) {
            return $pattern;
        }
        
        // Extrahiere Präfix und Suffix aus dem Pattern
        $parts = explode('[No]', $pattern);
        $prefix = $parts[0] ?? '';
        $suffix = $parts[1] ?? '';
        
        // Finde die höchste existierende Nummer mit diesem Pattern
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        
        // Baue REGEX für MySQL - escaped für REGEXP
        $prefixEscaped = preg_quote($prefix, '/');
        $suffixEscaped = preg_quote($suffix, '/');
        $regexPattern = '^' . $prefixEscaped . '([0-9]+)' . $suffixEscaped . '$';
        
        $query->select('member_no')
            ->from($db->quoteName('#__cluborganisation_persons'))
            ->where($db->quoteName('member_no') . ' REGEXP ' . $db->quote($regexPattern))
            ->order($db->quoteName('member_no') . ' DESC');
        
        $db->setQuery($query);
        $existingNumbers = $db->loadColumn();
        
        $maxNumber = 0;
        
        // Extrahiere Nummern aus den gefundenen Mitgliedsnummern
        foreach ($existingNumbers as $memberNo) {
            // Entferne Präfix und Suffix und extrahiere die Nummer
            if (strlen($prefix) > 0) {
                $memberNo = substr($memberNo, strlen($prefix));
            }
            if (strlen($suffix) > 0 && strlen($memberNo) >= strlen($suffix)) {
                $memberNo = substr($memberNo, 0, -strlen($suffix));
            }
            
            // Prüfe ob es eine Nummer ist
            if (is_numeric($memberNo)) {
                $number = (int)$memberNo;
                if ($number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
        }
        
        // Generiere die nächste Nummer
        $nextNumber = $maxNumber + 1;
        
        // Baue die neue Mitgliedsnummer
        $newMemberNo = $prefix . $nextNumber . $suffix;
        
        return $newMemberNo;
    }

    /**
     * Methode zum Vorbereiten der gespeicherten Daten
     *
     * @param   \Joomla\CMS\Table\Table  $table  Table-Objekt
     *
     * @return  void
     *
     * @since   1.0.0
     */
    protected function prepareTable($table)
    {
        // Leere Datumswerte auf NULL setzen
        if (empty($table->deceased)) {
            $table->deceased = null;
        }
        
        // Leere Integer-Werte auf NULL setzen
        if (empty($table->user_id)) {
            $table->user_id = null;
        }
    }
    
    /**
     * Speichert die Person und erstellt optional einen Joomla-User
     *
     * @param   array  $data  Die Formulardaten
     *
     * @return  boolean  True bei Erfolg
     *
     * @since   1.3.0
     */
    public function save($data)
    {
        $app = Factory::getApplication();
        $createUser = isset($data['create_joomla_user']) && $data['create_joomla_user'];
        $userGroup = isset($data['joomla_user_group']) ? (int)$data['joomla_user_group'] : 2;
        
        // Entferne create_joomla_user und joomla_user_group aus den Daten (nicht in der DB)
        unset($data['create_joomla_user']);
        unset($data['joomla_user_group']);
        
        // Standard-Speichern
        if (!parent::save($data)) {
            return false;
        }
        
        // Wenn Joomla-User erstellt werden soll
        if ($createUser && empty($data['user_id'])) {
            $personId = $this->getState($this->getName() . '.id');
            $table = $this->getTable();
            
            if ($table->load($personId)) {
                $userId = $this->createJoomlaUser($table, $userGroup);
                
                if ($userId) {
                    // Speichere user_id in Person
                    $table->user_id = $userId;
                    $table->store();
                    
                    // Zeige Erfolgs-Nachricht mit generiertem Passwort
                    $password = $app->getUserState('com_cluborganisation.user.password', '');
                    $username = $app->getUserState('com_cluborganisation.user.username', '');
                    $app->enqueueMessage(
                        sprintf(\Joomla\CMS\Language\Text::_('COM_CLUBORGANISATION_USER_CREATED'), $username, $password),
                        'info'
                    );
                    
                    // Lösche Passwort aus Session
                    $app->setUserState('com_cluborganisation.user.password', null);
                    $app->setUserState('com_cluborganisation.user.username', null);
                }
            }
        }
        
        return true;
    }
    
    /**
     * Erstellt einen Joomla-User für die Person
     *
     * @param   \Joomla\CMS\Table\Table  $person     Person-Tabelle
     * @param   int                      $groupId    Joomla User Group ID
     *
     * @return  int|false  User-ID bei Erfolg, false bei Fehler
     *
     * @since   1.3.0
     */
    protected function createJoomlaUser($person, $groupId = 2)
    {
        $app = Factory::getApplication();
        
        try {
            // Generiere Username
            $username = $this->generateUsername($person->firstname, $person->lastname);
            
            // Generiere zufälliges 12-stelliges Passwort (Joomla-Standard)
            $password = $this->generatePassword(12);
            
            // Erstelle User-Daten
            $userData = [
                'name'       => trim($person->firstname . ' ' . $person->lastname),
                'username'   => $username,
                'email'      => $person->email,
                'password'   => $password,
                'password2'  => $password,
                'block'      => 0, // enabled
                'sendEmail'  => 0, // Receive System Emails = No
                'requireReset' => 1, // Require Password Reset = Yes
                'registerDate' => Factory::getDate()->toSql(),
                'groups'     => [$groupId], // Ausgewählte Benutzergruppe
            ];
            
            // Erstelle User-Objekt
            $user = new \Joomla\CMS\User\User();
            
            // Binde Daten
            if (!$user->bind($userData)) {
                $app->enqueueMessage(
                    sprintf(\Joomla\CMS\Language\Text::_('COM_CLUBORGANISATION_USER_CREATION_FAILED'), $user->getError()),
                    'error'
                );
                return false;
            }
            
            // Speichere User
            if (!$user->save()) {
                $app->enqueueMessage(
                    sprintf(\Joomla\CMS\Language\Text::_('COM_CLUBORGANISATION_USER_CREATION_FAILED'), $user->getError()),
                    'error'
                );
                return false;
            }
            
            // Speichere Passwort in Session für Anzeige
            $app->setUserState('com_cluborganisation.user.password', $password);
            $app->setUserState('com_cluborganisation.user.username', $username);
            
            return $user->id;
            
        } catch (\Exception $e) {
            $app->enqueueMessage(
                sprintf(\Joomla\CMS\Language\Text::_('COM_CLUBORGANISATION_USER_CREATION_FAILED'), $e->getMessage()),
                'error'
            );
            return false;
        }
    }
    
    /**
     * Generiert einen eindeutigen Username
     *
     * @param   string  $firstname  Vorname
     * @param   string  $lastname   Nachname
     *
     * @return  string  Generierter Username
     *
     * @since   1.2.0
     */
    protected function generateUsername($firstname, $lastname)
    {
        // Bereinige Namen: nur Buchstaben, lowercase
        $firstname = strtolower(preg_replace('/[^a-zA-Z]/', '', $firstname));
        $lastname = strtolower(preg_replace('/[^a-zA-Z]/', '', $lastname));
        
        // Basis-Username: vollständiger Vorname + erster Buchstabe Nachname
        $baseUsername = $firstname . substr($lastname, 0, 1);
        $username = $baseUsername;
        
        $db = Factory::getDbo();
        $lastnamePos = 1;
        
        // Prüfe ob Username existiert, wenn ja: füge weitere Buchstaben vom Nachnamen hinzu
        while ($this->usernameExists($username)) {
            if ($lastnamePos < strlen($lastname)) {
                $lastnamePos++;
                $username = $firstname . substr($lastname, 0, $lastnamePos);
            } else {
                // Alle Buchstaben vom Nachnamen verwendet, füge Zahl hinzu
                $counter = 1;
                while ($this->usernameExists($username . $counter)) {
                    $counter++;
                }
                $username = $username . $counter;
                break;
            }
        }
        
        return $username;
    }
    
    /**
     * Prüft ob ein Username bereits existiert
     *
     * @param   string  $username  Der zu prüfende Username
     *
     * @return  boolean  True wenn Username existiert
     *
     * @since   1.2.0
     */
    protected function usernameExists($username)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__users'))
            ->where($db->quoteName('username') . ' = ' . $db->quote($username));
        
        $db->setQuery($query);
        return (int) $db->loadResult() > 0;
    }
    
    /**
     * Generiert ein zufälliges Passwort das Joomla-Anforderungen erfüllt
     *
     * @param   int  $length  Länge des Passworts (mindestens 12)
     *
     * @return  string  Generiertes Passwort
     *
     * @since   1.3.0
     */
    protected function generatePassword($length = 12)
    {
        // Mindestlänge 12 für Joomla
        if ($length < 12) {
            $length = 12;
        }
        
        // Zeichensätze für verschiedene Anforderungen
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $special = '!@#$%&*+=-';
        
        // Starte mit je einem Zeichen aus jeder Kategorie (Joomla-Anforderung)
        $password = '';
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];
        
        // Fülle Rest mit gemischten Zeichen
        $allChars = $lowercase . $uppercase . $numbers . $special;
        $remainingLength = $length - 4;
        
        for ($i = 0; $i < $remainingLength; $i++) {
            $newChar = $allChars[random_int(0, strlen($allChars) - 1)];
            
            // Verhindere aufeinanderfolgende gleiche Zeichen
            $maxAttempts = 10;
            $attempts = 0;
            while ($attempts < $maxAttempts && strlen($password) > 0 && $newChar === $password[strlen($password) - 1]) {
                $newChar = $allChars[random_int(0, strlen($allChars) - 1)];
                $attempts++;
            }
            
            $password .= $newChar;
        }
        
        // Mische das Passwort um die Reihenfolge zu randomisieren
        $password = str_shuffle($password);
        
        // Stelle sicher, dass keine doppelten aufeinanderfolgenden Zeichen existieren
        $password = $this->removeDuplicateConsecutiveChars($password, $allChars);
        
        return $password;
    }
    
    /**
     * Entfernt doppelte aufeinanderfolgende Zeichen aus einem String
     *
     * @param   string  $str       Der zu bereinigende String
     * @param   string  $allChars  Alle verfügbaren Zeichen zum Ersetzen
     *
     * @return  string  Bereinigter String
     *
     * @since   1.3.0
     */
    private function removeDuplicateConsecutiveChars($str, $allChars)
    {
        $result = '';
        $len = strlen($str);
        
        for ($i = 0; $i < $len; $i++) {
            $char = $str[$i];
            
            // Wenn nicht das erste Zeichen und gleich wie vorheriges
            if ($i > 0 && $char === $result[strlen($result) - 1]) {
                // Finde ein anderes Zeichen
                $maxAttempts = 20;
                $attempts = 0;
                do {
                    $char = $allChars[random_int(0, strlen($allChars) - 1)];
                    $attempts++;
                } while ($attempts < $maxAttempts && $char === $result[strlen($result) - 1]);
            }
            
            $result .= $char;
        }
        
        return $result;
    }
}
