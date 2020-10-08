<?= message_box('success'); ?>
<?= message_box('error');
$created = can_action('55', 'created');
$edited = can_action('55', 'edited');
$deleted = can_action('55', 'deleted');
$kanban = $this->session->userdata('leads_kanban');
$uri_segment = $this->uri->segment(4);
if (!empty($kanban)) {
    $k_leads = 'kanban';
} elseif ($uri_segment == 'kanban') {
    $k_leads = 'kanban';
} else {
    $k_leads = 'list';
}

if ($k_leads == 'kanban') {
    $text = 'list';
    $btn = 'purple';
} else {
    $text = 'kanban';
    $btn = 'danger';
}
?>
<div class="row mb-lg">
    <div class="col-sm-2 ">
        <div class="pull-left pr-lg">
            <a href="<?= base_url() ?>admin/leads/index/<?= $text ?>" class="btn btn-xs btn-<?= $btn ?> pull-right" data-toggle="tooltip" data-placement="top" title="<?= lang('switch_to_kanban') ?>">
                <i class="fa fa-undo"> </i><?= ' ' . lang('switch_to_' . $text) ?>
            </a>
        </div>
    </div>
    <?php if ($text == 'kanban') {
        $type = $this->uri->segment(4);
        $id = $this->uri->segment(5);
        ?>
    <?php } ?>
