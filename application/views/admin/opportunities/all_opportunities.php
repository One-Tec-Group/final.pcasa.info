<?php
echo message_box('success');
echo message_box('error');
$created = can_action('56', 'created');
$edited = can_action('56', 'edited');
$deleted = can_action('56', 'deleted');
if (!empty($created) || !empty($edited)) {
    ?>
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('all_opportunities') ?></a></li>
            <?php if (!empty($opportunity_info)) { ?>
                <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#edit_opportunity" data-toggle="tab"><?= lang('edit_opportunities') . " (" . $opportunity_info->opportunity_name . ")"; ?></a></li>
            <?php } ?>
        </ul>
        <div class="tab-content bg-white">
            <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
            <?php } else { ?>
                <div class="panel panel-custom">
                    <header class="panel-heading ">
                        <div class="panel-title"><strong><?= lang('all_opportunities') ?></strong></div>
                    </header>
                <?php } ?>
                <div class="table-responsive">
                    <table class="table table-striped DataTables " id="DataTables" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><?= lang('#') ?></th>
                                <th><?= lang('opportunity_name') ?></th>
                                <th><?= lang('stages') ?></th>
                                <th><?= lang('state') ?></th>
                                <th><?= lang('phone') ?></th>
                                <th><?= lang('nationality') ?></th>
                                <th><?= lang('converted_by') ?></th>
                                <th><?= lang('assigned_to') ?></th>
                                <?php
                                $show_custom_fields = custom_form_table(8, null);
                                if (!empty($show_custom_fields)) {
                                    foreach ($show_custom_fields as $c_label => $v_fields) {
                                        if (!empty($c_label)) {
                                            ?>
                                            <th><?= $c_label ?> </th>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                                <th class="col-options no-sort"><?= lang('action') ?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <script type="text/javascript">
                            $(document).ready(function () {
                                list = base_url + "admin/opportunities/opportunitiesList";
                            });
                        </script>
                    </table>
                </div>
            </div>
            <?php if (!empty($opportunity_info)) { ?>
                <?php if (!empty($created) || !empty($edited)) { ?>
                    <div class="tab-pane <?= $active == 2 ? 'active' : ''; ?>" id="edit_opportunity">
                        <form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data" id="form" action="<?php echo base_url(); ?>admin/opportunities/saved_opportunity/<?= $opportunity_info->opportunities_id; ?>" method="post" class="form-horizontal  ">
                            <input type="hidden" name="opportunity_lead_id" value="<?= $opportunity_info->opportunity_lead_id; ?>"/>
                            <input type="hidden" name="opportunity_name" id="opportunity_name" value="<?= $opportunity_info->opportunity_name; ?>"/>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-lg-2 control-label"><?= lang('opportunity_name') ?></label>
                                    <div class="col-lg-4">
                                        <input type="text" id="opportunity_name_view" class="form-control" value="<?= $opportunity_info->opportunity_name; ?>" readonly disabled>
                                    </div>
                                    <label class="col-lg-2 control-label"><?= lang('stages') ?> </label>
                                    <div class="col-lg-4">
                                        <select name="stages" class="form-control select_box" style="width: 100%;" required="">
                                            <option value="new"<?= ($opportunity_info->stages == 'new' ? ' selected' : '') ?>><?= lang('new') ?></option>
                                            <option value="qualification"<?= ($opportunity_info->stages == 'qualification' ? ' selected' : '') ?>><?= lang('qualification') ?></option>
                                            <option value="proposition"<?= ($opportunity_info->stages == 'proposition' ? ' selected' : '') ?>><?= lang('proposition') ?></option>
                                            <option value="won"<?= ($opportunity_info->stages == 'won' ? ' selected' : '') ?>><?= lang('won') ?></option>
                                            <option value="lost"<?= ($opportunity_info->stages == 'lost' ? ' selected' : '') ?>><?= lang('lost') ?></option>
                                            <option value="dead"<?= ($opportunity_info->stages == 'dead' ? ' selected' : '') ?>><?= lang('dead') ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label"><?= lang('probability') ?> %</label>
                                    <div class="col-lg-4">
                                        <input name="probability" data-ui-slider="" type="text" value="<?= $opportunity_info->probability; ?>" data-slider-min="0" data-slider-max="100" data-slider-step="1"
                                               data-slider-value="<?= $opportunity_info->probability; ?>" data-slider-orientation="horizontal" class="slider slider-horizontal" data-slider-id="red">
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-2 control-label"><?= lang('close_date') ?></label>
                                        <?php
                                        $close_date = date('Y-m-d', strtotime($opportunity_info->close_date));
                                        $next_action_date = date('Y-m-d', strtotime($opportunity_info->next_action_date));
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
                                </div>
                                <div class="form-group" id="border-none">
                                    <label for="field-1" class="col-sm-2 control-label"><?= lang('current_state') ?> <span class="required">*</span></label>
                                    <div class="col-sm-4">
                                        <div class="input-group">
                                            <select name="opportunities_state_reason_id" style="width: 100%" class="select_box" required="">
                                                <?php
                                                if (!empty($all_state)) {
                                                    foreach ($all_state as $state => $opportunities_state) {
                                                        if (!empty($state)) {
                                                            ?>
                                                            <optgroup label="<?= lang($state) ?>">
                                                                <?php foreach ($opportunities_state as $v_state) { ?>
                                                                    <option value="<?= $v_state->opportunities_state_reason_id ?>" <?php
                                                                    if (!empty($opportunity_info->opportunities_state_reason_id)) {
                                                                        echo $v_state->opportunities_state_reason_id == $opportunity_info->opportunities_state_reason_id ? 'selected' : '';
                                                                    }
                                                                    ?>><?= $v_state->opportunities_state_reason ?></option>
                                                                        <?php } ?>
                                                            </optgroup>
                                                            <?php
                                                        }
                                                    }
                                                }
                                                $created = can_action('129', 'created');
                                                ?>
                                            </select>
                                            <?php if (!empty($created)) { ?>
                                                <div class="input-group-addon" title="<?= lang('new') . ' ' . lang('opportunities_state_reason') ?>" data-toggle="tooltip" data-placement="top">
                                                    <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/opportunities/opportunities_state_reason"><i class="fa fa-plus"></i></a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label"><?= lang('expected_revenue') ?></label>
                                    <div class="col-lg-4">
                                        <input type="text" data-parsley-type="number" min="0" class="form-control" value="<?= $opportunity_info->expected_revenue; ?>" name="expected_revenue">
                                    </div>
                                    <label class="col-lg-2 control-label"><?= lang('new_link') ?></label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" value="<?= $opportunity_info->new_link; ?>" name="new_link"/>
                                    </div>
                                </div>
                                <div class="form-group terms">
                                    <label class="col-lg-2 control-label"><?= lang('next_action') ?> </label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" value="<?= $opportunity_info->next_action; ?>" name="next_action">
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
                                        <textarea name="notes" class="form-control textarea"><?= $opportunity_info->notes; ?></textarea>
                                    </div>
                                </div>
                                <?= custom_form_Fields(8, $opportunity_info->opportunities_id, true); ?>
                                <?= $opportunity_info->opportunity_lead_id; ?>
                                <hr/>

                                <?php $leads_info = $this->items_model->check_by(array('leads_id' => $opportunity_info->opportunity_lead_id), 'tbl_leads'); ?>
                                <div class="form-group">
                                    <label class="col-lg-2 control-label"><?= lang('lead_name') ?> <span
                                            class="text-danger">*</span></label>
                                    <div class="col-lg-10">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                            <?php
                                            if (!empty($leads_info)) {
                                                $salutation = $leads_info->salutaiton;
                                            }
                                            ?>
                                                <span>
                                                    <input name="salutaiton" type="radio" id="salutaion_select" value="Mr" <?= ((isset($salutation) && $salutation == "Mr") ? "checked='checked'" : ""); ?>> Mr
                                                    <input name="salutaiton" type="radio" id="salutaion_select" value="Mrs" <?= ((isset($salutation) && $salutation == "Mrs") ? "checked='checked'" : ""); ?> style="margin-left:10px;"> Mrs
                                                    <input name="salutaiton" type="radio" id="salutaion_select" value="Miss" <?= ((isset($salutation) && $salutation == "Miss") ? "checked='checked'" : ""); ?> style="margin-left:10px;"> Miss
                                                    <input name="salutaiton" type="radio" id="salutaion_select" value="Ms" <?= ((isset($salutation) && $salutation == "Ms") ? "checked='checked'" : ""); ?> style="margin-left:10px;"> Ms
                                                </span>
                                            </div>
                                            <input type="text" id="laed_name" class="form-control" value="<?php
                                            if (!empty($leads_info)) {
                                                echo $leads_info->lead_name;
                                            }
                                            ?>" name="lead_name" required="">
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
                                    <label class="col-lg-2 control-label"><?= lang('lead_category') ?> </label>
                                    <div class="col-lg-4">
                                        <div class="input-group">
                                            <select name="lead_category_id" class="form-control select_box" style="width: 100%" required="">
                                                <?php
                                                $lead_category_info = $this->db->order_by('lead_category_id', 'DESC')->get('tbl_lead_category')->result();
                                                if (!empty($lead_category_info)) {
                                                    foreach ($lead_category_info as $v_lead_category) {
                                                        ?>
                                                        <option
                                                            value="<?= $v_lead_category->lead_category_id ?>" <?= (!empty($leads_info) && $leads_info->lead_category_id == $v_lead_category->lead_category_id ? 'selected' : '') ?>><?= $v_lead_category->lead_category ?></option>
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
                                    <label class="col-lg-2 control-label"><?= lang('nationality') ?> </label>
                                    <div class="col-lg-4">
                                              <select name="nationality" class="form-control select_box" style="width: 100%" required="">
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
                                        ?>" name="contact_name" required="">
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
                                <div class="form-group">
                                    <label class="col-lg-2 control-label"><?= lang('email') ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-4">
                                        <input type="email" class="form-control" value="<?php
                                        if (!empty($leads_info)) {
                                            echo $leads_info->email;
                                        }
                                        ?>" name="email" required="">
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
                                        <input type="text" required="" class="form-control" value="<?php
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
                                    <textarea name="lead_notes" class="form-control textarea" rows="4"><?php
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
                                <input type="hidden" name="permission" value="custom_permission">
                                <div class="form-group <?php
                                if (!empty($opportunity_info->permission) && $opportunity_info->permission != 'all') {
                                    echo 'show';
                                }
                                ?>" id="permission_user_1">
                                    <label for="field-1" class="col-sm-2 control-label"><?= lang('who_responsible') ?>
                                        <span class="required">*</span></label>
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
                                                            <input type="checkbox"
                                                            <?php
                                                            if (!empty($opportunity_info->permission) && $opportunity_info->permission != 'all') {
                                                                $get_permission = json_decode($opportunity_info->permission);
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
                                                    if (!empty($opportunity_info->permission) && $opportunity_info->permission != 'all') {
                                                        $get_permission = json_decode($opportunity_info->permission);
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
                                                            <input <?php
                                                            if (!empty($disable)) {
                                                                echo 'disabled' . ' ' . 'checked';
                                                            }
                                                            ?> id="<?= $v_user->user_id ?>"
                                                                <?php
                                                                if (!empty($opportunity_info->permission) && $opportunity_info->permission != 'all') {
                                                                    $get_permission = json_decode($opportunity_info->permission);
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
                                                            <input <?php
                                                            if (!empty($disable)) {
                                                                echo 'disabled' . ' ' . 'checked';
                                                            }
                                                            ?> id="<?= $v_user->user_id ?>"
                                                                <?php
                                                                if (!empty($opportunity_info->permission) && $opportunity_info->permission != 'all') {
                                                                    $get_permission = json_decode($opportunity_info->permission);
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
                                <div class="btn-bottom-toolbar text-right">
                                    <button type="submit" class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                    <button type="button" onclick="goBack()" class="btn btn-sm btn-danger"><?= lang('cancel') ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php } else { ?>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>
