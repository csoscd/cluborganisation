<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_cluborganisation
 */

declare(strict_types=1);

use Joomla\CMS\Language\Text;

$labels = [
    'person.firstname' => Text::_('COM_CLUBORGANISATION_FIELD_FIRSTNAME_LABEL'),
    'person.lastname' => Text::_('COM_CLUBORGANISATION_FIELD_LASTNAME_LABEL'),
    'person.email' => Text::_('COM_CLUBORGANISATION_FIELD_EMAIL_LABEL'),
    'person.mobile' => Text::_('COM_CLUBORGANISATION_FIELD_MOBILE_LABEL'),
    'membership.type' => Text::_('COM_CLUBORGANISATION_FIELD_MEMBERSHIP_TYPE_LABEL'),
    'membership.begin' => Text::_('COM_CLUBORGANISATION_FIELD_BEGIN_LABEL'),
    'membership.end' => Text::_('COM_CLUBORGANISATION_FIELD_END_LABEL'),
];

$fields = $this->fields ?: array_keys($labels);
?>
<h1><?php echo Text::_('COM_CLUBORGANISATION_PROFILE'); ?></h1>
<?php if (!$this->person) : ?>
    <p><?php echo Text::_('COM_CLUBORGANISATION_NO_DATA'); ?></p>
<?php else : ?>
    <dl class="dl-horizontal">
        <?php foreach ($fields as $field) : ?>
            <?php if (str_starts_with($field, 'person.')) : ?>
                <dt><?php echo $labels[$field] ?? $field; ?></dt>
                <dd>
                    <?php
                    $property = str_replace('person.', '', $field);
                    echo $this->escape((string) $this->person->{$property});
                    ?>
                </dd>
            <?php endif; ?>
        <?php endforeach; ?>
    </dl>

    <?php if ($this->memberships) : ?>
        <h2><?php echo Text::_('COM_CLUBORGANISATION_TABLE_MEMBERSHIPS'); ?></h2>
        <table class="table">
            <thead>
                <tr>
                    <?php foreach ($fields as $field) : ?>
                        <?php if (str_starts_with($field, 'membership.')) : ?>
                            <th><?php echo $labels[$field] ?? $field; ?></th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->memberships as $membership) : ?>
                    <tr>
                        <?php foreach ($fields as $field) : ?>
                            <?php if ($field === 'membership.type') : ?>
                                <td><?php echo $this->escape((string) $membership->membership_type_title); ?></td>
                            <?php elseif ($field === 'membership.begin') : ?>
                                <td><?php echo $this->escape($membership->begin); ?></td>
                            <?php elseif ($field === 'membership.end') : ?>
                                <td><?php echo $this->escape($membership->end); ?></td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>
