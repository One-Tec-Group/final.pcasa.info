<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('convert_to_opportunity') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data" id="form" action="<?php echo base_url("admin/opportunities/saved_opportunity"); ?>" method="post" class="form-horizontal">
            <input type="hidden" name="opportunity_lead_id" value="<?= $lead_details->leads_id; ?>"/>
            <input type="hidden" name="opportunity_name" value="<?= $lead_details->lead_name; ?>"/>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('opportunity_name') ?></label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control" value="<?= $lead_details->lead_name; ?>" readonly disabled>
                    </div>
                    <label class="col-lg-2 control-label"><?= lang('stages') ?></label> <span class="text-danger">*</span>
                    <div class="col-lg-4">
                        <select name="stages" class="form-control select_box" style="width: 100%;" required>
                            <option value="new" selected><?= lang('new') ?></option>
                            <option value="qualification"><?= lang('qualification') ?></option>
                            <option value="proposition"><?= lang('proposition') ?></option>
                            <option value="won"><?= lang('won') ?></option>
                            <option value="lost"><?= lang('lost') ?></option>
                            <option value="dead"><?= lang('dead') ?></option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('probability') ?> %</label>
                    <div class="col-lg-10">
                        <input name="probability" required data-ui-slider="" type="text" data-slider-min="0" data-slider-max="100" data-slider-step="1" data-slider-orientation="horizontal" class="slider slider-horizontal" data-slider-id="red">
                    </div>
                </div>
                <div class="form-group" id="border-none">
                    <label for="field-1" class="col-sm-2 control-label"><?= lang('current_state') ?> <span class="text-danger">*</span></label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <select name="opportunities_state_reason_id" style="width: 100%" class="select_box" required>
                                <?php
                                if (!empty($all_state)) {
                                    foreach ($all_state as $state => $opportunities_state) {
                                        if (!empty($state)) { ?>
                                            <optgroup label="<?= lang($state) ?>">
                                                <?php foreach ($opportunities_state as $v_state) { ?>
                                                    <option value="<?= $v_state->opportunities_state_reason_id ?>"><?= $v_state->opportunities_state_reason ?></option>
                                                        <?php } ?>
                                            </optgroup>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <label class="col-lg-2 control-label"><?= lang('close_date') ?></label>
                    <?php
                        $close_date = date('Y-m-d');
                        $next_action_date = date('Y-m-d');
                    ?>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <input class="form-control datepicker" type="text" value="<?= $close_date; ?>" name="close_date" data-date-format="<?= config_item('date_picker_format'); ?>">
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-calendar"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('expected_revenue') ?></label>
                    <div class="col-lg-4">
                        <input type="text" data-parsley-type="number" min="0" class="form-control" name="expected_revenue">
                    </div>
                    <label class="col-lg-2 control-label"><?= lang('new_link') ?></label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="new_link"/>
                    </div>
                </div>
                <div class="form-group terms">
                    <label class="col-lg-2 control-label"><?= lang('next_action') ?> </label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="next_action">
                    </div>
                    <label class="col-lg-2 control-label"><?= lang('next_action_date') ?></label>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <input class="form-control datepicker" type="text" value="<?= $next_action_date; ?>" name="next_action_date" data-date-format="<?= config_item('date_picker_format'); ?>">
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-calendar"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('short_note') ?> </label>
                    <div class="col-lg-8">
                        <textarea name="notes" class="form-control textarea"></textarea>
                    </div>
                </div>
                <?= custom_form_Fields(8, null, true); ?>
                <input type="hidden" name="permission" value="custom_permission">
                <div class="form-group show" id="permission_user_1">
                    <label for="field-1" class="col-sm-2 control-label"><?= lang('who_responsible'); ?> <span class="required">*</span></label>
                    <div class="col-sm-9">
                        <?php
                        if (!empty($assign_user)) {
                            foreach ($assign_user as $key => $v_user) {
                              if($v_user->username != "admin"){
                                if ($v_user->role_id == 1) {
                                    $disable = true;
                                    $role = '<strong class="badge btn-danger">' . lang('admin') . '</strong>';
                                } else {
                                    $disable = false;
                                    $role = '<strong class="badge btn-primary">' . lang('staff') . '</strong>';
                                }
                                ?>
                                <div class="checkbox c-checkbox needsclick">
                                    <label class="needsclick">
                                        <input type="checkbox" onchange='toggleHiddenClass(<?= $v_user->user_id ?>);' value="<?= $v_user->user_id ?>" name="assigned_to[]" class="needsclick">
                                        <span class="fa fa-check"></span><?= $this->db->where('user_id', $v_user->user_id)->get('tbl_account_details')->row()->fullname . ' ' . $role ?>
                                    </label>
                                </div>
                                <div class="action_1 p hidden" id="action_1<?= $v_user->user_id ?>">
                                    <label class="checkbox-inline c-checkbox">
                                        <input id="<?= $v_user->user_id ?>" checked type="checkbox" name="action_1<?= $v_user->user_id ?>[]" disabled value="view">
                                        <span class="fa fa-check"></span><?= lang('can') . ' ' . lang('view') ?>
                                    </label>
                                    <label class="checkbox-inline c-checkbox">
                                        <input <?php
                                        if (!empty($disable)) {
                                            echo 'disabled' . ' ' . 'checked';
                                        }
                                        ?> id="<?= $v_user->user_id ?>" type="checkbox" value="edit" name="action_<?= $v_user->user_id ?>[]">
                                        <span class="fa fa-check"></span><?= lang('can') . ' ' . lang('edit') ?>
                                    </label>
                                    <label class="checkbox-inline c-checkbox">
                                        <input <?php
                                        if (!empty($disable)) {
                                            echo 'disabled' . ' ' . 'checked';
                                        }
                                        ?> id="<?= $v_user->user_id ?>" name="action_<?= $v_user->user_id ?>[]" type="checkbox" value="delete">
                                        <span class="fa fa-check"></span><?= lang('can') . ' ' . lang('delete') ?>
                                    </label>
                                    <input id="<?= $v_user->user_id ?>" type="hidden" name="action_<?= $v_user->user_id ?>[]" value="view">
                                </div>
                                <?php
                            }
                        }
                        }
                        ?>
                    </div>
                </div>
                <div class="btn-bottom-toolbar text-right">
                    <button type="submit" class="btn btn-sm btn-primary"><?= lang('convert') ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
$('[data-ui-slider]').slider();
function toggleHiddenClass(id) {
  var element = document.getElementById("action_1" + id);
  if (element.classList) {
    element.classList.toggle("hidden");
  } else {
    var classes = element.className.split(" ");
    var i = classes.indexOf("hidden");
    if (i >= 0){
      classes.splice(i, 1);
    } else {
      classes.push("hidden");
      element.className = classes.join(" ");
    }
  }
}
</script>
