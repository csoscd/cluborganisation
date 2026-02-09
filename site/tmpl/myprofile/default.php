<?php
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
?>

<div class="cluborganisation-myprofile">
    <h1><?php echo Text::_('COM_CLUBORGANISATION_MY_PROFILE'); ?></h1>

    <?php if ($this->myData) : ?>
        <div class="card">
            <div class="card-body">
                <h2><?php echo Text::_('COM_CLUBORGANISATION_PERSONAL_DATA'); ?></h2>
                
                <dl class="row">
                    <dt class="col-sm-3"><?php echo Text::_('COM_CLUBORGANISATION_FIELD_MEMBER_NO'); ?>:</dt>
                    <dd class="col-sm-9"><?php echo $this->escape($this->myData->member_no); ?></dd>

                    <dt class="col-sm-3"><?php echo Text::_('COM_CLUBORGANISATION_NAME'); ?>:</dt>
                    <dd class="col-sm-9">
                        <?php echo $this->escape($this->myData->salutation_title . ' ' . 
                                                  $this->myData->firstname . ' ' . 
                                                  $this->myData->lastname); ?>
                    </dd>

                    <dt class="col-sm-3"><?php echo Text::_('COM_CLUBORGANISATION_FIELD_ADDRESS'); ?>:</dt>
                    <dd class="col-sm-9">
                        <?php echo $this->escape($this->myData->address); ?><br>
                        <?php echo $this->escape($this->myData->zip . ' ' . $this->myData->city); ?><br>
                        <?php echo $this->escape($this->myData->country); ?>
                    </dd>

                    <dt class="col-sm-3"><?php echo Text::_('COM_CLUBORGANISATION_FIELD_EMAIL'); ?>:</dt>
                    <dd class="col-sm-9"><?php echo $this->escape($this->myData->email); ?></dd>

                    <dt class="col-sm-3"><?php echo Text::_('COM_CLUBORGANISATION_FIELD_MOBILE'); ?>:</dt>
                    <dd class="col-sm-9"><?php echo $this->escape($this->myData->mobile); ?></dd>

                    <?php if ($this->myData->telephone) : ?>
                        <dt class="col-sm-3"><?php echo Text::_('COM_CLUBORGANISATION_FIELD_TELEPHONE'); ?>:</dt>
                        <dd class="col-sm-9"><?php echo $this->escape($this->myData->telephone); ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        <?php if (!empty($this->myMemberships)) : ?>
            <div class="card mt-3">
                <div class="card-body">
                    <h2><?php echo Text::_('COM_CLUBORGANISATION_MY_MEMBERSHIPS'); ?></h2>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo Text::_('COM_CLUBORGANISATION_MEMBERSHIP_TYPE'); ?></th>
                                <th><?php echo Text::_('COM_CLUBORGANISATION_BEGIN'); ?></th>
                                <th><?php echo Text::_('COM_CLUBORGANISATION_END'); ?></th>
                                <th><?php echo Text::_('COM_CLUBORGANISATION_STATUS'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $today = date('Y-m-d');
                            foreach ($this->myMemberships as $membership) : 
                                $isActive = ($membership->begin <= $today && ($membership->end >= $today || $membership->end === null));
                            ?>
                                <tr>
                                    <td><?php echo $this->escape($membership->type_title); ?></td>
                                    <td><?php echo HTMLHelper::_('date', $membership->begin, 'd.m.Y'); ?></td>
                                    <td><?php echo $membership->end ? HTMLHelper::_('date', $membership->end, 'd.m.Y') : Text::_('COM_CLUBORGANISATION_ONGOING'); ?></td>
                                    <td>
                                        <?php if ($isActive) : ?>
                                            <span class="badge bg-success"><?php echo Text::_('COM_CLUBORGANISATION_ACTIVE'); ?></span>
                                        <?php else : ?>
                                            <span class="badge bg-secondary"><?php echo Text::_('COM_CLUBORGANISATION_INACTIVE'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
