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
    'person.member_no' => Text::_('COM_CLUBORGANISATION_FIELD_MEMBER_NO_LABEL'),
    'membership.type' => Text::_('COM_CLUBORGANISATION_FIELD_MEMBERSHIP_TYPE_LABEL'),
    'membership.begin' => Text::_('COM_CLUBORGANISATION_FIELD_BEGIN_LABEL'),
    'membership.end' => Text::_('COM_CLUBORGANISATION_FIELD_END_LABEL'),
];

$fields = $this->fields ?: array_keys($labels);
?>
<h1><?php echo Text::_('COM_CLUBORGANISATION_REPORT_MEMBERSHIPS'); ?> (<?php echo (int) $this->year; ?>)</h1>

<h2><?php echo Text::_('COM_CLUBORGANISATION_FIELD_BEGIN_LABEL'); ?></h2>
<?php if (!$this->begins) : ?>
    <p><?php echo Text::_('COM_CLUBORGANISATION_NO_DATA'); ?></p>
<?php else : ?>
    <table class="table">
        <thead>
            <tr>
                <?php foreach ($fields as $field) : ?>
                    <th><?php echo $labels[$field] ?? $field; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->begins as $item) : ?>
                <tr>
                    <?php foreach ($fields as $field) : ?>
                        <td>
                            <?php if ($field === 'person.firstname') : ?>
                                <?php echo $this->escape($item->firstname); ?>
                            <?php elseif ($field === 'person.lastname') : ?>
                                <?php echo $this->escape($item->lastname); ?>
                            <?php elseif ($field === 'person.member_no') : ?>
                                <?php echo $this->escape($item->member_no); ?>
                            <?php elseif ($field === 'membership.type') : ?>
                                <?php echo $this->escape((string) $item->membership_type_title); ?>
                            <?php elseif ($field === 'membership.begin') : ?>
                                <?php echo $this->escape($item->begin); ?>
                            <?php elseif ($field === 'membership.end') : ?>
                                <?php echo $this->escape($item->end); ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<h2><?php echo Text::_('COM_CLUBORGANISATION_FIELD_END_LABEL'); ?></h2>
<?php if (!$this->ends) : ?>
    <p><?php echo Text::_('COM_CLUBORGANISATION_NO_DATA'); ?></p>
<?php else : ?>
    <table class="table">
        <thead>
            <tr>
                <?php foreach ($fields as $field) : ?>
                    <th><?php echo $labels[$field] ?? $field; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->ends as $item) : ?>
                <tr>
                    <?php foreach ($fields as $field) : ?>
                        <td>
                            <?php if ($field === 'person.firstname') : ?>
                                <?php echo $this->escape($item->firstname); ?>
                            <?php elseif ($field === 'person.lastname') : ?>
                                <?php echo $this->escape($item->lastname); ?>
                            <?php elseif ($field === 'person.member_no') : ?>
                                <?php echo $this->escape($item->member_no); ?>
                            <?php elseif ($field === 'membership.type') : ?>
                                <?php echo $this->escape((string) $item->membership_type_title); ?>
                            <?php elseif ($field === 'membership.begin') : ?>
                                <?php echo $this->escape($item->begin); ?>
                            <?php elseif ($field === 'membership.end') : ?>
                                <?php echo $this->escape($item->end); ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
