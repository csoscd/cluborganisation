<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_cluborganisation
 */

declare(strict_types=1);

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.formvalidator');

$labels = [
    'person.email' => Text::_('COM_CLUBORGANISATION_FIELD_EMAIL_LABEL'),
    'person.mobile' => Text::_('COM_CLUBORGANISATION_FIELD_MOBILE_LABEL'),
    'person.telephone' => Text::_('COM_CLUBORGANISATION_FIELD_TELEPHONE_LABEL'),
    'person.address' => Text::_('COM_CLUBORGANISATION_FIELD_ADDRESS_LABEL'),
    'person.city' => Text::_('COM_CLUBORGANISATION_FIELD_CITY_LABEL'),
    'person.zip' => Text::_('COM_CLUBORGANISATION_FIELD_ZIP_LABEL'),
    'person.country' => Text::_('COM_CLUBORGANISATION_FIELD_COUNTRY_LABEL'),
    'membership.type' => Text::_('COM_CLUBORGANISATION_FIELD_MEMBERSHIP_TYPE_LABEL'),
    'membership.begin' => Text::_('COM_CLUBORGANISATION_FIELD_BEGIN_LABEL'),
    'membership.end' => Text::_('COM_CLUBORGANISATION_FIELD_END_LABEL'),
];

$fields = $this->fields ?: array_keys($labels);
?>
<h1><?php echo Text::_('COM_CLUBORGANISATION_PROFILE_EDIT'); ?></h1>
<form action="" method="post" name="profileForm" id="profileForm" class="form-validate">
    <?php foreach ($this->form->getFieldset('details') as $field) : ?>
        <?php $key = 'person.' . $field->fieldname; ?>
        <?php if (in_array($key, $fields, true)) : ?>
            <div class="control-group">
                <div class="control-label"><?php echo $field->label; ?></div>
                <div class="controls"><?php echo $field->input; ?></div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <input type="hidden" name="task" value="profileedit.save">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

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
