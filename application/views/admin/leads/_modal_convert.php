<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang('convert_to_client') ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
        <form data-parsley-validate="" novalidate="" action="<?php echo base_url() ?>admin/client/opportunityConverted/<?php if (!empty($leads_info->leads_id)) echo $leads_info->leads_id; ?>" method="post" class="form-horizontal form-groups-bordered">
            <div class="panel-body">
              <input type="hidden" name="opportunity_id" value="<?= $opportunity_info->opportunities_id; ?>">
              <input type="hidden" name="lead_id" value="<?= $leads_info->leads_id; ?>">
                <label class="control-label col-sm-1"></label>
                <div class="col-sm-12">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#general_details" data-toggle="tab"><?= lang('general') ?></a></li>
                            <li><a href="#contact_details" data-toggle="tab"><?= lang('client_contact') . ' ' . lang('details') ?></a></li>
                            <li><a href="#web_details" data-toggle="tab"><?= lang('web') ?></a></li>
                        </ul>
                        <div class="tab-content bg-white">
                            <div class="chart tab-pane active" id="general_details">
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('client_name') ?></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control company" required="" value="<?php
                                        if (!empty($leads_info->lead_name)) {
                                            echo $leads_info->lead_name . '" readonly="';
                                        }
                                        ?>" name="name">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('client') . " " . lang("email") ?></label>
                                    <div class="col-lg-9">
                                        <input type="email" class="form-control company" required="" value="<?php
                                        if (!empty($leads_info->email)) {
                                            echo $leads_info->email . '" readonly="';
                                        }
                                        ?>" name="email">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?= lang('client') . " " . lang('group') ?></label>
                                    <div class="col-sm-5">
                                        <select name="customer_group_id" class="form-control select_box" style="width: 100%">
                                            <?php
                                            $all_customer_group = $this->db->order_by('customer_group_id', 'DESC')->get('tbl_customer_group')->result();
                                            if (!empty($all_customer_group)) {
                                                foreach ($all_customer_group as $customer_group) :
                                                    ?>
                                                    <option value="<?= $customer_group->customer_group_id ?>"><?= $customer_group->customer_group; ?></option>
                                                        <?php
                                                    endforeach;
                                                }
                                                ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"><?= lang('client') . " " . lang('language') ?></label>
                                    <div class="col-sm-5">
                                        <select name="language" class="form-control company select_box" style="width: 100%">
                                            <?php foreach ($languages as $lang) : ?>
                                                <option value="<?= $lang->name ?>"<?php
                                                    if ($this->config->item('language') == $lang->name) {
                                                        echo 'selected';
                                                    }
                                                    ?>
                                                    ><?= ucfirst($lang->name) ?></option>
                                                <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('currency') ?></label>
                                    <div class="col-lg-9">
                                        <select name="currency" class="form-control company select_box" style="width: 100%">
                                            <?php if (!empty($currencies)): foreach ($currencies as $currency): ?>
                                                    <option value="<?= $currency->code ?>"
                                                    <?php
                                                    if ($this->config->item('default_currency') == $currency->code) {
                                                        echo 'selected';
                                                    }
                                                    ?>><?= $currency->name ?></option>
                                                            <?php
                                                        endforeach;
                                                    endif;
                                                    ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('short_note') ?></label>
                                    <div class="col-lg-9">
                                        <textarea class="form-control company" name="short_note"><?php
                                            if (!empty($leads_info->notes)) {
                                                echo $leads_info->notes . '" readonly="';
                                            }
                                            ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="chart tab-pane" id="contact_details">
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('landline') ?></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control company" value="<?php
                                        if (!empty($leads_info->phone)) {
                                            echo $leads_info->phone . '" readonly="';
                                        }
                                        ?>" name="phone">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('mobile') ?><span class="text-danger"> *</span></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control company" required=""
                                               value="<?php
                                               if (!empty($leads_info->mobile)) {
                                                   echo $leads_info->mobile . '" readonly="';
                                               }
                                               ?>" name="mobile">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('fax') ?></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control company" name="fax">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('city') ?></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control company" value="<?php
                                        if (!empty($leads_info->city)) {
                                            echo $leads_info->city . '" readonly="';
                                        }
                                        ?>" name="city">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('country') ?></label>
                                    <div class="col-lg-9">
                                        <select name="country" class="form-control company select_box" style="width: 100%">
                                            <optgroup label="Default Country">
                                                <option value="<?= $this->config->item('company_country') ?>"><?= $this->config->item('company_country') ?></option>
                                            </optgroup>
                                            <optgroup label="<?= lang('other_countries') ?>">
                                                <?php if (!empty($countries)): foreach ($countries as $country): ?>
                                                        <option
                                                            value="<?= $country->value ?>" <?= (!empty($leads_info->country) && $leads_info->country == $country->value ? 'selected' : NULL) ?>><?= $country->value ?>
                                                        </option>
                                                        <?php
                                                    endforeach;
                                                endif;
                                                ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('address') ?></label>
                                    <div class="col-lg-9">
                                        <textarea class="form-control company" name="address"><?php
                                            if (!empty($leads_info->address)) {
                                                echo $leads_info->address . '" readonly="';
                                            }
                                            ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label">
                                        <a href="#"  onclick="fetch_lat_long_from_google_cprofile(); return false;" data-toggle="tooltip" data-title="<?php echo lang('fetch_from_google') . ' - ' . lang('customer_fetch_lat_lng_usage'); ?>"><i id="gmaps-search-icon" class="fa fa-google" aria-hidden="true"></i></a>
                                        <?= lang('latitude') . '( ' . lang('google_map') . ' )' ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" name="latitude">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('longitude') . '( ' . lang('google_map') . ' )' ?></label>
                                    <div class="col-lg-5">
                                        <input type="text" class="form-control" name="longitude">
                                    </div>
                                </div>
                            </div>
                            <div class="chart tab-pane" id="web_details">
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('website') ?></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control company" name="website">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('skype_id') ?></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control company" value="<?php
                                        if (!empty($leads_info->skype)) {
                                            echo $leads_info->skype . '" readonly="';
                                        }
                                        ?>" name="skype_id">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('facebook_profile_link') ?></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control company" value="<?php
                                        if (!empty($leads_info->facebook)) {
                                            echo $leads_info->facebook . '" readonly="';
                                        }
                                        ?>" name="facebook">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('twitter_profile_link') ?></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control company" value="<?php
                                        if (!empty($leads_info->twitter)) {
                                            echo $leads_info->twitter . '" readonly="';
                                        }
                                        ?>" name="twitter">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('linkedin_profile_link') ?></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control company" name="linkedin">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                    <button type="submit" class="btn btn-primary"><?= lang('save') ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<script type="text/javascript">
    function fetch_lat_long_from_google_cprofile() {
        var data = {};
        data.address = $('textarea[name="address"]').val();
        data.city = $('input[name="city"]').val();
        data.country = $('select[name="country"] option:selected').text();
        console.log(data);
        $('#gmaps-search-icon').removeClass('fa-google').addClass('fa-spinner fa-spin');
        $.post('<?= base_url() ?>admin/global_controller/fetch_address_info_gmaps', data).done(function (data) {
            data = JSON.parse(data);
            $('#gmaps-search-icon').removeClass('fa-spinner fa-spin').addClass('fa-google');
            if (data.response.status == 'OK') {
                $('input[name="latitude"]').val(data.lat);
                $('input[name="longitude"]').val(data.lng);
            } else {
                if (data.response.status == 'ZERO_RESULTS') {
                    toastr.warning("<?php echo lang('g_search_address_not_found'); ?>");
                } else {
                    toastr.warning(data.response.status);
                }
            }
        });
    }
</script>
