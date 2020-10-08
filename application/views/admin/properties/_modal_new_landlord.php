<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('new') . " " . lang("property_landlord") ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form role="form" enctype="multipart/form-data" data-parsley-validate="" novalidate="" action="<?php echo base_url(); ?>admin/leads/saved_leads/" method="post" class="form-horizontal">
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('lead_name') ?> <span
                            class="text-danger">*</span></label>
                    <div class="col-lg-10">
                        <div class="input-group">
                            <div class="input-group-addon">
                                <span>
                                    <input name="salutaiton" type="radio" id="salutaion_select" value="Mr"> Mr
                                    <input name="salutaiton" type="radio" id="salutaion_select" value="Mrs" style="margin-left:10px;"> Mrs
                                    <input name="salutaiton" type="radio" id="salutaion_select" value="Miss" style="margin-left:10px;"> Miss
                                    <input name="salutaiton" type="radio" id="salutaion_select" value="Ms" style="margin-left:10px;"> Ms
                                </span>
                            </div>
                            <input type="text" class="form-control" name="lead_name" required="">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('lead_source') ?> </label>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <select name="lead_source_id" class="form-control select_box" style="width: 100%" required="">
                                <?php
                                $lead_source_info = $this->db->order_by('lead_source_id', 'DESC')->get('tbl_lead_source')->result();
                                if (!empty($lead_source_info)) {
                                    foreach ($lead_source_info as $v_lead_source) {
                                        ?>
                                        <option value="<?= $v_lead_source->lead_source_id ?>"><?= $v_lead_source->lead_source ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                            </select>
                        </div>
                    </div>
                    <label class="col-lg-2 control-label"><?= lang('lead_status') ?> </label>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <select name="lead_status_id" class="form-control select_box" style="width: 100%" required="">
                                <?php
                                if (!empty($status_info)) {
                                    foreach ($status_info as $type => $v_leads_status) {
                                        if (!empty($v_leads_status)) {
                                            ?>
                                            <optgroup label="<?= lang($type) ?>">
                                                <?php foreach ($v_leads_status as $v_l_status) { ?>
                                                    <option value="<?= $v_l_status->lead_status_id ?>"><?= $v_l_status->lead_status ?></option>
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
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('lead_category') ?> </label>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <select name="lead_category_id" class="form-control select_box" style="width: 100%" required="">
                                <?php
                                $lead_category_info = $this->db->order_by('lead_category_id', 'DESC')->get('tbl_lead_category')->result();
                                if (!empty($lead_category_info)) {
                                    foreach ($lead_category_info as $v_lead_category) {
                                        ?>
                                        <option value="<?= $v_lead_category->lead_category_id ?>"><?= $v_lead_category->lead_category ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                            </select>
                        </div>
                    </div>
                    <label class="col-lg-2 control-label"><?= lang('nationality') ?> </label>
                    <div class="col-lg-4">
                        <select name="nationality" class="form-control select_box" style="width: 100%" required="">
                            <?php
                            if (!empty($nationalities)) {
                                foreach ($nationalities as $v_leads_nationality) {
                                    ?>
                                    <option value="<?= $v_leads_nationality->id ?>" style="<?= base_url("assets/img/flags/" . $v_leads_nationality->flag); ?>"><?= $v_leads_nationality->nationality ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('lead_date_of_birth') ?> </label>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <input class="form-control datepicker" type="text" name="date_of_birth" data-date-format="mm-dd-yyyy" data-parsley-id="10">
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-calendar"></i></a>
                            </div>
                        </div>
                    </div>
                    <label class="col-lg-2 control-label"><?= lang('lead_passport') ?> </label>
                    <div class="col-lg-4">
                        <div class="input-group" style="width:100%;">
                            <input type="text" class="form-control" name="passport_number"/>
                            <div class="input-group-addon" style="padding:0;border:0;width:30%;" data-toggle="tooltip" data-placement="top" data-original-title="Passport Expire">
                                <div class="input-group">
                                    <input class="form-control datepicker" type="text" name="passport_expire" data-date-format="mm-dd-yyyy" data-parsley-id="10">
                                    <div class="input-group-addon">
                                        <a href="#"><i class="fa fa-calendar"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('organization') ?> </label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="organization">
                    </div>
                    <label class="col-lg-2 control-label"><?= lang('address') ?> </label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="address">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('contact_name') ?> <span class="text-danger">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="contact_name" required="">
                    </div>
                    <label class="col-lg-2 control-label"><?= lang('city') ?> </label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="city">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('email') ?> <span class="text-danger">*</span></label>
                    <div class="col-lg-4">
                        <input type="email" class="form-control" name="email" required="">
                    </div>
                    <label class="col-lg-2 control-label"><?= lang('state') ?> </label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="state">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('email_2') ?></label>
                    <div class="col-lg-4">
                        <input type="email" class="form-control" name="email2">
                    </div>
                    <label class="col-lg-2 control-label"><?= lang('country') ?></label>
                    <div class="col-lg-4">
                        <select name="country" class="form-control person select_box" style="width: 100%">
                            <optgroup label="Default Country">
                                <option value="<?= $this->config->item('company_country') ?>"><?= $this->config->item('company_country') ?></option>
                            </optgroup>
                            <optgroup label="<?= lang('other_countries') ?>">
                                <?php
                                $countries = $this->db->get('tbl_countries')->result();
                                if (!empty($countries)): foreach ($countries as $country):
                                        ?>
                                        <option value="<?= $country->value ?>"><?= $country->value ?></option>
                                        <?php
                                    endforeach;
                                endif;
                                ?>
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('email_3') ?></label>
                    <div class="col-lg-4">
                        <input type="email" class="form-control" name="email3">
                    </div>
                    <label class="col-lg-2 control-label"><?= lang('landline') ?></label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="phone">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('fax') ?> </label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="fax">
                    </div>
                    <label class="col-lg-2 control-label"><?= lang('phone') ?><span class="text-danger">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" required="" class="form-control" name="mobile"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('skype_id') ?> </label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="skype">
                    </div>
                    <label class="col-lg-2 control-label"><?= lang('phone_2') ?></label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="phone2">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('twitter_profile_link') ?> </label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="twitter">
                    </div>
                    <label class="col-lg-2 control-label"><?= lang('phone_3') ?></label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="phone3">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-lg-2 control-label"><?= lang('facebook_profile_link') ?> </label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="facebook">
                    </div>
                    <label class="col-lg-2 control-label"><?= lang('phone_4') ?></label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="phone4">
                    </div>
                </div>
                <div class="form-group" id="border-none">
                    <label class="col-lg-2 control-label"><?= lang('short_note') ?> </label>
                    <div class="col-lg-10">
                        <textarea name="notes" class="form-control textarea" rows="4"></textarea>
                    </div>
                </div>
                <?php $leads_id = null; ?>
                <input type="hidden" name="permission" value="custom_permission">
                <div class="form-group show" id="permission_user_1">
                    <label for="field-1" class="col-sm-2 control-label"><?= lang('assined_to'); ?> <span class="required">*</span></label>
                    <div class="col-sm-9">
                        <?php
                        if (!empty($assign_user)) {
                            foreach ($assign_user as $key => $v_user) {
                                if ($v_user->username != "admin") {
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
                                            <input type="checkbox" value="<?= $v_user->user_id ?>" name="assigned_to[]" onclick="document.getElementById('action_1<?= $v_user->user_id ?>').classList.toggle('hide');" class="needsclick">
                                            <span class="fa fa-check"></span><?= $this->db->where('user_id', $v_user->user_id)->get('tbl_account_details')->row()->fullname . ' ' . $role ?>
                                        </label>
                                    </div>
                                    <div class="action_1 p hide" id="action_1<?= $v_user->user_id ?>">
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
                      <button type="submit" class="btn btn-sm btn-primary"><?= lang('save') ?></button>
                </div>
        </form>
    </div>
</div>
