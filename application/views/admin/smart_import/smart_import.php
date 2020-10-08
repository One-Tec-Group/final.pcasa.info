<?= message_box('success'); ?>
<?= message_box('warning'); ?>
<?= message_box('error'); ?>
<div class="panel panel-custom">
    <header class="panel-heading">
        <div class="panel-title"><strong><?= lang('smart_bulk_import') ?></strong>
            <div class="pull-right hidden-print">
                <div class="pull-right ">
                  <form action="<?= base_url("admin/smart_import"); ?>" method="post" accept-charset="utf-8">
                      <input type="hidden" name="download_sample" value="true">
                      <button type="submit" class="btn btn-success legitRipple"><?= lang('download_sample') ?></button>
                  </form>
                </div>
            </div>
        </div>
    </header>
    <div class="panel-body">
           <ul class="list-unstyled">
                <li>1. Your CSV data should be in the format below. The first line of your CSV file should be the column headers as in the table example. Also make sure that your file is
                   <b>UTF-8</b> to avoid unnecessary <b>encoding problems</b>.
                </li>
                <li>2. If the column <b>you are trying to import is date make sure that is formatted in format Y-m-d (2019-11-13).</b></li>
                <li>3. All other fields is <b>requiered fileds.</b></li>
                <li>4. CSV file must contain <b>less than 10,000 rows.</b></li>
            </ul>
        <hr/><br/>
        <form role="form" enctype="multipart/form-data" id="form" action="<?php echo base_url(); ?>admin/smart_import" method="post" class="form-horizontal">
          <input type="hidden" name="leads_import" value="true">
            <div class="panel-body">
                <div class="form-group">
                    <label for="field-1" class="col-sm-2 control-label">
                        <?= lang('choose_file') ?><span class="required">*</span></label>
                    <div class="col-sm-5">
                        <div style="display: inherit;margin-bottom: inherit" class="fileinput fileinput-new" data-provides="fileinput">
                            <span class="btn btn-default btn-file">
                              <span class="fileinput-new"><?= lang('select_file') ?></span>
                              <span class="fileinput-exists"><?= lang('change') ?></span>
                              <input type="file" name="upload_file" >
                            </span>
                            <span class="fileinput-filename"></span>
                            <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('lead_source') ?> </label>
                    <div class="col-lg-4">
                        <select name="lead_source_id" class="form-control select_box" style="width: 100%" required="">
                            <?php
                            $lead_source_info = $this->db->get('tbl_lead_source')->result();
                            if (!empty($lead_source_info)) {
                                foreach ($lead_source_info as $v_lead_source) {
                                    ?>
                                    <option
                                        value="<?= $v_lead_source->lead_source_id ?>" <?= (!empty($leads_info) && $leads_info->lead_source_id == $v_lead_source->lead_source_id ? 'selected' : '') ?>><?= $v_lead_source->lead_source ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('lead_category') ?> </label>
                    <div class="col-lg-4">
                        <select name="lead_category_id" class="form-control select_box" style="width: 100%" required="">
                            <?php
                            $lead_category_info = $this->db->get('tbl_lead_category')->result();
                            if (!empty($lead_category_info)) {
                                foreach ($lead_category_info as $v_lead_category) {
                                    ?>
                                    <option
                                        value="<?= $v_lead_category->lead_category_id ?>" <?= (!empty($leads_info) && $leads_info->lead_category_id == $v_lead_category->lead_category_id ? 'selected' : '') ?>><?= $v_lead_category->lead_category ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('lead_status') ?> </label>
                    <div class="col-lg-4">
                        <select name="lead_status_id" class="form-control select_box" style="width: 100%" required="">
                            <?php
                            if (!empty($status_info)) {
                                foreach ($status_info as $type => $leads_status) {
                                    if (!empty($leads_status)) {
                                        ?>
                                        <optgroup label="<?= lang($type) ?>">
                                            <?php foreach ($leads_status as $v_status) { ?>
                                                <option
                                                    value="<?= $v_status->lead_status_id ?>" <?php
                                                if (!empty($leads_info->lead_status_id)) {
                                                    echo $v_status->lead_status_id == $leads_info->lead_status_id ? 'selected' : '';
                                                }
                                                ?>><?= $v_status->lead_status ?></option>
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
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('property_source') ?> </label>
                    <div class="col-lg-4">
                        <select name="property_source_id" class="form-control select_box" style="width: 100%" required="">
                            <?php
                            $property_source_info = $this->db->get('tbl_property_source')->result();
                            if (!empty($property_source_info)) {
                                foreach ($property_source_info as $v_property_source) {
                                    ?>
                                    <option value="<?= $v_property_source->property_source_id ?>" <?= (!empty($leads_info) && $leads_info->property_source_id == $v_property_source->property_source_id ? 'selected' : '') ?>><?= $v_property_source->property_source ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="border-none">
                    <label for="field-1" class="col-sm-2 control-label"><?= lang('assined_to') ?> <span class="required">*</span></label>
                    <div class="col-sm-9">
                        <div class="checkbox c-radio needsclick">
                            <label class="needsclick">
                                <input id="" type="radio" name="permission" value="everyone">
                                <span class="fa fa-circle"></span><?= lang('everyone') ?>
                                <i title="<?= lang('permission_for_all') ?>" class="fa fa-question-circle" data-toggle="tooltip" data-placement="top"></i>
                            </label>
                        </div>
                        <div class="checkbox c-radio needsclick">
                            <label class="needsclick">
                                <input id="" checked type="radio" name="permission" value="custom_permission">
                                <span class="fa fa-circle"></span><?= lang('custom_permission') ?>
                                <i title="<?= lang('permission_for_customization') ?>" class="fa fa-question-circle" data-toggle="tooltip" data-placement="top"></i>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group show" id="permission_user_1">
                    <label for="field-1" class="col-sm-2 control-label"><?= lang('select') . ' ' . lang('users') ?>
                        <span class="required">*</span></label>
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
                                        <input type="checkbox"
                                            <?php
                                            if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                                                $get_permission = json_decode($leads_info->permission);
                                                foreach ($get_permission as $user_id => $v_permission) {
                                                    if ($user_id == $v_user->user_id) {
                                                        echo 'checked';
                                                    }
                                                }

                                            }
                                            ?> value="<?= $v_user->user_id ?>" name="assigned_to[]" class="needsclick">
                                            <?php
                                            $staff_details = $this->db->where('user_id', $v_user->user_id)->get('tbl_account_details')->row();
                                             ?>
                                        <span class="fa fa-check"></span><?= $staff_details->fullname . ' ' . $role ?>
                                    </label>
                                </div>
                                <div class="action_1 p
                                <?php
                                if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                                    $get_permission = json_decode($leads_info->permission);

                                    foreach ($get_permission as $user_id => $v_permission) {
                                        if ($user_id == $v_user->user_id) {
                                            echo 'show';
                                        }
                                    }

                                }
                                ?>" id="action_1<?= $v_user->user_id ?>">
                                    <label class="checkbox-inline c-checkbox">
                                        <input id="<?= $v_user->user_id ?>" checked type="checkbox" name="action_1<?= $v_user->user_id ?>[]" disabled value="view">
                                        <span class="fa fa-check"></span><?= lang('can') . ' ' . lang('view') ?>
                                    </label>
                                    <label class="checkbox-inline c-checkbox">
                                        <input <?php if (!empty($disable)) {
                                            echo 'disabled' . ' ' . 'checked';
                                        } ?> id="<?= $v_user->user_id ?>"
                                            <?php
                                            if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                                                $get_permission = json_decode($leads_info->permission);
                                                foreach ($get_permission as $user_id => $v_permission) {
                                                    if ($user_id == $v_user->user_id) {
                                                        if (in_array('edit', $v_permission)) {
                                                            echo 'checked';
                                                        };

                                                    }
                                                }

                                            }
                                            ?> type="checkbox" value="edit" name="action_<?= $v_user->user_id ?>[]">
                                        <span class="fa fa-check"></span><?= lang('can') . ' ' . lang('edit') ?>
                                    </label>
                                    <label class="checkbox-inline c-checkbox">
                                        <input <?php if (!empty($disable)) {
                                            echo 'disabled' . ' ' . 'checked';
                                        } ?> id="<?= $v_user->user_id ?>"
                                            <?php
                                            if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                                                $get_permission = json_decode($leads_info->permission);
                                                foreach ($get_permission as $user_id => $v_permission) {
                                                    if ($user_id == $v_user->user_id) {
                                                        if (in_array('delete', $v_permission)) {
                                                            echo 'checked';
                                                        };
                                                    }
                                                }

                                            }
                                            ?> name="action_<?= $v_user->user_id ?>[]" type="checkbox" value="delete">
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
                <div class="form-group">
                    <label class="col-lg-2 control-label"></label>
                    <div class="col-lg-6">
                        <button type="submit" class="btn btn-sm btn-primary"></i> <?= lang('upload') ?></button>
                    </div>
                </div>
            </div>
    </div>
</div>