</div>
<div class="row">
    <div id="leads_data" class="col-sm-12">
        <?php if ($k_leads == 'kanban') { ?>
            <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/kanban/kan-app.css"/>
            <div class="app-wrapper">
                <p class="total-card-counter" id="totalCards"></p>
                <div class="board" id="board"></div>
            </div>
            <?php include_once 'assets/plugins/kanban/leads_kan-app.php'; ?>
        <?php } else { ?>
            <?php if (!empty($created) || !empty($edited)) {
                ?>
                <div class="btn-group pull-right btn-with-tooltip-group _filter_data filters" data-toggle="tooltip" data-title="<?= lang('filter_by'); ?>">
                    <button type="button" class="btn btn-default filters_button" aria-haspopup="true" aria-expanded="false" style="padding: 5.5px 20px;font-size: 20px;">
                        <i class="fa fa-filter" aria-hidden="true"></i>
                    </button>
                </div>
                <style>
                    .nav.nav-tabs > li.active > a{
                      background-color: #d19b15;
                      color:#fff;
                    }
                    .nav.nav-tabs > li:hover > a:hover{
                      background-color: #d6a93a;
                      color:#fff;
                    }
                    .sticky {
                      position: fixed;
                      top: 80px;
                      width: 16%;
                      right: 5px;
                      overflow: auto;
                      max-height: 80%;
                    }
                </style>
                <div class="nav-tabs-custom">
                <!-- Tabs within a box -->
                <ul class="nav nav-tabs">
                    <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('all_leads') ?></a></li>
                    <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create" data-toggle="tab"><?= $active == 2 ? lang('edit_lead') . (($leads_info != NULL && $leads_info != "") ? " (" . $leads_info->lead_name . ")" : "") : lang('new_leads'); ?></a></li>
                    <!-- <li><a href="<?= base_url() ?>admin/leads/import_leads"><?= lang('import_leads') ?></a></li> -->
                </ul>
                <div class="tab-content bg-white">
                <!-- ************** general *************-->
                <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <?php } else { ?>
                <div class="panel panel-custom">
                <header class="panel-heading ">
                    <div class="panel-title"><strong><?= lang('all_leads') ?></strong></div>
                </header>
            <?php } ?>
            <div class="table-responsive">
                <table class="table table-striped DataTables" id="DataTables" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th><?= lang('lead_name') ?></th>
                        <th><?= lang('email') ?></th>
                        <th><?= lang('phone') ?></th>
                        <th><?= lang('lead_source') ?></th>
                        <th><?= lang('lead_category') ?></th>
                        <th><?= lang('lead_status') ?></th>
                        <th><?= lang('assigned_to') ?></th>
                        <th class="col-options no-sort"><?= lang('action') ?></th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            </div>
            <?php if (!empty($created) || !empty($edited)) { ?>
            <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="create">
                <form role="form" enctype="multipart/form-data" data-parsley-validate="" novalidate="" action="<?php echo base_url(); ?>admin/leads/saved_leads/<?php
                      if (!empty($leads_info)) {
                          echo $leads_info->leads_id;
                      }
                      ?>" method="post" class="form-horizontal">
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-lg-2 control-label"><?= lang('lead_name') ?> <span class="text-danger">*</span></label>
                            <div class="col-lg-10">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                    <?php
                                    if (!empty($leads_info)) {
                                        $salutation = $leads_info->salutaiton;
                                    }
                                    ?>
                                        <span>
                                            <input required name="salutaiton" type="radio" id="salutaion_select" value="Mr" <?= ((isset($salutation) && $salutation == "Mr") ? "checked='checked'" : ""); ?>> Mr
                                            <input required name="salutaiton" type="radio" id="salutaion_select" value="Mrs" <?= ((isset($salutation) && $salutation == "Mrs") ? "checked='checked'" : ""); ?> style="margin-left:10px;"> Mrs
                                            <input required name="salutaiton" type="radio" id="salutaion_select" value="Miss" <?= ((isset($salutation) && $salutation == "Miss") ? "checked='checked'" : ""); ?> style="margin-left:10px;"> Miss
                                            <input required name="salutaiton" type="radio" id="salutaion_select" value="Ms" <?= ((isset($salutation) && $salutation == "Ms") ? "checked='checked'" : ""); ?> style="margin-left:10px;"> Ms
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" value="<?php
                                    if (!empty($leads_info)) {
                                        echo $leads_info->lead_name;
                                    }
                                    ?>" name="lead_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label"><?= lang('lead_source') ?> <span class="text-danger">*</span></label>
                            <div class="col-lg-4">
                                <div class="input-group">
                                    <select name="lead_source_id" class="form-control select_box" style="width: 100%" required>
                                        <option value disabled<?= (empty($leads_info) ? ' selected' : '') ?>><?= lang("select"); ?></option>
                                        <?php
                                        $lead_source_info = $this->db->order_by('lead_source_id', 'DESC')->get('tbl_lead_source')->result();
                                        if (!empty($lead_source_info)) {
                                            foreach ($lead_source_info as $v_lead_source) {
                                                ?>
                                                <option
                                                    value="<?= $v_lead_source->lead_source_id ?>" <?= (!empty($leads_info) && $leads_info->lead_source_id == $v_lead_source->lead_source_id ? 'selected' : '') ?>><?= $v_lead_source->lead_source ?></option>
                                                <?php
                                            }
                                        }
                                        $_created = can_action('128', 'created');
                                        ?>
                                    </select>
                                    <?php if (!empty($_created)) { ?>
                                        <div class="input-group-addon" title="<?= lang('new') . ' ' . lang('lead_source') ?>" data-toggle="tooltip" data-placement="top">
                                            <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/leads/lead_source"><i class="fa fa-plus"></i></a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <label class="col-lg-2 control-label"><?= lang('lead_status') ?> <span class="text-danger">*</span></label>
                            <div class="col-lg-4">
                                <div class="input-group">
                                    <select name="lead_status_id" class="form-control select_box" style="width: 100%" required>
                                        <option value disabled<?= (empty($leads_info) ? ' selected' : '') ?>><?= lang("select"); ?></option>
                                        <?php
                                        if (!empty($status_info)) {
                                            foreach ($status_info as $type => $v_leads_status) {
                                                if (!empty($v_leads_status)) {
                                                    ?>
                                                    <optgroup label="<?= lang($type) ?>">
                                                        <?php foreach ($v_leads_status as $v_l_status) { ?>
                                                            <option
                                                                value="<?= $v_l_status->lead_status_id ?>" <?php
                                                            if (!empty($leads_info->lead_status_id)) {
                                                                echo $v_l_status->lead_status_id == $leads_info->lead_status_id ? 'selected' : '';
                                                            }
                                                            ?>><?= $v_l_status->lead_status ?></option>
                                                        <?php } ?>
                                                    </optgroup>
                                                    <?php
                                                }
                                            }
                                        }
                                        $created = can_action('127', 'created');
                                        ?>
                                    </select>
                                    <?php if (!empty($created)) { ?>
                                        <div class="input-group-addon"
                                             title="<?= lang('new') . ' ' . lang('lead_status') ?>"
                                             data-toggle="tooltip" data-placement="top">
                                            <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/leads/lead_status"><i class="fa fa-plus"></i></a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label"><?= lang('lead_category') ?> <span class="text-danger">*</span></label>
                            <div class="col-lg-4">
                                <div class="input-group">
                                    <select name="lead_category_id" class="form-control select_box" style="width: 100%" required>
                                        <option value disabled<?= (empty($leads_info) ? ' selected' : '') ?>><?= lang("select"); ?></option>
                                        <?php
                                        $lead_category_info = $this->db->order_by('lead_category_id', 'DESC')->get('tbl_lead_category')->result();
                                        if (!empty($lead_category_info)) {
                                            foreach ($lead_category_info as $v_lead_category) {
                                                ?>
                                                <option value="<?= $v_lead_category->lead_category_id ?>" <?= (!empty($leads_info) && $leads_info->lead_category_id == $v_lead_category->lead_category_id ? 'selected' : '') ?>><?= $v_lead_category->lead_category ?></option>
                                                <?php
                                            }
                                        }
                                        $_created = can_action('128', 'created');
                                        ?>
                                    </select>
                                    <?php if (!empty($_created)) { ?>
                                        <div class="input-group-addon" title="<?= lang('new') . ' ' . lang('lead_category') ?>" data-toggle="tooltip" data-placement="top">
                                            <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/leads/lead_category"><i class="fa fa-plus"></i></a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <label class="col-lg-2 control-label"><?= lang('nationality') ?> <span class="text-danger">*</span></label>
                            <div class="col-lg-4">
                                      <select name="nationality" class="form-control select_box" style="width: 100%" required>
                                      <option value disabled<?= (empty($leads_info) ? ' selected' : '') ?>><?= lang("select"); ?></option>
                                        <?php
                                        if (!empty($nationalities)) {
                                            foreach ($nationalities as $v_leads_nationality) { ?>
                                                  <option value="<?= $v_leads_nationality->id ?>" <?php
                                                  if (!empty($leads_info->nationality)) {
                                                      echo $v_leads_nationality->id == $leads_info->nationality ? 'selected' : '';
                                                  }
                                                ?> style="<?= base_url("assets/img/flags/" . $v_leads_nationality->flag); ?>"><?= $v_leads_nationality->nationality ?></option>
                                              <?php
                                            }
                                        }
                                        $created = can_action('127', 'created');
                                        ?>
                                    </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label"><?= lang('lead_date_of_birth') ?> </label>
                            <div class="col-lg-4">
                                <div class="input-group">
                                  <input class="form-control datepicker" type="text" value="<?php
                                  if (!empty($leads_info)) {
                                      echo $leads_info->date_of_birth;
                                  }
                                  ?>" name="date_of_birth" data-date-format="mm-dd-yyyy" data-parsley-id="10">
                                  <div class="input-group-addon">
                                    <a href="#"><i class="fa fa-calendar"></i></a>
                                  </div>
                                </div>
                            </div>
                            <label class="col-lg-2 control-label"><?= lang('lead_passport') ?> </label>
                            <div class="col-lg-4">
                              <div class="input-group" style="width:100%;">
                                  <input type="text" class="form-control" name="passport_number" value="<?php
                                  if (!empty($leads_info)) {
                                      echo $leads_info->passport_number;
                                  }
                                  ?>"/>
                                  <div class="input-group-addon" style="padding:0;border:0;width:30%;" data-toggle="tooltip" data-placement="top" data-original-title="Passport Expire">
                                        <div class="input-group">
                                          <input class="form-control datepicker" type="text" value="<?php
                                          if (!empty($leads_info)) {
                                              echo $leads_info->passport_expire;
                                          }
                                          ?>" name="passport_expire" data-date-format="mm-dd-yyyy" data-parsley-id="10">
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
                                <input type="text" class="form-control" value="<?php
                                if (!empty($leads_info)) {
                                    echo $leads_info->organization;
                                }
                                ?>" name="organization">
                            </div>
                            <label class="col-lg-2 control-label"><?= lang('address') ?> </label>
                            <div class="col-lg-4">
                              <input type="text" class="form-control" value="<?php
                                  if (!empty($leads_info)) {
                                      echo $leads_info->address;
                                  }
                                  ?>" name="address">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label"><?= lang('contact_name') ?> <span class="text-danger">*</span></label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" value="<?php
                                if (!empty($leads_info)) {
                                    echo $leads_info->contact_name;
                                }
                                ?>" name="contact_name" required>
                            </div>
                            <label class="col-lg-2 control-label"><?= lang('city') ?> </label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" value="<?php
                                if (!empty($leads_info)) {
                                    echo $leads_info->city;
                                }
                                ?>" name="city">
                            </div>
                        </div>
                        <!-- End discount Fields -->
                        <div class="form-group">
                            <label class="col-lg-2 control-label"><?= lang('email') ?> <span class="text-danger">*</span></label>
                            <div class="col-lg-4">
                                <input type="email" class="form-control" value="<?php
                                if (!empty($leads_info)) {
                                    echo $leads_info->email;
                                }
                                ?>" name="email" required>
                            </div>
                            <label class="col-lg-2 control-label"><?= lang('state') ?> </label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" value="<?php
                                if (!empty($leads_info)) {
                                    echo $leads_info->state;
                                }
                                ?>" name="state">
                            </div>
                        </div>
                        <div class="form-group">
                          <label class="col-lg-2 control-label"><?= lang('email_2') ?></label>
                          <div class="col-lg-4">
                              <input type="email" class="form-control" value="<?php
                              if (!empty($leads_info)) {
                                  echo $leads_info->email2;
                              }
                              ?>" name="email2">
                          </div>
                          <label class="col-lg-2 control-label"><?= lang('country') ?></label>
                          <div class="col-lg-4">
                              <select name="country" class="form-control person select_box" style="width: 100%">
                                  <optgroup label="Default Country">
                                      <?php if (!empty($leads_info->country)) { ?>
                                          <option
                                              value="<?= $leads_info->country ?>"><?= $leads_info->country ?></option>
                                      <?php } else { ?>
                                          <option
                                              value="<?= $this->config->item('company_country') ?>"><?= $this->config->item('company_country') ?></option>
                                      <?php } ?>
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
                                <input type="email" class="form-control" value="<?php
                                if (!empty($leads_info)) {
                                    echo $leads_info->email3;
                                }
                                ?>" name="email3">
                            </div>
                            <label class="col-lg-2 control-label"><?= lang('landline') ?></label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" value="<?php
                                if (!empty($leads_info)) {
                                    echo $leads_info->phone;
                                }
                                ?>" name="phone">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label"><?= lang('fax') ?> </label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" value="<?php
                                if (!empty($leads_info)) {
                                    echo $leads_info->fax;
                                }
                                ?>" name="fax">
                            </div>
                            <label class="col-lg-2 control-label"><?= lang('phone') ?><span class="text-danger">*</span></label>
                            <div class="col-lg-4">
                                <input type="text" required class="form-control" value="<?php
                                if (!empty($leads_info)) {
                                    echo $leads_info->mobile;
                                }
                                ?>" name="mobile"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label"><?= lang('skype_id') ?> </label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" value="<?php
                                if (!empty($leads_info)) {
                                    echo $leads_info->skype;
                                }
                                ?>" name="skype">
                            </div>
                            <label class="col-lg-2 control-label"><?= lang('phone_2') ?></label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" value="<?php
                                if (!empty($leads_info)) {
                                    echo $leads_info->phone2;
                                }
                                ?>" name="phone2">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label"><?= lang('twitter_profile_link') ?> </label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" value="<?php
                                if (!empty($leads_info)) {
                                    echo $leads_info->twitter;
                                }
                                ?>" name="twitter">
                            </div>
                            <label class="col-lg-2 control-label"><?= lang('phone_3') ?></label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" value="<?php
                                if (!empty($leads_info)) {
                                    echo $leads_info->phone3;
                                }
                                ?>" name="phone3">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label"><?= lang('facebook_profile_link') ?> </label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" value="<?php
                                if (!empty($leads_info)) {
                                    echo $leads_info->facebook;
                                }
                                ?>" name="facebook">
                            </div>
                            <label class="col-lg-2 control-label"><?= lang('phone_4') ?></label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" value="<?php
                                if (!empty($leads_info)) {
                                    echo $leads_info->phone4;
                                }
                                ?>" name="phone4">
                            </div>
                        </div>
                        <div class="form-group" id="border-none">
                            <label class="col-lg-2 control-label"><?= lang('short_note') ?> </label>
                            <div class="col-lg-10">
                            <textarea name="notes" class="form-control textarea" rows="4"><?php
                                if (!empty($leads_info)) {
                                    echo $leads_info->notes;
                                }
                                ?></textarea>
                            </div>
                        </div>
                        <?php
                        if (!empty($leads_info)) {
                            $leads_id = $leads_info->leads_id;
                        } else {
                            $leads_id = null;
                        }
                        ?>
                        <div class="form-group" id="border-none">
                            <label for="field-1" class="col-sm-2 control-label"><?= lang('assined_to') ?>
                                <span class="required">*</span></label>
                            <div class="col-sm-9">
                                <div class="checkbox c-radio needsclick">
                                    <label class="needsclick">
                                        <input id="" <?php
                                        if (!empty($leads_info->permission) && $leads_info->permission == 'all') {
                                            echo 'checked';
                                        } elseif (empty($leads_info)) {
                                            echo 'checked';
                                        }
                                        ?> type="radio" name="permission" value="everyone">
                                        <span class="fa fa-circle"></span><?= lang('everyone') ?>
                                        <i title="<?= lang('permission_for_all') ?>"
                                           class="fa fa-question-circle" data-toggle="tooltip"
                                           data-placement="top"></i>
                                    </label>
                                </div>
                                <div class="checkbox c-radio needsclick">
                                    <label class="needsclick">
                                        <input id="" <?php
                                        if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                                            echo 'checked';
                                        }
                                        ?> type="radio" name="permission" value="custom_permission"
                                        >
                                        <span class="fa fa-circle"></span><?= lang('custom_permission') ?>
                                        <i title="<?= lang('permission_for_customization') ?>" class="fa fa-question-circle" data-toggle="tooltip" data-placement="top"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group <?php
                        if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                            echo 'show';
                        }
                        ?>" id="permission_user_1">
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
                                                  <span class="fa fa-check"></span><?= $this->db->where('user_id', $v_user->user_id)->get('tbl_account_details')->row()->fullname . ' ' . $role ?>
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
                                        ?> " id="action_1<?= $v_user->user_id ?>">
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
                                                    ?>
                                                     type="checkbox"
                                                     value="edit" name="action_<?= $v_user->user_id ?>[]">
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
                        <?php if (empty($leads_info->converted_client_id) || $leads_info->converted_client_id == 0) { ?>
                            <div class="btn-bottom-toolbar text-right">
                                <?php
                                if (!empty($leads_info)) { ?>
                                    <button type="submit" class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                    <button type="button" onclick="goBack()" class="btn btn-sm btn-danger"><?= lang('cancel') ?></button>
                                <?php } else {
                                    ?>
                                    <button type="submit" class="btn btn-sm btn-primary"><?= lang('save') ?></button>
                                <?php }
                                ?>
                            </div>
                        <?php } ?>
                </form>
            </div>
        <?php } else { ?>
            </div>
        <?php } ?>
            </div>
            </div>
        <?php } ?>
    </div>
</div>
  <div id="filer_sideBar" class="filter_sidebar" style="display:none;">
          <form action="javascript:;" id="filter_leads" method="post" accept-charset="utf-8">
            <div class="filter_sidebar_content">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group-feedback form-group-feedback-right">
                            <input type="search" class="form-control filter_input" name="filter_full_name" placeholder="SEARCH BY NAME:">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group-feedback form-group-feedback-right">
                            <input type="search" class="form-control filter_input" name="filter_email" placeholder="SEARCH BY E-MAIL:">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group-feedback form-group-feedback-right">
                            <input type="search" class="form-control filter_input" name="filter_mobile" placeholder="SEARCH BY PHONE:">
                        </div>
                    </div>
                    <div class="card-header bg-transparent header-elements-inline">
                        <span class="text-uppercase font-size-sm font-weight-semibold">Filters</span>
                    </div>
                    <div class="card-body">
                            <div class="form-group">
                              <div class="checkbox c-checkbox">
                                 <label class="needsclick">
                                    <input type="checkbox" value="Yes" name="filter_own_property" data-parsley-multiple="allow_authorize" class="filter_input" data-parsley-id="36"><span class="fa fa-check"></span>
                                 </label>Own Property
                              </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <select data-placeholder="ASSIGNED TO" title="ASSIGNED TO" name="filter_assigned_to" class="form-control select_box filter_input" style="width:100%;">
                                    <option value disabled selected>ASSIGNED TO</option>
                                      <?php foreach ($assign_user as $staff){
                                        if($staff->username != "admin"){
                                        $staff_details = $this->db->where('user_id', $staff->user_id)->get('tbl_account_details')->row();
                                        echo '<option value="' . $staff->user_id . '">' . $staff_details->fullname . '</option>';
                                      }
                                      } ?>
                              </select>
                            </div>
                            <div class="form-group">
                                <div class="mb-4">
                                  <select data-placeholder="CATEGORY" title="CATEGORY" name="filter_category" class="form-control select_box filter_input" style="width:100%;">
                                      <option value disabled selected>CATEGORY</option>
                                        <?php
                                        $lead_category_info = $this->db->order_by('lead_category_id', 'DESC')->get('tbl_lead_category')->result();
                                        foreach ($lead_category_info as $category){
                                          echo '<option value="' . $category->lead_category_id . '">' . $category->lead_category . '</option>';
                                        } ?>
                                </select>
                              </div>
                            </div>
                            <div class="form-group">
                                <div class="mb-4">
                                  <select data-placeholder="SOURCE" title="SOURCE" name="filter_source" class="form-control select_box filter_input" style="width:100%;">
                                      <option value disabled selected>SOURCE</option>
                                        <?php
                                        $lead_source_info = $this->db->order_by('lead_source_id', 'DESC')->get('tbl_lead_source')->result();
                                        foreach ($lead_source_info as $source){
                                          echo '<option value="' . $source->lead_source_id . '">' . $source->lead_source . '</option>';
                                        } ?>
                                </select>
                              </div>
                            </div>
                            <div class="form-group">
                                <div class="mb-4">
                                  <select data-placeholder="NATIONALITY"  title="NATIONALITY" name="filter_nationality" class="form-control select_box filter_input" style="width:100%;">
                                      <option value disabled selected>NATIONALITY</option>
                                        <?php foreach ($nationalities as $nationality) {
                                              echo '<option value="' . $nationality->id . '">' . $nationality->nationality .' (' . $nationality->long_name .')</option>';
                                        } ?>
                                </select>
                              </div>
                            </div>
                            <div class="form-group">
                                <div class="mb-4">
                                  <select data-placeholder="COUNTRY" title="COUNTRY" name="filter_nationality" class="form-control select_box filter_input" style="width:100%;">
                                      <option value disabled selected>COUNTRY</option>
                                        <?php foreach ($nationalities as $countries) {
                                              echo '<option value="' . $countries->id . '">' . $countries->long_name .'</option>';
                                        } ?>
                                </select>
                              </div>
                            </div>
                            <div class="form-group">
                                <div class="mb-4">
                                    <select data-placeholder="COMMUNITY" title="COMMUNITY" name="filter_community" class="form-control select_box filter_input" style="width:100%;">
                                      <option value disabled selected>COMMUNITY</option>
                                        <?php
                                        $data = $this->db->where("`prop_community` IS NOT NULL AND `prop_community` != ''")->get("tbl_properties");
                                        $existArray = array();
                                        foreach ($data->result() as $ddata) {
                                            if (!(in_array($ddata->prop_community, $existArray))) {
                                                array_push($existArray, $ddata->prop_community);
                                                echo "<option value='" . $ddata->prop_community . "'>" . $ddata->prop_community . "</option>";
                                            }
                                        }
                                         ?>
                                    </select>
                                  </div>
                            </div>
                            <div class="form-group">
                                <div class="mb-4">
                                  <select data-placeholder="SUB COMMUNITY" title="SUB COMMUNITY" name="filter_sub_community" class="form-control select_box filter_input" style="width:100%;">
                                    <option value disabled selected>SUB COMMUNITY</option>
                                      <?php
                                      $data = $this->db->where("`prop_sub_community` IS NOT NULL AND `prop_sub_community` != ''")->get("tbl_properties");
                                      $existArray = array();
                                      foreach ($data->result() as $ddata) {
                                          if (!(in_array($ddata->prop_sub_community, $existArray))) {
                                              array_push($existArray, $ddata->prop_sub_community);
                                              echo "<option value='" . $ddata->prop_sub_community . "'>" . $ddata->prop_sub_community . "</option>";
                                          }
                                      }
                                       ?>
                                  </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="mb-4" style="float: right !important;margin-bottom: 20px !important;">
                                    <button type="reset"  class="btn btn-secondary legitRipple">RESET</button>
                                    <button type="button" class="btn btn-primary go_filter legitRipple">GO</button>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
<script>
function leadFastView(lead_id, row_id){
  $.ajax({
    method: "POST",
    url: "<?= base_url("admin/leads/lead_fast_view/"); ?>",
    data: { lead_id: lead_id},
    success: function(html){
      $(".lead_fast_view").remove();
      $("#lead_fast_view").remove();
      $('.close_quick_view').each(function (){
        $(this).removeClass("close_quick_view").removeClass("btn-danger").addClass("btn-info").attr("title", "View");
        $(this).attr("onclick", "leadFastView(" + $(this).attr("data-lead-id") + "," + $(this).attr("data-lead") + ");").find("span").removeClass("fa-close").addClass("fa-eye");
      });
      $(".lead_view_" + lead_id).addClass("close_quick_view").removeClass("btn-info").addClass("btn-danger").attr("title", "Close View");
      $(".lead_view_" + lead_id).attr("onclick", "closeQuickView(" + lead_id + "," + row_id + ");").find("span").removeClass("fa-eye").addClass("fa-close");
      $('table > tbody > tr').eq(row_id).after(html);
    }
  });
}
function closeQuickView(lead_id, row_id){
  $(".lead_fast_view").remove();
  $("#lead_fast_view").remove();
  $(".lead_view_" + lead_id).removeClass("close_quick_view").removeClass("btn-danger").addClass("btn-info").attr("title", "View");
  $(".lead_view_" + lead_id).attr("onclick", "leadFastView(" + lead_id + "," + row_id + ");").find("span").removeClass("fa-close").addClass("fa-eye");
}
$(".filters").click(function (){
  $("#leads_data").toggleClass("col-sm-12").toggleClass("col-sm-10");
  $("body").toggleClass("aside-collapsed");
  $("#filer_sideBar").toggle();
  if($("#filer_sideBar").is(":hidden")){
    $(".filters_button").css("color", "");
    $(".filters_button").css("background", "");
  }else{
    $(".filters_button").css("color", "white");
    $(".filters_button").css("background", "#d19b15");
  }
});
$(document).ready(function () {
    filtering = "filter_leads";

    list = base_url + "admin/leads/leadList";

    $(".go_filter").click(function (){
      table_url(list);
    });
    window.onscroll = function() {stickyFunction()};

    var filter_header = document.getElementById("filer_sideBar");
    var sticky = filter_header.offsetTop;

    function stickyFunction() {
      if (window.pageYOffset > sticky+20) {
        filter_header.classList.add("sticky");
      } else {
        filter_header.classList.remove("sticky");
      }
    }
});

</script>
