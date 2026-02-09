<?php
namespace CSOSCD\Component\ClubOrganisation\Administrator\Table;
defined('_JEXEC') or die;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class SalutationTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__cluborganisation_salutations', 'id', $db);
    }
}
