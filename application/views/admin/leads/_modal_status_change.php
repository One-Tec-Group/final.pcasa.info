<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= $title . " for ( " . $lead_data->lead_name . " )"; ?></h4>
    </div>
    <div class="modal-body wrap-modal wrap">
      <?php if(!empty($lead_data) && !empty($status_data)) { ?>
        <form data-parsley-validate="" novalidate="" action="javascript:;" method="post" class="form-horizontal form-groups-bordered">
            <div class="panel-body">
              <input type="hidden" name="lead_status_id" value="<?= $status_data->lead_status_id; ?>"/>
              <input type="hidden" name="lead_id" value="<?= $lead_data->leads_id; ?>"/>
              <?php if(($status_data->lead_status_id == 1) && ($status_data->lead_status == "Undefined") && ($status_data->lead_type == "open")){ ?>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('status'); ?> <span class="text text-danger">*</span></label>
                        <div class="col-lg-9">
                          <div class="input-group">
                              <input class="form-control" required type="text" value="<?= $status_data->lead_status; ?>" readonly>
                              <div class="input-group-addon" style="background:#27c24c;">
                                <b class="text-white"><?= strtoupper($status_data->lead_type); ?></b>
                              </div>
                          </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('reason'); ?> <span class="text text-danger">*</span></label>
                        <div class="col-lg-9">
                            <textarea placeholder="<?= lang('reason'); ?>" name="status_comment" required class="form-control"></textarea>
                        </div>
                    </div>
                </div>
              <?php } else if(($status_data->lead_status_id == 2) && ($status_data->lead_status == "Open") && ($status_data->lead_type == "open")){ ?>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('status'); ?> <span class="text text-danger">*</span></label>
                        <div class="col-lg-9">
                          <div class="input-group">
                              <input class="form-control" type="text" required value="<?= $status_data->lead_status; ?>" readonly>
                              <div class="input-group-addon" style="background:#27c24c;">
                                <b class="text-white"><?= strtoupper($status_data->lead_type); ?></b>
                              </div>
                          </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('next_action'); ?> <span class="text text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input placeholder="<?= lang('next_action'); ?>" name="status_next_action" required class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('next_action') . " " . lang("date"); ?> <span class="text text-danger">*</span></label>
                        <div class="col-lg-9">
                          <div class="input-group">
                              <input id="status_next_action_date" class="form-control datepicker" required placeholder="<?= lang('next_action') . " " . lang("date"); ?>" type="text" name="status_next_action_date" data-date-format="mm-dd-yyyy">
                              <div class="input-group-addon">
                                <a href="javascript:;" onclick="document.getElementById('status_next_action_date').focus();"><i class="fa fa-calendar"></i></a>
                              </div>
                            </div>
                        </div>
                    </div>
                    <small>Task and reminder will be added.</small>
                </div>
              <?php } else if(($status_data->lead_status_id == 3) && ($status_data->lead_status == "Contacted") && ($status_data->lead_type == "open")){ ?>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('status'); ?> <span class="text text-danger">*</span></label>
                        <div class="col-lg-9">
                          <div class="input-group">
                              <input class="form-control" required type="text" value="<?= $status_data->lead_status; ?>" readonly>
                              <div class="input-group-addon" style="background:#27c24c;">
                                <b class="text-white"><?= strtoupper($status_data->lead_type); ?></b>
                              </div>
                          </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('next_action'); ?> <span class="text text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input placeholder="<?= lang('next_action'); ?>" name="status_next_action" required class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('next_action') . " " . lang("date"); ?> <span class="text text-danger">*</span></label>
                        <div class="col-lg-9">
                          <div class="input-group">
                              <input id="status_next_action_date" required class="form-control datepicker" placeholder="<?= lang('next_action') . " " . lang("date"); ?>" type="text" name="status_next_action_date" data-date-format="mm-dd-yyyy">
                              <div class="input-group-addon">
                                <a href="javascript:;" onclick="document.getElementById('status_next_action_date').focus();"><i class="fa fa-calendar"></i></a>
                              </div>
                          </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('contact_by'); ?> <span class="text text-danger">*</span></label>
                        <div class="col-lg-9">
                          <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                  <input type="radio" onclick="document.getElementById('by_meeting').style.display='none';document.getElementById('by_call').style.display='block';" value="by_call" name="contact_by" required class="filter_input"><span class="fa fa-check"></span><?= lang("call"); ?>
                                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label class="needsclick">
                                  <input type="radio" onclick="document.getElementById('by_call').style.display='none';document.getElementById('by_meeting').style.display='block';" value="by_meeting" name="contact_by" required class="filter_input"><span class="fa fa-check"></span><?= str_replace("s", "", lang("mettings")); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="by_call" style="display:none;">
                      <div class="form-group">
                          <label class="col-lg-3 control-label"><?= lang('call') . " " . lang("date"); ?> <span class="text text-danger">*</span></label>
                          <div class="col-lg-9">
                            <div class="input-group">
                                <input id="call_date" class="form-control datepicker" placeholder="<?= lang('call') . " " . lang("date"); ?>" required type="text" name="call_date" data-date-format="mm-dd-yyyy">
                                <div class="input-group-addon">
                                  <a href="javascript:;" onclick="document.getElementById('call_date').focus();"><i class="fa fa-calendar"></i></a>
                                </div>
                            </div>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-lg-3 control-label"><?= lang('contact'); ?> <span class="text text-danger">*</span></label>
                          <div class="col-lg-9">
                            <div class="input-group">
                                <input id="contact_with" class="form-control" placeholder="<?= lang('contact'); ?>" type="text" required name="contact_with">
                                <div class="input-group-addon">
                                  <a href="javascript:;" onclick="document.getElementById('contact_with').value = '<?= $lead_data->lead_name; ?>';"><i>Client him-self</i></a>
                                </div>
                            </div>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-lg-3 control-label"><?= lang('call_summary'); ?> <span class="text text-danger">*</span></label>
                          <div class="col-lg-9">
                              <textarea placeholder="<?= lang('call_summary'); ?>" name="call_summary" required class="form-control"></textarea>
                          </div>
                      </div>
                    </div>
                    <div id="by_meeting" style="display:none;">
                      <div class="form-group">
                          <label class="col-lg-3 control-label"><?= lang("metting_subject"); ?> <span class="text text-danger">*</span></label>
                          <div class="col-lg-9">
                              <input class="form-control" placeholder="<?= lang("metting_subject"); ?>" required type="text" name="metting_subject">
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-lg-3 control-label"><?= str_replace("s", "", lang("mettings")) . " " . lang("date"); ?> <span class="text text-danger">*</span></label>
                          <div class="col-lg-3">
                            <div class="input-group">
                                <input id="meeting_date" class="form-control datepicker" placeholder="<?= str_replace("s", "", lang("mettings")) . " " . lang("date"); ?>" required type="text" name="meeting_date" data-date-format="mm-dd-yyyy">
                                <div class="input-group-addon">
                                  <a href="javascript:;" onclick="document.getElementById('meeting_date').focus();"><i class="fa fa-calendar"></i></a>
                                </div>
                            </div>
                          </div>
                          <label class="col-lg-3 control-label"><?= str_replace("s", "", lang("mettings")) . " " . lang("date"); ?> <span class="text text-danger">*</span></label>
                          <div class="col-lg-3">
                            <div class="input-group">
                                <input id="meeting_time" class="form-control timepicker" placeholder="<?= str_replace("s", "", lang("mettings")) . " " . lang("time"); ?>" required type="text" name="meeting_time">
                                <div class="input-group-addon">
                                  <a href="javascript:;" onclick="document.getElementById('meeting_time').focus();"><i class="fa fa-clock-o"></i></a>
                                </div>
                            </div>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-lg-3 control-label"><?= str_replace("s", "", lang("mettings")) . " " . lang('location'); ?> <span class="text text-danger">*</span></label>
                          <div class="col-lg-9">
                            <input class="form-control" placeholder="<?= str_replace("s", "", lang("mettings")) . " " . lang('location'); ?>" type="text" required name="meeting_location">
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-lg-3 control-label"><?= lang('meeting_summary'); ?> <span class="text text-danger">*</span></label>
                          <div class="col-lg-9">
                              <textarea placeholder="<?= lang('meeting_summary'); ?>" name="meeting_summary" required class="form-control"></textarea>
                          </div>
                      </div>
                    </div>
                    <small>Task and reminder   and call/meeting information will be added.</small>
                </div>
              <?php } else if(($status_data->lead_status_id == 4) && ($status_data->lead_status == "Closed") && ($status_data->lead_type == "close")){ ?>
                  <div class="col-sm-12">
                      <div class="form-group">
                          <label class="col-lg-3 control-label"><?= lang('status'); ?> <span class="text text-danger">*</span></label>
                          <div class="col-lg-9">
                            <div class="input-group">
                                <input class="form-control" required type="text" value="<?= $status_data->lead_status; ?>" readonly>
                                <div class="input-group-addon" style="background:#f05050;">
                                  <b class="text-white"><?= strtoupper($status_data->lead_type); ?></b>
                                </div>
                            </div>
                          </div>
                      </div>
                      <div class="form-group">
                          <label class="col-lg-3 control-label"><?= lang('reason'); ?> <span class="text text-danger">*</span></label>
                          <div class="col-lg-9">
                              <textarea placeholder="<?= lang('reason'); ?>" name="status_comment" required class="form-control"></textarea>
                          </div>
                      </div>
                  </div>
              <?php } else if(($status_data->lead_status_id == 5) && ($status_data->lead_status == "Contected") && ($status_data->lead_type == "close")){ ?>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('status'); ?> <span class="text text-danger">*</span></label>
                        <div class="col-lg-9">
                          <div class="input-group">
                              <input class="form-control" required type="text" value="<?= $status_data->lead_status; ?>" readonly>
                              <div class="input-group-addon" style="background:#f05050;">
                                <b class="text-white"><?= strtoupper($status_data->lead_type); ?></b>
                              </div>
                          </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('contact_by'); ?> <span class="text text-danger">*</span></label>
                        <div class="col-lg-9">
                          <div class="checkbox c-checkbox">
                                <label class="needsclick">
                                  <input type="radio" value="by_call" name="contact_by" required class="filter_input"><span class="fa fa-check"></span><?= lang("call"); ?>
                                </label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label class="needsclick">
                                  <input type="radio" value="by_meeting" name="contact_by" required class="filter_input"><span class="fa fa-check"></span><?= str_replace("s", "", lang("mettings")); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= lang('reason'); ?> <span class="text text-danger">*</span></label>
                        <div class="col-lg-9">
                            <textarea placeholder="<?= lang('reason'); ?>" name="status_comment" required class="form-control"></textarea>
                        </div>
                    </div>
                </div>
              <?php } ?>
                <br/>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
                    <button type="submit" class="btn btn-primary"><?= lang('save') ?></button>
                </div>
            </div>
        </form>
      <?php } else { ?>
            <label class="col-lg-12 control-label">Lead data OR Status data is missing.</label>
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= lang('close') ?></button>
      <?php } ?>
    </div>
</div>
