<div class="panel panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= lang("new") . " " . lang('email') ?></h4>
    </div>
    <?php
      $lead_info = $this->db->select("`tbl_leads`.`email`, `tbl_leads`.`salutaiton`, `tbl_leads`.`lead_name`")->where("leads_id", $lead_id)->get("tbl_leads")->row();
     ?>
    <div class="modal-body wrap-modal wrap">
        <form id="new_email_form" data-parsley-validate="" novalidate="" method="post" class="form-horizontal">
            <input type="hidden" name="lead_id" value="<?= $lead_id; ?>">
            <input type="hidden" name="lead_email" value="<?= $lead_info->email; ?>">
            <input type="hidden" name="lead_name" value="<?= (($lead_info->salutaiton != NULL && $lead_info->salutaiton  != "") ? $lead_info->salutaiton . ". " : "") . $lead_info->lead_name; ?>">
            <div class="form-group">
                <label for="field-1" class="col-sm-4 control-label"><?= lang('lead_name') ?>: </label>
                <div class="col-sm-7">
                    <input type="text" value="<?= (($lead_info->salutaiton != NULL && $lead_info->salutaiton  != "") ? $lead_info->salutaiton . ". " : "") . $lead_info->lead_name; ?>" readonly disabled style="width:100%;background:white;border:0;border-bottom:1px solid #dde6e9;">
                </div>
            </div>
            <div class="form-group">
                <label for="field-1" class="col-sm-4 control-label"><?= lang('email') ?>: </label>
                <div class="col-sm-7">
                    <input type="text" value="<?= $lead_info->email; ?>" readonly disabled style="width:100%;background:white;border:0;border-bottom:1px solid #dde6e9;">
                </div>
            </div>
            <div class="form-group">
                <label for="field-1" class="col-sm-4 control-label"><?= lang('emailTemplate') ?>: </label>
                <div class="col-sm-7">
                    <select name="emailTenplate" id="emailTenplate" class="form-control">
                        <option value selected disabled><?= lang('none') ?></option>
                        <?= $emailTemplates; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="field-1" class="col-sm-4 control-label"><?= lang('subject') ?>: </label>
                <div class="col-sm-7">
                    <input type="text" value="" class="form-control" placeholder="E-MAIL SUBJECT" name="emailSubject">
                </div>
            </div>
            <div class="btn-bottom-toolbar text-right">
                <button type="button" id="file-send-button-email" data-subject="email" data-url="admin/CrmMailServerAPI/lead_send_email/" class="btn btn-sm btn-primary"><?= lang('send') ?></button>
            </div>
        </form>
    </div>
</div>
