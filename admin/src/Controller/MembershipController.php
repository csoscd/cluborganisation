<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

namespace CSOSCD\Component\ClubOrganisation\Administrator\Controller;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;

class MembershipController extends FormController
{
    protected $text_prefix = 'COM_CLUBORGANISATION_MEMBERSHIP';
    protected $view_list = 'memberships';

    /**
     * AJAX-Endpunkt: Gibt alle aktiven Mitgliedschaften des übergeordneten Typs zurück.
     * URL: index.php?option=com_cluborganisation&task=membership.getParentMemberships&type_id=X&format=json
     *
     * @return  void
     *
     * @since   2.3.0
     */
    public function getParentMemberships(): void
    {
        $app    = Factory::getApplication();
        $input  = $app->getInput();
        $typeId = (int) $input->getInt('type_id', 0);

        $response = ['success' => false, 'is_dependent' => false, 'data' => []];

        if ($typeId > 0) {
            $db    = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select(['is_dependent', 'depends_on_type'])
                ->from($db->quoteName('#__cluborganisation_membershiptypes'))
                ->where($db->quoteName('id') . ' = ' . $typeId);
            $db->setQuery($query);
            $typeInfo = $db->loadObject();

            if ($typeInfo && $typeInfo->is_dependent && $typeInfo->depends_on_type) {
                $today = date('Y-m-d');
                $query = $db->getQuery(true)
                    ->select(['m.id', 'm.begin', 'm.end',
                        'CONCAT(' . $db->quoteName('p.lastname') . ', \', \', ' . $db->quoteName('p.firstname') . ') AS person_name'])
                    ->from($db->quoteName('#__cluborganisation_memberships', 'm'))
                    ->join('LEFT', $db->quoteName('#__cluborganisation_persons', 'p') . ' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('m.person_id'))
                    ->where($db->quoteName('m.type') . ' = ' . (int) $typeInfo->depends_on_type)
                    ->where('(' . $db->quoteName('m.end') . ' IS NULL OR ' . $db->quoteName('m.end') . ' >= ' . $db->quote($today) . ')')
                    ->order($db->quoteName('p.lastname') . ', ' . $db->quoteName('p.firstname') . ', ' . $db->quoteName('m.begin'));
                $db->setQuery($query);

                $response['success']      = true;
                $response['is_dependent'] = true;
                $response['data']         = $db->loadObjectList();
            } elseif ($typeInfo) {
                $response['success']      = true;
                $response['is_dependent'] = (bool) $typeInfo->is_dependent;
            }
        }

        $app->setHeader('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($response);
        $app->close();
    }
}
