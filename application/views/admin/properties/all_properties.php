<?= message_box('success'); ?>
<?=
message_box('error');
$created = can_action('156', 'created');
$edited = can_action('156', 'edited');
$deleted = can_action('156', 'deleted');
$uri_segment = $this->uri->segment(4);
?>
<div class="row">
    <div class="col-lg-2 pl-lg">
        <div class="panel widget">
            <div class="pl-sm pr-sm pb-sm">
                <strong><a style="font-size: 15px" class="p_status go_filter" data-filter="fast_click" data-input="view_by" data-filter-value="all" href="javascript:;"><?= lang("property_uncategorized"); ?></a>
                    <small class="pull-right " style="padding-top: 2px"> <?= number_format($properties_uncategorized); ?> / <a style="color:#656565;" class="go_filter" data-filter="fast_click" data-input="view_by" href="javascript:;"><?= number_format($properties_count); ?></a></small>
                </strong>
                <div class="progress progress-striped progress-xs mb-sm">
                    <div class="progress-bar progress-bar-primary" data-toggle="tooltip" data-original-title="<?= (($properties_uncategorized * 100) / $properties_count); ?>%" style="width:<?= (($properties_uncategorized * 100) / $properties_count); ?>%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-2">
        <div class="panel widget">
            <div class="pl-sm pr-sm pb-sm">
                <strong><a style="font-size: 15px" class="p_status go_filter" data-filter="fast_click" data-input="view_by" data-filter-value="live" href="javascript:;"><?= lang("property_live"); ?></a>
                    <small class="pull-right " style="padding-top: 2px"> <?= number_format($properties_live_count); ?> / <a style="color:#656565;" class="go_filter" data-filter="fast_click" data-input="view_by" href="javascript:;"><?= number_format($properties_count); ?></a></small>
                </strong>
                <div class="progress progress-striped progress-xs mb-sm">
                    <div class="progress-bar progress-bar-success " data-toggle="tooltip" data-original-title="<?= (($properties_live_count * 100) / $properties_count); ?>%" style="width:<?= (($properties_live_count * 100) / $properties_count); ?>%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-2 pl-lg">
        <div class="panel widget">
            <div class="pl-sm pr-sm pb-sm">
                <strong><a style="font-size: 15px" class="p_status go_filter" data-input="view_by" href="javascript:;"><?= lang("property_in_review"); ?></a>
                    <small class="pull-right " style="padding-top: 2px"> <?= number_format($properties_in_review); ?> / <a style="color:#656565;" class="go_filter" data-filter="fast_click" data-input="view_by" href="javascript:;"><?= number_format($properties_count); ?></a></small>
                </strong>
                <div class="progress progress-striped progress-xs mb-sm">
                    <div class="progress-bar progress-bar-primary" data-toggle="tooltip" data-original-title=""<?= (($properties_in_review * 100) / $properties_count); ?>%" style="width: "<?= (($properties_in_review * 100) / $properties_count); ?>%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-2">
        <div class="panel widget">
            <div class="pl-sm pr-sm pb-sm">
                <strong><a style="font-size: 15px" class="p_status go_filter" data-filter="fast_click" data-input="view_by" data-filter-value="unpuplished" href="javascript:;"><?= lang("property_unpublished"); ?></a>
                    <small class="pull-right " style="padding-top: 2px"> <?= number_format($properties_unpublished_count); ?> / <a style="color:#656565;" class="go_filter" data-filter="fast_click" data-input="view_by" href="javascript:;"><?= number_format($properties_count); ?></a></small>
                </strong>
                <div class="progress progress-striped progress-xs mb-sm">
                    <div class="progress-bar progress-bar-success " data-toggle="tooltip" data-original-title="<?= (($properties_unpublished_count * 100) / $properties_count); ?>%" style="width:<?= (($properties_unpublished_count * 100) / $properties_count); ?>%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-2">
        <div class="panel widget">
            <div class="pl-sm pr-sm pb-sm">
                <strong><a style="font-size: 15px" class="p_status go_filter" data-filter="fast_click" data-input="view_by" data-filter-value="draft" href="javascript:;"><?= lang("property_draft"); ?></a>
                    <small class="pull-right " style="padding-top: 2px"> <?= number_format($properties_draft_count); ?> / <a style="color:#656565;" class="go_filter" data-filter="fast_click" data-input="view_by" href="javascript:;"><?= number_format($properties_count); ?></a></small>
                </strong>
                <div class="progress progress-striped progress-xs mb-sm">
                    <div class="progress-bar progress-bar-warning " data-toggle="tooltip" data-original-title="<?= (($properties_draft_count * 100) / $properties_count); ?>%" style="width:<?= (($properties_draft_count * 100) / $properties_count); ?>%"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-2">
        <div class="panel widget">
            <div class="pl-sm pr-sm pb-sm">
                <strong><a style="font-size: 15px" class="p_status go_filter" data-filter="fast_click" data-input="view_by" data-filter-value="archive" href="javascript:;"><?= lang("property_archive"); ?></a>
                    <small class="pull-right " style="padding-top: 2px"> <?= number_format($properties_archive_count); ?> / <a style="color:#656565;" class="go_filter" data-filter="fast_click" data-input="view_by" href="javascript:;"><?= number_format($properties_count); ?></a></small>
                </strong>
                <div class="progress progress-striped progress-xs mb-sm">
                    <div class="progress-bar progress-bar-danger " data-toggle="tooltip" data-original-title="<?= (($properties_archive_count * 100) / $properties_count); ?>%" style="width:<?= (($properties_archive_count * 100) / $properties_count); ?>%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div id="properties_data" class="col-sm-12">
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
                input::-webkit-outer-spin-button,
                input::-webkit-inner-spin-button {
                    -webkit-appearance: none;
                    margin: 0;
                    padding: 5px;
                }
                input[type=number] {
                    -moz-appearance:textfield;
                    padding: 5px;
                }
                .note-editable{
                  height: 100px;
                }
            </style>
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="<?= $active == 1 ? 'active' : ''; ?>"><a href="#manage" data-toggle="tab"><?= lang('all_properties') ?></a></li>
                    <li class="<?= $active == 2 ? 'active' : ''; ?>"><a href="#create" data-toggle="tab"><?= $active == 2 ? lang('edit_property') . (($property_info != NULL && $property_info != "") ? " (" . $property_info->prop_title . ")" : "") : lang('new_property'); ?></a></li>
                    <!-- <li><a href="<?= base_url() ?>admin/properies/import_properties"><?= lang('import_properties') ?></a></li> -->
                </ul>
                <div class="tab-content bg-white">
                    <div class="tab-pane <?= $active == 1 ? 'active' : ''; ?>" id="manage">
                    <?php } else { ?>
                        <div class="panel panel-custom">
                            <header class="panel-heading ">
                                <div class="panel-title"><strong><?= lang('all_properties') ?></strong></div>
                            </header>
                        <?php } ?>
                        <div class="table-responsive">
                            <table class="table table-striped DataTables" id="DataTables" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th><?= lang('prop_reference') ?></th>
                                        <th><?= lang('prop_purpose') ?></th>
                                        <th><?= lang('prop_type') ?></th>
                                        <th><?= lang('bedrooms') ?></th>
                                        <th><?= lang('prop_location') ?></th>
                                        <th><?= lang('property_area_sqft') ?></th>
                                        <th><?= lang('price') ?></th>
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
                            <form role="form" enctype="multipart/form-data" data-parsley-validate="" novalidate="" action="<?php echo base_url(); ?>admin/properties/saved_property/<?php
                            if (!empty($property_info)) {
                                echo $property_info->prop_id;
                            }
                            ?>" method="post" class="form-horizontal">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="col-lg-1 control-label"><?= lang('property_type') ?>: <span class="text-danger">*</span></label>
                                        <div class="col-lg-3">
                                            <div class="input-group">
                                                <select name="property_type_id" class="form-control select_box" style="width: 100%" required>
                                                    <option value selected disabled><?= lang("select"); ?></option>
                                                    <?php
                                                    $proeprty_type_info = $this->db->order_by('property_type_id', 'DESC')->get('tbl_properties_types')->result();
                                                    if (!empty($proeprty_type_info)) {
                                                        foreach ($proeprty_type_info as $v_property_type) {
                                                            ?>
                                                            <option value="<?= $v_property_type->property_type_id ?>" <?= (!empty($property_info) && $property_info->property_type_id == $v_property_type->property_type_id ? 'selected' : '') ?>><?= $v_property_type->property_type ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    $_created = can_action('159', 'created');
                                                    ?>
                                                </select>
                                                <?php if (!empty($_created)) { ?>
                                                    <div class="input-group-addon" title="<?= lang('new') . ' ' . lang('property_type') ?>" data-toggle="tooltip" data-placement="top">
                                                        <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/properties/property_type"><i class="fa fa-plus"></i></a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <label class="col-lg-1 control-label"><?= lang('property_beds') ?>: </label>
                                        <div class="col-lg-1">
                                            <select name="prop_bedrooms" class="form-control select_box">
                                              <option value disabled selected><?= lang("select"); ?></option>
                                              <option value="1"<?= ((!empty($property_info) && $property_info->prop_bedrooms == "1") ? " selected" : ""); ?>>1</option>
                                              <option value="2"<?= ((!empty($property_info) && $property_info->prop_bedrooms == "2") ? " selected" : ""); ?>>2</option>
                                              <option value="3"<?= ((!empty($property_info) && $property_info->prop_bedrooms == "3") ? " selected" : ""); ?>>3</option>
                                              <option value="4"<?= ((!empty($property_info) && $property_info->prop_bedrooms == "4") ? " selected" : ""); ?>>4</option>
                                              <option value="5"<?= ((!empty($property_info) && $property_info->prop_bedrooms == "5") ? " selected" : ""); ?>>5</option>
                                              <option value="6"<?= ((!empty($property_info) && $property_info->prop_bedrooms == "6") ? " selected" : ""); ?>>6</option>
                                              <option value="7"<?= ((!empty($property_info) && $property_info->prop_bedrooms == "7") ? " selected" : ""); ?>>7</option>
                                              <option value="8"<?= ((!empty($property_info) && $property_info->prop_bedrooms == "8") ? " selected" : ""); ?>>8</option>
                                              <option value="9"<?= ((!empty($property_info) && $property_info->prop_bedrooms == "9") ? " selected" : ""); ?>>9</option>
                                              <option value="10+"<?= ((!empty($property_info) && $property_info->prop_bedrooms == "10+") ? " selected" : ""); ?>>10+</option>
                                            </select>
                                        </div>
                                        <label class="col-lg-1 control-label"><?= lang('proeprty_baths') ?>: </label>
                                        <div class="col-lg-1">
                                            <select name="prop_bathrooms" class="form-control select_box">
                                              <option value disabled selected><?= lang("select"); ?></option>
                                              <option value="1"<?= ((!empty($property_info) && $property_info->prop_bathrooms == "1") ? " selected" : ""); ?>>1</option>
                                              <option value="2"<?= ((!empty($property_info) && $property_info->prop_bathrooms == "2") ? " selected" : ""); ?>>2</option>
                                              <option value="3"<?= ((!empty($property_info) && $property_info->prop_bathrooms == "3") ? " selected" : ""); ?>>3</option>
                                              <option value="4"<?= ((!empty($property_info) && $property_info->prop_bathrooms == "4") ? " selected" : ""); ?>>4</option>
                                              <option value="5"<?= ((!empty($property_info) && $property_info->prop_bathrooms == "5") ? " selected" : ""); ?>>5</option>
                                              <option value="6"<?= ((!empty($property_info) && $property_info->prop_bathrooms == "6") ? " selected" : ""); ?>>6</option>
                                              <option value="7"<?= ((!empty($property_info) && $property_info->prop_bathrooms == "7") ? " selected" : ""); ?>>7</option>
                                              <option value="8"<?= ((!empty($property_info) && $property_info->prop_bathrooms == "8") ? " selected" : ""); ?>>8</option>
                                              <option value="9"<?= ((!empty($property_info) && $property_info->prop_bathrooms == "9") ? " selected" : ""); ?>>9</option>
                                              <option value="10+"<?= ((!empty($property_info) && $property_info->prop_bathrooms == "10+") ? " selected" : ""); ?>>10+</option>
                                            </select>
                                        </div>
                                        <label class="col-lg-1 control-label"><?= lang('property_lsm') ?>: </label>
                                        <div class="col-lg-3">
                                            <div class="checkbox c-checkbox">
                                                <label class="needsclick">
                                                    <input type="radio" <?= ((!empty($property_info) && $property_info->prop_lsm == "shared") ? "checked" : ""); ?> value="shared" name="prop_lms" data-parsley-multiple="allow_authorize" class="filter_input" required><span class="fa fa-check"></span>Shared
                                                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <label class="needsclick">
                                                    <input type="radio" <?= ((!empty($property_info) && $property_info->prop_lsm == "private") ? "checked" : ""); ?> value="private" name="prop_lms" data-parsley-multiple="allow_authorize" class="filter_input" required><span class="fa fa-check"></span>Private
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-1 control-label"><?= lang('proeprty_purpose') ?>: <span class="text-danger">*</span><label class="control-label" id="rent_frequency_label" style="margin-top:15px;"><?= lang("price"); ?>: <span class="text-danger">*</span></label></label>
                                        <div class="col-lg-3">
                                            <div class="checkbox c-checkbox" style="width:40%;">
                                                <label class="needsclick">
                                                    <input type="radio" <?= ((!empty($property_info)) ? (($property_info->prop_purpose == "SALE") ? "checked" : "") : "checked"); ?> value="sale" name="prop_purpose" data-parsley-multiple="allow_authorize" class="filter_input" onclick="changePurpose('sale');" required><span class="fa fa-check"></span>SALE
                                                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <label class="needsclick">
                                                    <input type="radio" <?= ((!empty($property_info) && $property_info->prop_purpose == "RENT") ? "checked" : ""); ?> value="rent" name="prop_purpose" data-parsley-multiple="allow_authorize" class="filter_input" onclick="changePurpose('rent');" required><span class="fa fa-check"></span>RENT
                                                </label>
                                            </div>
                                            <div class="input-group" style="margin-top:15px;">
                                                <input type="text" class="form-control" name="prop_price" value="<?php
                                                if (!empty($leads_info)) {
                                                    echo $leads_info->prop_price;
                                                }
                                                ?>"/>
                                                <div class="input-group-addon" style="padding:0;" data-toggle="tooltip" data-placement="top">
                                                      <div class="input-group" id="rent_frequency" >
                                                        <div class="input-group-addon">AED</div>
                                                      </div>
                                                </div>
                                            </div>
                                        </div>
                                        <label class="col-lg-1 control-label"><?= lang('property_parking') ?>: </label>
                                        <div class="col-lg-1">
                                            <input type="text" class="form-control" value="<?php
                                            if (!empty($property_info)) {
                                                echo $property_info->prop_parking;
                                            }
                                            ?>" name="prop_parking">
                                        </div>
                                        <label class="col-lg-1 control-label"><?= lang('property_yrsbuilt') ?>: </label>
                                        <div class="col-lg-1">
                                            <input type="text" class="form-control datepicker" style="width:100% !important;" value="<?php
                                            if (!empty($property_info)) {
                                                echo $property_info->prop_year_built;
                                            }
                                            ?>" name="prop_year_built">
                                        </div>
                                        <label class="col-lg-1 control-label"><?= lang('property_trasaction') ?>: </label>
                                        <div class="col-lg-3">
                                            <input type="text" class="form-control" value="<?php
                                            if (!empty($property_info)) {
                                                echo $property_info->prop_transaction;
                                            }
                                            ?>" name="prop_transaction" placeholder="<?= lang("property_trasaction_#"); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-1 control-label"><?= lang('property_location_map') ?>: </label>
                                        <div class="col-lg-3">
                                          <div class="input-group">
                                              <input type="text" class="form-control" value="<?php
                                              if (!empty($property_info)) {
                                                  echo $property_info->prop_location;
                                              }
                                              ?>" name="prop_location">
                                              <div class="input-group-addon" title="<?= lang('property_location_map') ?>" data-toggle="tooltip" data-placement="top">
                                                  <a data-toggle="modals" data-target="#myModals" href="javascript:;"><i class="fa fa-map-marker"></i></a>
                                              </div>
                                          </div>
                                        </div>
                                        <label class="col-lg-1 control-label"><?= lang('proeprty_developer') ?>: </label>
                                        <div class="col-lg-3">
                                            <input type="text" class="form-control" value="<?php
                                            if (!empty($property_info)) {
                                                echo $property_info->prop_developer;
                                            }
                                            ?>" name="prop_developer">
                                        </div>
                                        <label class="col-lg-1 control-label"><?= lang('proeprty_permit') ?>: </label>
                                        <div class="col-lg-3">
                                            <input type="text" class="form-control" value="<?php
                                            if (!empty($property_info)) {
                                                echo $property_info->prop_permit;
                                            }
                                            ?>" name="prop_permit" placeholder="<?= lang("proeprty_permit_#"); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-1 control-label" style="margin-top:25px;"><?= lang('state') ?>: <span class="text-danger">*</span></label>
                                        <div class="col-lg-3" style="margin-top:25px;">
                                            <input type="text" class="form-control" value="<?php
                                            if (!empty($property_info)) {
                                                echo $property_info->prop_state;
                                            }
                                            ?>" name="prop_state" step="0.01" required>
                                        </div>
                                        <label class="col-lg-1 control-label" style="margin-top:20px;"><?= lang('property_plotarea_sqft') ?>: <span class="text-danger">*</span></label>
                                        <div class="col-lg-3">
                                            <span style="display:inline-block;width:100% !important;"><small class="pull-right" id="plot_are_sqm"><?= ((!empty($property_info) && !empty($property_info->prop_plot_area)) ? number_format((float)($property_info->prop_plot_area * 0.093), 3, '.', '') . " " : "....") . lang("property_area_sqm"); ?></small></span>
                                            <div class="input-group">
                                                <input type="number" class="form-control changeDistance" data-reply="plot_are_sqm" name="prop_plot_area" required value="<?php
                                                if (!empty($property_info)) {
                                                    echo $property_info->prop_plot_area;
                                                }
                                                ?>" step="0.01"/>
                                                <div class="input-group-addon">
                                                    <a href="javascript:;">SQFT</a>
                                                </div>
                                              </div>
                                        </div>
                                        <label class="col-lg-1 control-label" style="margin-top:25px;"><?= lang('property_landlord') ?>: <span class="text-danger">*</span></label>
                                        <div class="col-lg-3" style="margin-top:25px;">
                                            <div class="input-group" style="width:100%;">
                                                <input type="hidden" name="prop_owner_id" id="prop_owner" required<?= ((!empty($property_info) && $property_info->prop_owner_id != NULL) ? " value='" . $property_info->prop_owner_id . "'" : ""); ?>/>
                                                <input list="ownersList" id="owner_option" class="form-control" placeholder="At least 5 Chars. to view options" required<?= ((!empty($property_info) && $property_info->prop_owner_id != NULL) ? "value='" . $this->db->select("lead_name")->where("leads_id", $property_info->prop_owner_id)->get("tbl_leads")->row()->lead_name . "'" : ""); ?>/>
                                                    <datalist id="ownersList"></datalist>
                                                    <?php if (!empty(can_action('55', 'created'))) { ?>
                                                            <div class="input-group-addon" title="<?= lang('new') . ' ' . lang('lead') ?>" data-toggle="tooltip" data-placement="top">
                                                                <a data-toggle="modal" data-target="#myModal_extra_lg" href="<?= base_url() ?>admin/properties/new_landlord/"><i class="fa fa-plus"></i></a>
                                                            </div>
                                                    <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-1 control-label" style="margin-top:25px;"><?= lang('property_community') ?>: <span class="text-danger">*</span></label>
                                        <div class="col-lg-3" style="margin-top:25px;">
                                            <input type="text" class="form-control" required value="<?php
                                            if (!empty($property_info)) {
                                                echo $property_info->prop_community;
                                            }
                                            ?>" name="prop_community">
                                        </div>
                                        <label class="col-lg-1 control-label" style="margin-top:20px;"><?= lang('property_area_sqft') ?>: <span class="text-danger">*</span></label>
                                        <div class="col-lg-3">
                                            <span style="display:inline-block;width:100% !important;"><small class="pull-right" id="area_sqm">....<?= lang("property_area_sqm"); ?></small></span>
                                            <div class="input-group">
                                                <input type="number" class="form-control changeDistance" data-reply="area_sqm" name="prop_size_sqft" required value="<?php
                                                if (!empty($property_info)) {
                                                    echo $property_info->prop_size_sqft;
                                                }
                                                ?>"/>
                                                <div class="input-group-addon">
                                                    <a href="javascript:;">SQFT</a>
                                                </div>
                                            </div>
                                        </div>
                                        <label class="col-lg-1 control-label" style="margin-top:25px;"><?= lang('property_rented') ?>: </label>
                                        <div class="col-lg-3" style="margin-top:25px;">
                                            <div class="checkbox c-checkbox" style="width:40%;">
                                                <label class="needsclick">
                                                    <input type="checkbox"<?= ((!empty($property_info) && $property_info->prop_rented == "yes") ? " checked" : ""); ?> value="yes" name="prop_rented" class="filter_input"><span class="fa fa-check"></span><?= lang('property_rented') ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-1 control-label"><?= lang('property_sub_community') ?>: <span class="text-danger">*</span></label>
                                        <div class="col-lg-3">
                                            <input type="text" class="form-control" required value="<?php
                                            if (!empty($property_info)) {
                                                echo $property_info->prop_sub_community;
                                            }
                                            ?>" name="prop_sub_community">
                                        </div>
                                        <?php if (!empty($property_info)) {
                                            $deposit = json_decode($property_info->prop_deposit_info);;
                                        }
                                        ?>
                                        <label class="col-lg-1 control-label"><?= lang('property_deposit') ?>: </label>
                                        <div class="col-lg-3">
                                            <div class="row">
                                            <div class=" col-lg-6">
                                                  <div class="input-group">
                                                      <input type="number" class="form-control" name="property_deposit" value="<?php
                                                      if (!empty($deposit)) {
                                                          echo $deposit[0];
                                                      }
                                                      ?>"/>
                                                      <div class="input-group-addon">
                                                          <a href="javascript:;">%</a>
                                                      </div>
                                                  </div>
                                              </div>
                                              <div class=" col-lg-6">
                                                  <div class="input-group">
                                                      <input type="number" class="form-control" name="property_deposit_money" value="<?php
                                                      if (!empty($deposit)) {
                                                          echo $deposit[1];
                                                      }
                                                      ?>"/>
                                                      <div class="input-group-addon">
                                                          <a href="javascript:;">AED</a>
                                                      </div>
                                                  </div>
                                              </div>
                                            </div>
                                        </div>
                                        <label class="col-lg-1 control-label"><?= lang('source') ?>: <span class="text-danger">*</span></label>
                                        <div class="col-lg-3">
                                            <div class="input-group">
                                                <select name="property_source_id" class="form-control select_box" style="width: 100%" required="">
                                                    <?php
                                                      $proeprty_source_info = $this->db->order_by('property_source_id', 'DESC')->get('tbl_property_source')->result();
                                                    if (!empty($proeprty_source_info)) {
                                                        foreach ($proeprty_source_info as $v_property_source) {
                                                            ?>
                                                            <option value="<?= $v_property_source->property_source_id ?>" <?= (!empty($property_info) && $property_info->property_source_id == $v_property_source->property_source_id ? 'selected' : '') ?>><?= $v_property_source->property_source ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    $_created = can_action('159', 'created');
                                                    ?>
                                                </select>
                                                <?php if (!empty($_created)) { ?>
                                                    <div class="input-group-addon" title="<?= lang('new') . ' ' . lang('source') ?>" data-toggle="tooltip" data-placement="top">
                                                        <a data-toggle="modal" data-target="#myModal" href="<?= base_url() ?>admin/properties/property_source"><i class="fa fa-plus"></i></a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (!empty($property_info)) {
                                        $unit = json_decode($property_info->prop_unit_info);;
                                    }
                                    ?>
                                    <div class="form-group">
                                        <label class="col-lg-1 control-label"><?= lang('property_unit') ?>: <span class="text-danger">*</span></label>
                                        <div class="col-lg-1">
                                            <input type="text" class="form-control" required value="<?php
                                            if (!empty($unit[0])) {
                                                echo $unit[0];
                                            }
                                            ?>" name="property_unit" placeholder="<?= lang('property_unit') ?>">
                                        </div>
                                        <div class="col-lg-1">
                                            <input type="text" class="form-control" required value="<?php
                                            if (!empty($unit[1])) {
                                                echo $unit[1];
                                            }
                                            ?>" name="property_plot" placeholder="<?= lang('property_plot') ?>">
                                        </div>
                                        <div class="col-lg-1">
                                            <input type="text" class="form-control" required value="<?php
                                            if (!empty($unit[2])) {
                                                echo $unit[2];
                                            }
                                            ?>" name="property_street" placeholder="<?= lang('property_street') ?>">
                                        </div>
                                        <label class="col-lg-1 control-label"><?= lang('title') ?>: </label>
                                        <div class="col-lg-3">
                                            <input type="text" class="form-control" value="<?php
                                            if (!empty($property_info)) {
                                                echo $property_info->prop_title;
                                            }
                                            ?>" name="prop_title">
                                        </div>
                                        <label class="col-lg-1 control-label"><?= lang('country') ?> <span class="text-danger">*</span></label>
                                        <div class="col-lg-3">
                                            <select name="prop_country" class="form-control person select_box" required style="width: 100%">
                                                <?php if (!empty($property_info->prop_country)) { ?>
                                                    <optgroup label="Default Country">
                                                        <option value="<?= $property_info->prop_country ?>"><?= $property_info->prop_country ?></option>
                                                    </optgroup>
                                                    <optgroup label="<?= lang('other_countries') ?>">
                                                <?php }
                                                $countries = $this->db->get('tbl_countries')->result();
                                                if (!empty($countries)): foreach ($countries as $country): ?>
                                                        <option value="<?= $country->value ?>"><?= $country->value ?></option>
                                                        <?php
                                                    endforeach;
                                                endif;
                                                 if (!empty($property_info->prop_country)) { echo "</optgroup>"; } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-1 control-label"><?= lang('porperty_custom_reference') ?>: </label>
                                        <div class="col-lg-3">
                                            <input type="text" class="form-control" value="<?php
                                            if (!empty($property_info)) {
                                                echo $property_info->prop_custom_reference;
                                            }
                                            ?>" name="prop_custom_reference">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-2 control-label"><?= lang('property_description') ?> </label>
                                        <div class="col-lg-9">
                                            <textarea name="prop_description" class="form-control textarea"><?php
                                                if (!empty($property_info)) {
                                                    echo $property_info->prop_description;
                                                }
                                                ?></textarea>
                                        </div>
                                        <div class="tab-index pull-left btn-blue-border" style="padding:5px;border:3px solid #5d9cec;border-radius:10px;margin-top:4px;">
                                            <a  data-target="#myModal_extra_lg" data-toggle="modal" href="<?= base_url("admin/properties/property_addition/photos"); ?>" unselectable="on"><i class="class_toggle_tab fa fa-plus all_btn_spacing margin-bottom-5"></i> <?= lang("photos"); ?> <span class="add_photos_count" photos_count="0">(0)</span></a>
                                        </div>
                                        <div class="tab-index pull-left btn-blue-border" style="padding:5px;border:3px solid #5d9cec;border-radius:10px;margin-top:20px;">
                                            <a data-target="#myModal_extra_lg" data-toggle="modal" href="<?= base_url("admin/properties/property_addition/videos"); ?>" unselectable="on"><i class="class_toggle_tab fa fa-plus all_btn_spacing margin-bottom-5"></i> <?= lang("videos"); ?> <span class="add_videos_count" videos_count="0">(0)</span></a>
                                        </div>
                                        <div class="tab-index pull-left btn-blue-border" style="padding:5px;border:3px solid #5d9cec;border-radius:10px;margin-top:20px;">
                                            <a data-target="#myModal_extra_lg" data-toggle="modal" href="<?= base_url("admin/properties/property_addition/attachment"); ?>" unselectable="on"><i class="class_toggle_tab fa fa-plus all_btn_spacing margin-bottom-5"></i> <?= lang("attachment"); ?> <span class="add_attachment_count" attachment_count="0">(0)</span></a>
                                        </div>
                                    </div>
                                    <?php
                                    if (!empty($property_info)) {
                                        $prop_id = $property_info->prop_id;
                                    } else {
                                        $prop_id = null;
                                    }
                                    ?>
                                    <div class="form-group" id="border-none">
                                        <label for="field-1" class="col-sm-2 control-label"><?= lang('assined_to') ?>
                                            <span class="required">*</span></label>
                                        <div class="col-sm-4">
                                            <div class="checkbox c-radio needsclick">
                                                <label class="needsclick">
                                                    <input id="" <?php
                                                    if (!empty($property_info->permission) && $property_info->permission == 'all') {
                                                        echo 'checked';
                                                    } elseif (empty($property_info)) {
                                                        echo 'checked';
                                                    }
                                                    ?> type="radio" name="permission" value="everyone">
                                                    <span class="fa fa-circle"></span><?= lang('everyone') ?>
                                                    <i title="<?= lang('permission_for_all') ?>" class="fa fa-question-circle" data-toggle="tooltip" data-placement="top"></i>
                                                </label>
                                            </div>
                                            <div class="checkbox c-radio needsclick">
                                                <label class="needsclick">
                                                    <input id="" <?php
                                                    if (!empty($property_info->permission) && $property_info->permission != 'all') {
                                                        echo 'checked';
                                                    }
                                                    ?> type="radio" name="permission" value="custom_permission">
                                                    <span class="fa fa-circle"></span><?= lang('custom_permission') ?>
                                                    <i title="<?= lang('permission_for_customization') ?>" class="fa fa-question-circle" data-toggle="tooltip" data-placement="top"></i>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group <?php
                                    if (!empty($property_info->permission) && $property_info->permission != 'all') {
                                        echo 'show';
                                    }
                                    ?>" id="permission_user_1">
                                        <label for="field-1" class="col-sm-2 control-label"><?= lang('select') . ' ' . lang('users') ?>
                                            <span class="required">*</span></label>
                                        <div class="col-sm-4">
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
                                                                if (!empty($property_info->permission) && $property_info->permission != 'all') {
                                                                    $get_permission = json_decode($property_info->permission);
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
                                                        if (!empty($property_info->permission) && $property_info->permission != 'all') {
                                                            $get_permission = json_decode($property_info->permission);
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
                                                                <input <?php
                                                                if (!empty($disable)) {
                                                                    echo 'disabled' . ' ' . 'checked';
                                                                }
                                                                ?> id="<?= $v_user->user_id ?>"
                                                                    <?php
                                                                    if (!empty($property_info->permission) && $property_info->permission != 'all') {
                                                                        $get_permission = json_decode($property_info->permission);
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
                                                                <input <?php
                                                                if (!empty($disable)) {
                                                                    echo 'disabled' . ' ' . 'checked';
                                                                }
                                                                ?> id="<?= $v_user->user_id ?>"
                                                                    <?php
                                                                    if (!empty($property_info->permission) && $property_info->permission != 'all') {
                                                                        $get_permission = json_decode($property_info->permission);
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
                                    <?php if (empty($property_info->converted_client_id) || $property_info->converted_client_id == 0) { ?>
                                        <div class="btn-bottom-toolbar text-right">
                                            <?php if (!empty($property_info)) { ?>
                                                <button type="submit" class="btn btn-sm btn-primary"><?= lang('updates') ?></button>
                                                <button type="button" onclick="goBack()" class="btn btn-sm btn-danger"><?= lang('cancel') ?></button>
                                            <?php } else {
                                                ?>
                                                <input type="submit" class="btn btn-sm btn-primary" value="<?= lang('save') ?>">
                                                <input type="submit" class="btn btn-sm btn-warning" name="save_as" value="<?= lang('save_as_draft') ?>">
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
    </div>
</div>
<div id="filer_sideBar" class="filter_sidebar" style="display:none;">
    <form action="javascript:;" id="filter_properties" method="post" accept-charset="utf-8">
        <div class="filter_sidebar_content">
            <div class="card">
                <div class="card-header bg-transparent header-elements-inline">
                    <span class="text-uppercase font-size-sm font-weight-semibold">Filters</span>
                </div>
                <hr>
                <div class="form-group">
                    <div class="checkbox c-checkbox">
                        <label>Purpose</label>
                        <div class="pull-right">
                            <label class="needsclick">
                                <input type="radio" value="Yes" name="filter_purpose" data-parsley-multiple="allow_authorize" class="filter_input" data-parsley-id="36"><span class="fa fa-check"></span>SALE
                            </label>&nbsp;&nbsp;&nbsp;&nbsp;
                            <label class="needsclick">
                                <input type="radio" value="Yes" name="filter_purpose" data-parsley-multiple="allow_authorize" class="filter_input" data-parsley-id="36"><span class="fa fa-check"></span>RENT
                            </label>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <select data-placeholder="ASSIGNED TO" title="ASSIGNED TO" name="filter_assigned_to" class="form-control select_box filter_input" style="width:100%;">
                        <option value disabled selected>ASSIGNED TO</option>
                        <?php
                        foreach ($assign_user as $staff) {
                            if ($staff->username != "admin") {
                                $staff_details = $this->db->where('user_id', $staff->user_id)->get('tbl_account_details')->row();
                                echo '<option value="' . $staff->user_id . '">' . $staff_details->fullname . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <div class="mb-4">
                        <select data-placeholder="TYPES" title="TYPES" name="filter_types" class="form-control select_box filter_input" style="width:100%;">
                            <option value disabled selected>TYPES</option>
                            <?php
                            $property_type_info = $this->db->order_by('property_type_id', 'DESC')->get('tbl_properties_types')->result();
                            foreach ($property_type_info as $type) {
                                echo '<option value="' . $type->property_type_id . '">' . $type->property_type . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="mb-4">
                        <select data-placeholder="DEVELOPER" title="DEVELOPER" name="filter_developer" class="form-control select_box filter_input" style="width:100%;">
                            <option value disabled selected>DEVELOPER</option>
                            <?php
                            $data = $this->db->select('prop_developer')->where("`prop_developer` IS NOT NULL AND `prop_developer` != ''")->get("tbl_properties");
                            $existArray = array();
                            foreach ($data->result() as $ddata) {
                                if (!(in_array($ddata->prop_developer, $existArray))) {
                                    array_push($existArray, $ddata->prop_developer);
                                    echo "<option value='" . $ddata->prop_developer . "'>" . $ddata->prop_developer . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="mb-4">
                        <select data-placeholder="LOCATION" title="LOCATION" name="filter_location" class="form-control select_box filter_input" style="width:100%;">
                            <option value disabled selected>LOCATION</option>
                            <?php
                            $data = $this->db->select('prop_location')->where("`prop_location` IS NOT NULL AND `prop_location` != ''")->get("tbl_properties");
                            $existArray = array();
                            foreach ($data->result() as $ddata) {
                                if (!(in_array($ddata->prop_location, $existArray))) {
                                    array_push($existArray, $ddata->prop_location);
                                    echo "<option value='" . $ddata->prop_location . "'>" . $ddata->prop_location . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="mb-4">
                        <select data-placeholder="COUNTRY" title="COUNTRY" name="filter_country" class="form-control select_box filter_input" style="width:100%;">
                            <option value disabled selected>COUNTRY</option>
                            <?php
                            foreach ($nationalities as $countries) {
                                echo '<option value="' . $countries->id . '">' . $countries->long_name . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="mb-4">
                        <select data-placeholder="COMMUNITY" title="COMMUNITY" name="filter_community" class="form-control select_box filter_input" style="width:100%;">
                            <option value disabled selected>COMMUNITY</option>
                            <?php
                            $data = $this->db->select('prop_community')->where("`prop_community` IS NOT NULL AND `prop_community` != ''")->get("tbl_properties");
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
                            $data = $this->db->select('prop_sub_community')->where("`prop_sub_community` IS NOT NULL AND `prop_sub_community` != ''")->get("tbl_properties");
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
                <hr/>
                <div class="card-body">
                    <div class="form-group">
                        <div class="checkbox c-checkbox">
                            <label class="needsclick">
                                <input type="checkbox" value="Yes" name="filter_ac" data-parsley-multiple="allow_authorize" class="filter_input" data-parsley-id="36"><span class="fa fa-check"></span>
                                Central AC</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox c-checkbox">
                            <label class="needsclick">
                                <input type="checkbox" value="Yes" name="filter_heater" data-parsley-multiple="allow_authorize" class="filter_input" data-parsley-id="36"><span class="fa fa-check"></span>
                                Central HEATER</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox c-checkbox">
                            <label class="needsclick">
                                <input type="checkbox" value="Yes" name="filter_study_room" data-parsley-multiple="allow_authorize" class="filter_input" data-parsley-id="36"><span class="fa fa-check"></span>
                                Study room</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox c-checkbox">
                            <label class="needsclick">
                                <input type="checkbox" value="Yes" name="filter_balacony" data-parsley-multiple="allow_authorize" class="filter_input" data-parsley-id="36"><span class="fa fa-check"></span>
                                Balacony</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox c-checkbox">
                            <label class="needsclick">
                                <input type="checkbox" value="Yes" name="filter_private_pool" data-parsley-multiple="allow_authorize" class="filter_input" data-parsley-id="36"><span class="fa fa-check"></span>
                                Private pool</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox c-checkbox">
                            <label class="needsclick">
                                <input type="checkbox" value="Yes" name="filter_storage" data-parsley-multiple="allow_authorize" class="filter_input" data-parsley-id="36"><span class="fa fa-check"></span>
                                Storage</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox c-checkbox">
                            <label class="needsclick">
                                <input type="checkbox" value="Yes" name="filter_conayin_leads" data-parsley-multiple="allow_authorize" class="filter_input" data-parsley-id="36"><span class="fa fa-check"></span>
                                Contain Leads</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox c-checkbox">
                            <label class="needsclick">
                                <input type="checkbox" value="Yes" name="filter_owned_property" data-parsley-multiple="allow_authorize" class="filter_input" data-parsley-id="36"><span class="fa fa-check"></span>
                                Owned Property</label>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <b>Price (AED)</b>
                        <div class="clearfix" style="display: inline-flex;">
                              <span style="margin-top:5px;">FROM:</span><input style="width:40%;" type="number" id="flter_price_start" name="flter_price_start" class="form-control" placeholder="START" min="0">
                              <span style="margin-top:5px;">TO:</span><input style="width:40%;" type="number" id="flter_price_end" name="flter_price_end" class="form-control" placeholder="START" min="0">
                        </div>
                    </div>
                    <hr style="margin-bottom:5px;margin-top:0px;"/>
                    <div class="form-group">
                        <b>Bedroom/s</b>
                        <div class="clearfix" style="display: inline-flex;">
                              <span style="margin-top:5px;">FROM:</span><input style="width:40%;" type="number" id="flter_beds_start" name="flter_beds_start" class="form-control" placeholder="START" min="0">
                              <span style="margin-top:5px;">TO:</span><input style="width:40%;" type="number" id="flter_beds_end" name="flter_beds_end" class="form-control" placeholder="START" min="0">
                        </div>
                    </div>
                    <hr style="margin-bottom:5px;margin-top:0px;"/>
                    <div class="form-group">
                        <b>Bathroom/s</b>
                        <div class="clearfix" style="display: inline-flex;">
                              <span style="margin-top:5px;">FROM:</span><input style="width:40%;" type="number" id="flter_bath_start" name="flter_bath_start" class="form-control" placeholder="START" min="0">
                              <span style="margin-top:5px;">TO:</span><input style="width:40%;" type="number" id="flter_bath_end" name="flter_bath_end" class="form-control" placeholder="START" min="0">
                        </div>
                    </div>
                    <hr style="margin-bottom:5px;margin-top:0px;"/>
                    <div class="form-group">
                        <b>Parking</b>
                        <div class="clearfix" style="display: inline-flex;">
                              <span style="margin-top:5px;">FROM:</span><input style="width:40%;" type="number" id="flter_parking_start" name="flter_parking_start" class="form-control" placeholder="START" min="0">
                              <span style="margin-top:5px;">TO:</span><input style="width:40%;" type="number" id="flter_parking_end" name="flter_parking_end" class="form-control" placeholder="START" min="0">
                        </div>
                    </div>
                    <input type="hidden" id="view_by" name="flter_view_by" value=""/>
                    <hr/>
                    <div class="form-group">
                        <div class="mb-4" style="float: right !important;margin-bottom: 20px !important;">
                            <button type="reset"  class="btn btn-secondary legitRipple">RESET</button>
                            <button type="button" class="btn btn-primary go_filter legitRipple" data-input="view_by">GO</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    function propertyFastView(prop_id, row_id) {
        $.ajax({
            method: "POST",
            url: "<?= base_url("admin/properties/property_fast_view/"); ?>",
            data: {prop_id: prop_id},
            success: function (html) {
                $(".property_fast_view").remove();
                $("#property_fast_view").remove();
                $('.close_quick_view').each(function () {
                    $(this).removeClass("close_quick_view").removeClass("btn-danger").addClass("btn-info").attr("title", "View");
                    $(this).attr("onclick", "propertyFastView(" + $(this).attr("data-proeprty-id") + "," + $(this).attr("data-proeprty") + ");").find("span").removeClass("fa-close").addClass("fa-eye");
                });
                $(".property_view_" + prop_id).addClass("close_quick_view").removeClass("btn-info").addClass("btn-danger").attr("title", "Close View");
                $(".property_view_" + prop_id).attr("onclick", "closeQuickView(" + prop_id + "," + row_id + ");").find("span").removeClass("fa-eye").addClass("fa-close");
                $('table > tbody > tr').eq(row_id).after(html);
            }
        });
    }
    function closeQuickView(prop_id, row_id) {
        $(".property_fast_view").remove();
        $("#proeprty_fast_view").remove();
        $(".property_view_" + prop_id).removeClass("close_quick_view").removeClass("btn-danger").addClass("btn-info").attr("title", "View");
        $(".property_view_" + prop_id).attr("onclick", "proeprtyFastView(" + prop_id + "," + row_id + ");").find("span").removeClass("fa-close").addClass("fa-eye");
    }
    function changePurpose(value){
          if(value == "rent"){
            $("#rent_frequency").append('<select id="rent_frequency_select" name="rent_frequency" class="form-control select_box" required><option value disabled selected><?= lang("select"); ?></option><option value="annually"><?= lang("annually"); ?></option><option value="semi-annually"><?= lang("semi-annually"); ?></option><option value="monthly"><?= lang("monthly"); ?></option><option value="daily"><?= lang("daily"); ?></option></select>');
            $("#rent_frequency").parent().css("width", "60%");
            $("#rent_frequency").parent().css("border", "0");
            $("#rent_frequency_label").html("<?= lang("rent_frequency"); ?>: <span class='text-danger'>*</span>");
          }else{
            $("#rent_frequency").parent().css("width", "");
            $("#rent_frequency").parent().css("border", "");
            $("#rent_frequency_select").remove();
            $("#rent_frequency_label").html("<?= lang("price"); ?>: <span class='text-danger'>*</span>");
          }
    }
    $(".filters").click(function () {
        $("#properties_data").toggleClass("col-sm-12").toggleClass("col-sm-10");
        $("body").toggleClass("aside-collapsed");
        $("#filer_sideBar").toggle();
        if ($("#filer_sideBar").is(":hidden")) {
            $(".filters_button").css("color", "");
            $(".filters_button").css("background", "");
        } else {
            $(".filters_button").css("color", "white");
            $(".filters_button").css("background", "#d19b15");
        }
    });
    $("#owner_option").keyup(function () {
      if($(this).val().length >= 5){
          var value = $(this).val();
          $.ajax({
              method: "POST",
              url: "<?= base_url("admin/properties/getOwners/"); ?>",
              data: {searchValue: value},
              success: function (html) {
                  $("#ownersList").html(html);
              }
          });
      }
    });
    $("#owner_option").change(function () {
        var list = $("#" + $(this).attr("list") + " > option");
        var value = $(this).val();
        list.each(function(){
            if($(this).attr("value") == value){
               $("#prop_owner").val($(this).attr("data-owner-id"));
            }
        });
    });
    $(".changeDistance").keyup(function (){
        var distance = $(this).val();
        var id = $(this).attr("data-reply");
        $("#" + id).html(parseFloat(distance * 0.093).toFixed(3) + " <?= lang("property_area_sqm"); ?>");
    });
    $(".changeDistance").change(function (){
        var distance = $(this).val();
        var id = $(this).attr("data-reply");
        $("#" + id).html(parseFloat(distance * 0.093).toFixed(3) + " <?= lang("property_area_sqm"); ?>");
    });
    $(document).ready(function () {
        filtering = "filter_properties";
        list = base_url + "admin/properties/PropertiesList";
        $(".go_filter").click(function () {
            if($(this).attr("data-filter") !== typeof undefined && $(this).attr("data-filter") !== false){
              $("#" + $(this).attr("data-input")).val($(this).attr("data-filter-value"));
            }else{
              $("#" + $(this).attr("data-input")).val("");
            }
            table_url(list);
        });
        window.onscroll = function () {
            stickyFunction()
        };
        var filter_header = document.getElementById("filer_sideBar");
        var sticky = filter_header.offsetTop;``
        function stickyFunction() {
            if (window.pageYOffset > sticky + 20) {
                filter_header.classList.add("sticky");
            } else {
                filter_header.classList.remove("sticky");
            }
        }
    });
</script>
