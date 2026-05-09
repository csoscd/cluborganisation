<?php
/**
 * @package     ClubOrganisation
 * @subpackage  Administrator
 * @author      Christian Schulz <technik@meinetechnikwelt.rocks>
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

// Type dependency data for JavaScript
$membershipTypeData = [];
if (!empty($this->membershipTypes)) {
    foreach ($this->membershipTypes as $mt) {
        $membershipTypeData[(int) $mt->id] = [
            'is_dependent'    => (bool) $mt->is_dependent,
            'depends_on_type' => (int) $mt->depends_on_type,
            'has_dependents'  => (bool) $mt->has_dependents,
        ];
    }
}
?>
<script>
(function () {
    var typeData    = <?php echo json_encode($membershipTypeData); ?>;
    var currentType = <?php echo (int) $this->item->type; ?>;
    var currentDependsOn = <?php echo (int) ($this->item->depends_on_membership_id ?? 0); ?>;
    var dependentCount = <?php echo (int) $this->dependentMembershipsCount; ?>;

    function getWrapper() {
        return document.getElementById('co-parent-membership-wrapper');
    }

    function getSelect() {
        return document.getElementById('jform_depends_on_membership_id');
    }

    function showParentField(memberships, preselect) {
        var wrapper = getWrapper();
        var sel     = getSelect();
        if (!wrapper || !sel) { return; }

        // Rebuild options
        sel.innerHTML = '';
        var emptyOpt  = document.createElement('option');
        emptyOpt.value = '';
        emptyOpt.textContent = Joomla.Text._('COM_CLUBORGANISATION_SELECT_PARENT_MEMBERSHIP');
        sel.appendChild(emptyOpt);

        memberships.forEach(function (m) {
            var opt = document.createElement('option');
            opt.value = m.id;
            var endLabel = m.end ? m.end : Joomla.Text._('COM_CLUBORGANISATION_ONGOING');
            opt.textContent = m.person_name + ' (' + m.begin + ' – ' + endLabel + ')';
            if (parseInt(m.id, 10) === preselect) {
                opt.selected = true;
            }
            sel.appendChild(opt);
        });

        wrapper.style.display = '';
    }

    function hideParentField() {
        var wrapper = getWrapper();
        var sel     = getSelect();
        if (wrapper) { wrapper.style.display = 'none'; }
        if (sel)     { sel.value = ''; }
    }

    function loadParentMemberships(typeId, preselect) {
        var info = typeData[typeId];
        if (!info || !info.is_dependent || !info.depends_on_type) {
            hideParentField();
            return;
        }
        var url = 'index.php?option=com_cluborganisation&task=membership.getParentMemberships'
                + '&type_id=' + typeId + '&format=json';
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success && data.is_dependent) {
                    showParentField(data.data, preselect || 0);
                } else {
                    hideParentField();
                }
            })
            .catch(function () { hideParentField(); });
    }

    function updateCascadeWarning(typeId) {
        var warning = document.getElementById('co-cascade-warning');
        if (!warning) { return; }
        var info = typeData[typeId] || null;
        if (info && info.has_dependents) {
            warning.style.display = '';
        } else {
            warning.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var typeSelect = document.getElementById('jform_type');
        if (!typeSelect) { return; }

        typeSelect.addEventListener('change', function () {
            var typeId = parseInt(this.value, 10);
            loadParentMemberships(typeId, 0);
            updateCascadeWarning(typeId);
        });

        // On page load for edit mode: restore parent membership field and warning
        if (currentType) {
            if (typeData[currentType] && typeData[currentType].is_dependent) {
                loadParentMemberships(currentType, currentDependsOn);
            }
            updateCascadeWarning(currentType);
        }
    });
}());
</script>
<form action="<?php echo Route::_('index.php?option=com_cluborganisation&view=membership&layout=edit&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="membership-form" class="form-validate">
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <?php echo $this->form->renderField('person_id'); ?>
                    <?php echo $this->form->renderField('type'); ?>

                    <div id="co-parent-membership-wrapper" style="display:none;">
                        <?php echo $this->form->renderField('depends_on_membership_id'); ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?php echo $this->form->renderField('begin'); ?>
                        </div>
                        <div class="col-md-6">
                            <?php echo $this->form->renderField('end'); ?>
                        </div>
                    </div>

                    <div id="co-cascade-warning" class="alert alert-warning" style="display:none;">
                        <span class="icon-warning me-1" aria-hidden="true"></span>
                        <strong><?php echo Text::_('JWARNING'); ?>:</strong>
                        <?php if ($this->dependentMembershipsCount > 0) : ?>
                            <?php echo Text::sprintf('COM_CLUBORGANISATION_MEMBERSHIP_CASCADE_WARNING_COUNT', $this->dependentMembershipsCount); ?>
                        <?php else : ?>
                            <?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIP_CASCADE_WARNING'); ?>
                        <?php endif; ?>
                    </div>

                    <?php echo $this->form->renderField('comment'); ?>
                    <?php echo $this->form->renderField('catid'); ?>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="task" value="" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
