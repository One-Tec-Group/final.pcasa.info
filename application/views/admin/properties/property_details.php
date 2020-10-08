<?php echo message_box('success'); ?>
<?php echo message_box('error'); ?>
<style>
    .note-editor .note-editable {
        height: 150px;
    }
</style>
<?php
$where = array('user_id' => $this->session->userdata('user_id'), 'module_id' => $property_details->prop_id, 'module_name' => 'properties');
$check_existing = $this->items_model->check_by($where, 'tbl_pinaction');
if (!empty($check_existing)) {
    $url = 'remove_todo/' . $check_existing->pinaction_id;
    $btn = 'danger';
    $title = lang('remove_todo');
} else {
    $url = 'add_todo_list/properties/' . $property_details->prop_id;
    $btn = 'warning';
    $title = lang('add_todo_list');
}

$can_edit = $this->items_model->can_action('tbl_properties', 'edit', array('prop_id' => $property_details->prop_id));
$can_delete = $this->items_model->can_action('tbl_properties', 'delete', array('prop_id' => $property_details->prop_id));
$comment_details = $this->db->where(array('prop_id' => $property_details->prop_id, 'comments_reply_id' => '0', 'task_attachment_id' => '0', 'uploaded_files_id' => '0'))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
$activities_info = $this->db->where(array('module' => 'properties', 'module_field_id' => $property_details->prop_id))->order_by('activity_date', 'DESC')->get('tbl_activities')->result();
$edited = can_action('55', 'edited');
$deleted = can_action('55', 'deleted');
?>
<div class="row mt-lg">
    <div class="col-sm-3">
        <?php
            $notified_reminder = count($this->db->where(array('module' => 'properties', 'module_id' => $property_details->prop_id, 'notified' => 'No'))->get('tbl_reminders')->result());
        ?>
        <!-- Tabs within a box -->
        <ul class="nav nav-pills nav-stacked navbar-custom-nav">
            <li class="<?= $active == 1 ? 'active' : '' ?>"><a href="#property_details" data-toggle="tab"><?= lang('property_details') ?></a></li>
            <li class="<?= $active == 4 ? 'active' : '' ?>">
              <a href="#property_comments" data-toggle="tab"><?= lang('comments') ?><strong class="pull-right"><?= (!empty($comment_details) ? count($comment_details) : null) ?></strong></a>
            </li>
            <li class="<?= $active == 5 ? 'active' : '' ?>">
                <a href="#property_attachments" data-toggle="tab"><?= lang('attachment') ?><strong class="pull-right"><?= (!empty($project_files_info) ? count($project_files_info) : null) ?></strong></a>
            </li>
            <li class="<?= $url == 'reminder' ? 'active' : '' ?>">
                <a href="#reminder" data-toggle="tab" aria-expanded="false"><?= lang('reminder') ?><strong class="pull-right"><?= (!empty($notified_reminder) ? $notified_reminder : null) ?></strong></a>
            </li>
            <li class="<?= $active == 6 ? 'active' : '' ?>">
                <a href="#activities" data-toggle="tab"><?= lang('activities') ?><strong class="pull-right"><?= (!empty($activities_info) ? count($activities_info) : null) ?></strong></a>
            </li>
        </ul>
    </div>
    <div class="col-sm-9">
        <div class="tab-content" style="border: 0;padding:0;">
            <!-- Task Details tab Starts -->
            <div class="tab-pane <?= $active == 1 ? 'active' : '' ?>" id="property_details" style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php
                            if (!empty($property_details->lead_name)) {
                                echo ((!empty($property_details->salutaiton)) ? $property_details->salutaiton . '. ' : "") . $property_details->lead_name;
                            }
                            ?>
                            <div class="pull-right ml-sm " style="margin-top: -6px">
                                <a data-toggle="tooltip" data-placement="top" title="<?= $title ?>"
                                   href="<?= base_url() ?>admin/projects/<?= $url ?>"
                                   class="btn-xs btn btn-<?= $btn ?>"><i class="fa fa-thumb-tack"></i></a>
                            </div>
                            <span class="btn-xs pull-right"></span>
                        </h3>
                    </div>
                    <div class="panel-body row form-horizontal task_details">
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('lead_name') ?>: </strong>
                                </label>
                                <p class="form-control-static"><?php
                                    if (!empty($property_details->lead_name)) {
                                        echo ((!empty($property_details->salutaiton)) ? $property_details->salutaiton . '. ' : "") . $property_details->lead_name;
                                    }
                                    ?></p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('phone') ?>: </strong></label>
                                <p class="form-control-static"><?php
                                $property_details->mobile = ltrim($property_details->mobile, '0');
                                $phone_information =  $this->db->get('tbl_countries')->result();
                                foreach ($phone_information as $single_phone) {
                                    if ((strlen($single_phone->calling_code) + 1) == strlen($single_phone->phone_code)) {
                                        if (substr($property_details->mobile, 0, strlen($single_phone->calling_code)) === $single_phone->calling_code || substr($property_details->mobile, 0, strlen($single_phone->phone_code)) === $single_phone->phone_code) {
                                            $d = new DateTime("now", new DateTimeZone($single_phone->time_zone));
                                            echo '<img class="img-rounded" src="' . base_url("assets/img/flags/" . $single_phone->flag) . '" style="margin-right: 10px;" height="20px"><i>' . $property_details->mobile . '</i>';
                                            echo '<small> (<i>' . $single_phone->long_name . '</i> - <i>Time: ' . $d->format("h:i A") . '</i>)</small>';
                                            break;
                                        }
                                    }
                                }
                                    ?></p>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('email') ?>: </strong></label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($property_details->email)) {
                                        echo $property_details->email;
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('phone_2') ?>: </strong>
                                </label>
                                <p class="form-control-static"><?php
                                $property_details->phone2 = ltrim($property_details->phone2, '0');
                                $phone_information =  $this->db->get('tbl_countries')->result();
                                foreach ($phone_information as $single_phone) {
                                    if ((strlen($single_phone->calling_code) + 1) == strlen($single_phone->phone_code)) {
                                        if (substr($property_details->phone2, 0, strlen($single_phone->calling_code)) === $single_phone->calling_code || substr($property_details->phone2, 0, strlen($single_phone->phone_code)) === $single_phone->phone_code) {
                                            $d = new DateTime("now", new DateTimeZone($single_phone->time_zone));
                                            echo '<img class="img-rounded" src="' . base_url("assets/img/flags/" . $single_phone->flag) . '" style="margin-right: 10px;" height="20px"><i>' . $property_details->phone2 . '</i>';
                                            echo '<small> (<i>' . $single_phone->long_name . '</i> - <i>Time: ' . $d->format("h:i A") . '</i>)</small>';
                                            break;
                                        }
                                    }
                                }
                                    ?></p>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('email_2') ?>: </strong></label>
                                <div class="pull-left">
                                  <p class="form-control-static">
                                      <?php
                                      if (!empty($property_details->email2)) {
                                          echo $property_details->email2;
                                      }
                                      ?>
                                  </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('phone_3') ?>: </strong></label>
                                <p class="form-control-static"><?php
                                $property_details->phone3 = ltrim($property_details->phone3, '0');
                                $phone_information =  $this->db->get('tbl_countries')->result();
                                foreach ($phone_information as $single_phone) {
                                    if ((strlen($single_phone->calling_code) + 1) == strlen($single_phone->phone_code)) {
                                        if (substr($property_details->phone3, 0, strlen($single_phone->calling_code)) === $single_phone->calling_code || substr($property_details->phone3, 0, strlen($single_phone->phone_code)) === $single_phone->phone_code) {
                                            $d = new DateTime("now", new DateTimeZone($single_phone->time_zone));
                                            echo '<img class="img-rounded" src="' . base_url("assets/img/flags/" . $single_phone->flag) . '" style="margin-right: 10px;" height="20px"><i>' . $property_details->phone3 . '</i>';
                                            echo '<small> (<i>' . $single_phone->long_name . '</i> - <i>Time: ' . $d->format("h:i A") . '</i>)</small>';
                                            break;
                                        }
                                    }
                                }
                                    ?></p>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('email_3') ?>: </strong></label>
                                <div class="pull-left">
                                  <p class="form-control-static">
                                      <?php
                                      if (!empty($property_details->email3)) {
                                          echo $property_details->email3;
                                      }
                                      ?>
                                  </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('phone_4') ?>: </strong></label>
                                <p class="form-control-static"><?php
                                $property_details->phone4 = ltrim($property_details->phone4, '0');
                                $phone_information =  $this->db->get('tbl_countries')->result();
                                foreach ($phone_information as $single_phone) {
                                    if ((strlen($single_phone->calling_code) + 1) == strlen($single_phone->phone_code)) {
                                        if (substr($property_details->phone4, 0, strlen($single_phone->calling_code)) === $single_phone->calling_code || substr($property_details->phone4, 0, strlen($single_phone->phone_code)) === $single_phone->phone_code) {
                                            $d = new DateTime("now", new DateTimeZone($single_phone->time_zone));
                                            echo '<img class="img-rounded" src="' . base_url("assets/img/flags/" . $single_phone->flag) . '" style="margin-right: 10px;" height="20px"><i>' . $property_details->phone4 . '</i>';
                                            echo '<small> (<i>' . $single_phone->long_name . '</i> - <i>Time: ' . $d->format("h:i A") . '</i>)</small>';
                                            break;
                                        }
                                    }
                                }
                                    ?></p>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('lead_source') ?>: </strong></label>
                                <div class="pull-left">
                                  <p class="form-control-static">
                                      <?php
                                      if (!empty($property_details->lead_source_id)) {
                                        $source = $this->db->where('lead_source_id', $property_details->lead_source_id)->get('tbl_lead_source')->row();
                                        echo ((!empty($source)) ? $source->lead_source : "");
                                      }
                                      ?>
                                  </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('lead_category') ?>: </strong></label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($property_details->lead_category_id)) {
                                        $category = $this->db->where('lead_category_id', $property_details->lead_category_id)->get('tbl_lead_category')->row();
                                        echo ((!empty($category)) ? $category->lead_category : "");
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('lead_status') ?>: </strong></label>
                                <div class="pull-left">
                                    <?php
                                    if (!empty($property_details->lead_status_id)) {
                                        $lead_status = $this->db->where('lead_status_id', $property_details->lead_status_id)->get('tbl_lead_status')->row();
                                        if ($lead_status->lead_type == 'open') {
                                            $status = "<span class='label label-success'>" . lang($lead_status->lead_type) . "</span>";
                                        } else {
                                            $status = "<span class='label label-warning'>" . lang($lead_status->lead_type) . "</span>";
                                        }
                                        ?>
                                        <p class="form-control-static"><?= $lead_status->lead_status . ' ' . $status ?></p>
                                    <?php }
                                    ?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('skype_id') ?>: </strong></label>
                                <?php if (!empty($property_details->skype)) { ?>
                                    <a href="skype:'<?= $property_details->skype; ?>'"><p class="form-control-static"><?= $property_details->skype; ?></p></a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('fax') ?>: </strong></label>
                                <div class="pull-left">
                                  <p class="form-control-static">
                                      <?php
                                      if (!empty($property_details->fax)) {
                                          echo $property_details->fax;
                                      }
                                      ?>
                                  </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('landline') ?>: </strong></label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($property_details->phone)) {
                                        echo $property_details->phone;
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('organization') ?>: </strong></label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($property_details->organization)) {
                                        echo $property_details->organization;
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('contact_name') ?>: </strong> </label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($property_details->contact_name)) {
                                        echo $property_details->contact_name;
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('lead_date_of_birth') ?>: </strong></label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($property_details->date_of_birth)) {
                                        date("d-m-Y", strtotime($property_details->date_of_birth));
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('lead_passport') ?>: </strong></label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($property_details->passport_number)) {
                                        echo $property_details->passport_number;
                                    }
                                    if (!empty($property_details->passport_expire)) {
                                        echo ' - ' . date("d-m-Y", strtotime($property_details->passport_expire));
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('address') ?>: </strong></label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($property_details->address)) {
                                        echo $property_details->address;
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('city') ?>: </strong></label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($property_details->city)) {
                                        echo $property_details->city;
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('state') ?>: </strong></label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($property_details->state)) {
                                        echo $property_details->state;
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('country') ?>: </strong></label>
                                <p class="form-control-static">
                                    <?php
                                    if (!empty($property_details->country)) {
                                        echo $property_details->country;
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('facebook_profile_link') ?>: </strong></label>
                                <?php if (!empty($property_details->skype)) { ?>
                                    <a href="//'<?= $property_details->facebook; ?>'"><p class="form-control-static"><?= $property_details->facebook; ?></p></a>
                                <?php } ?>
                            </div>
                            <div class="col-sm-6">
                                <label class="control-label col-sm-5"><strong><?= lang('twitter_profile_link') ?>: </strong></label>
                                <?php if (!empty($property_details->skype)) { ?>
                                    <a href="//'<?= $property_details->twitter; ?>'"><p class="form-control-static"><?= $property_details->twitter; ?></p></a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group col-sm-12">
                                    <?php if ($property_details->permission != '-') { ?>
                                <div class="form-group  col-sm-6">
                                    <label class="control-label col-sm-5"><strong><?= lang('participants') ?>: </strong></label>
                                    <div class="col-sm-7 ">
                                        <?php
                                        if ($property_details->permission != 'all') {
                                            $get_permission = json_decode($property_details->permission);
                                            if (!empty($get_permission)) :
                                                foreach ($get_permission as $permission => $v_permission) :
                                                    $user_info = $this->db->where(array('user_id' => $permission))->get('tbl_users')->row();
                                                    if ($user_info->role_id == 1) {
                                                        $label = 'circle-danger';
                                                    } else {
                                                        $label = 'circle-success';
                                                    }
                                                    $profile_info = $this->db->where(array('user_id' => $permission))->get('tbl_account_details')->row();
                                                    ?>
                                                    <a href="#" data-toggle="tooltip" data-placement="top" title="<?= $profile_info->fullname ?>">
                                                        <img src="<?= base_url() . $profile_info->avatar ?>" class="img-circle img-xs" alt="">
                                                        <span style="margin: 0px 0 8px -10px;" class="circle <?= $label ?>  circle-lg"></span>
                                                    </a>
                                                        <?php
                                                    endforeach;
                                                endif;
                                            } else {
                                                ?>
                                            <p class="form-control-static"><strong><?= lang('everyone') ?></strong>
                                                <i title="<?= lang('permission_for_all') ?>" class="fa fa-question-circle" data-toggle="tooltip" data-placement="top"></i>
                                                <?php
                                            }
                                            ?>
                                    </div>
                                </div>
                              <?php } ?>
                        </div>
                        <div class="col-sm-12">
                            <blockquote style="font-size: 12px;"><?php
                          if (!empty($property_details->notes)) {
                              echo $property_details->notes;
                          }
                          ?> </blockquote>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Task Details tab Ends -->

            <?php $comment_type = 'properties'; ?>
            <div class="tab-pane <?= $active == 4 ? 'active' : '' ?>" id="property_comments" style="position: relative;">
                <div class="panel panel-custom">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= lang('comments') ?></h3>
                    </div>
                    <div class="panel-body chat" id="chat-box">
                      <?php echo form_open(base_url("admin/properties/save_comments"), array("id" => $comment_type . "-comment-form", "class" => "form-horizontal general-form", "enctype" => "multipart/form-data", "role" => "form")); ?>
                        <input type="hidden" name="prop_id" value="<?php
                                if (!empty($property_details->prop_id)) {
                                    echo $property_details->prop_id;
                                }
                                ?>" class="form-control">
                        <div class="form-group">
                            <div class="col-sm-12">
                                <?php
                                echo form_textarea(array(
                                    "id" => "comment_description",
                                    "name" => "comment",
                                    "class" => "form-control comment_description",
                                    "placeholder" => $property_details->prop_title . ' ' . lang('comments'),
                                    "data-rule-required" => true,
                                    "rows" => 4,
                                    "data-msg-required" => lang("field_required"),
                                ));
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div id="comments_file-dropzone" class="dropzone mb15"></div>
                                <div id="comments_file-dropzone-scrollbar">
                                    <div id="comments_file-previews">
                                        <div id="file-upload-row" class="mt pull-left">
                                            <div class="preview box-content pr-lg" style="width:100px;">
                                                <span data-dz-remove class="pull-right" style="cursor: pointer">
                                                    <i class="fa fa-times"></i>
                                                </span>
                                                <img data-dz-thumbnail class="upload-thumbnail-sm"/>
                                                <input class="file-count-field" type="hidden" name="files[]"
                                                       value=""/>
                                                <div class="mb progress progress-striped upload-progress-sm active mt-sm" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                                    <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="pull-right">
                                    <button type="submit" id="file-save-button" class="btn btn-primary"><?= lang('post_comment') ?></button>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <?php
                        echo form_close();
                        $comment_reply_type = 'property-reply';
                        ?>
                        <?php $this->load->view('admin/properties/comments_list', array('comment_details' => $comment_details)) ?>
                        <script type="text/javascript">
                            $(document).ready(function () {
                                $('#file-save-button').on('click', function (e) {
                                    var ubtn = $(this);
                                    ubtn.html('Please wait...');
                                    ubtn.addClass('disabled');
                                });
                                $("#<?php echo $comment_type; ?>-comment-form").appForm({
                                    isModal: false,
                                    onSuccess: function (result) {
                                        $(".comment_description").val("");
                                        $(".dz-complete").remove();
                                        $('#file-save-button').removeClass("disabled").html('<?= lang('post_comment') ?>');
                                        $(result.data).insertAfter("#<?php echo $comment_type; ?>-comment-form");
                                        toastr[result.status](result.message);
                                    }
                                });
                                fileSerial = 0;
                                // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
                                var previewNode = document.querySelector("#file-upload-row");
                                previewNode.id = "";
                                var previewTemplate = previewNode.parentNode.innerHTML;
                                previewNode.parentNode.removeChild(previewNode);
                                Dropzone.autoDiscover = false;
                                var projectFilesDropzone = new Dropzone("#comments_file-dropzone", {
                                    url: "<?= base_url() ?>admin/global_controller/upload_file",
                                    thumbnailWidth: 80,
                                    thumbnailHeight: 80,
                                    parallelUploads: 20,
                                    previewTemplate: previewTemplate,
                                    dictDefaultMessage: '<?php echo lang("file_upload_instruction"); ?>',
                                    autoQueue: true,
                                    previewsContainer: "#comments_file-previews",
                                    clickable: true,
                                    accept: function (file, done) {
                                        if (file.name.length > 200) {
                                            done("Filename is too long.");
                                            $(file.previewTemplate).find(".description-field").remove();
                                        }
                                        //validate the file
                                        $.ajax({
                                            url: "<?= base_url() ?>admin/global_controller/validate_project_file",
                                            data: {file_name: file.name, file_size: file.size},
                                            cache: false,
                                            type: 'POST',
                                            dataType: "json",
                                            success: function (response) {
                                                if (response.success) {
                                                    fileSerial++;
                                                    $(file.previewTemplate).find(".description-field").attr("name", "comment_" + fileSerial);
                                                    $(file.previewTemplate).append("<input type='hidden' name='file_name_" + fileSerial + "' value='" + file.name + "' />\n\
                                     <input type='hidden' name='file_size_" + fileSerial + "' value='" + file.size + "' />");
                                                    $(file.previewTemplate).find(".file-count-field").val(fileSerial);
                                                    done();
                                                } else {
                                                    $(file.previewTemplate).find("input").remove();
                                                    done(response.message);
                                                }
                                            }
                                        });
                                    },
                                    processing: function () {
                                        $("#file-save-button").prop("disabled", true);
                                    },
                                    queuecomplete: function () {
                                        $("#file-save-button").prop("disabled", false);
                                    },
                                    fallback: function () {
                                        //add custom fallback;
                                        $("body").addClass("dropzone-disabled");
                                        $('.modal-dialog').find('[type="submit"]').removeAttr('disabled');

                                        $("#comments_file-dropzone").hide();

                                        $("#file-modal-footer").prepend("<button id='add-more-file-button' type='button' class='btn  btn-default pull-left'><i class='fa fa-plus-circle'></i> " + "<?php echo lang("add_more"); ?>" + "</button>");

                                        $("#file-modal-footer").on("click", "#add-more-file-button", function () {
                                            var newFileRow = "<div class='file-row pb pt10 b-b mb10'>"
                                                    + "<div class='pb clearfix '><button type='button' class='btn btn-xs btn-danger pull-left mr remove-file'><i class='fa fa-times'></i></button> <input class='pull-left' type='file' name='manualFiles[]' /></div>"
                                                    + "<div class='mb5 pb5'><input class='form-control description-field'  name='comment[]'  type='text' style='cursor: auto;' placeholder='<?php echo lang("comment") ?>' /></div>"
                                                    + "</div>";
                                            $("#comments_file-previews").prepend(newFileRow);
                                        });
                                        $("#add-more-file-button").trigger("click");
                                        $("#comments_file-previews").on("click", ".remove-file", function () {
                                            $(this).closest(".file-row").remove();
                                        });
                                    },
                                    success: function (file) {
                                        setTimeout(function () {
                                            $(file.previewElement).find(".progress-striped").removeClass("progress-striped").addClass("progress-bar-success");
                                        }, 1000);
                                    }
                                });

                            })
                        </script>
                    </div>
                </div>
            </div>
            <!-- Task Comments Panel Ends--->
            <!-- Task Attachment Panel Starts --->
            <div class="tab-pane <?= $active == 5 ? 'active' : '' ?>" id="property_attachments">
                <div class="panel panel-custom">
                    <div class="panel-heading mb0">
                          <?php
                          $attach_list = $this->session->userdata('property_media_view');
                          if (empty($attach_list)) {
                              $attach_list = 'list_view';
                          }
                          ?>
                        <h3 class="panel-title"><?= lang('attach_file_list') ?>
                            <a data-toggle="tooltip" data-placement="top" href="<?= base_url('admin/global_controller/download_all_attachment/prop_id/' . $property_details->prop_id) ?>"
                               class="btn btn-default" title="<?= lang('download') . ' ' . lang('all') . ' ' . lang('attachment') ?>"><i class="fa fa-cloud-download"></i></a>

                            <a data-toggle="tooltip" data-placement="top" class="btn btn-default toggle-media-view <?= (!empty($attach_list) && $attach_list == 'list_view' ? 'hidden' : '') ?>"
                               data-type="list_view" title="<?= lang('switch_to') . ' ' . lang('media_view') ?>"><i class="fa fa-image"></i></a>

                            <a data-toggle="tooltip" data-placement="top" class="btn btn-default toggle-media-view <?= (!empty($attach_list) && $attach_list == 'media_view' ? 'hidden' : '') ?>"
                               data-type="media_view" title="<?= lang('switch_to') . ' ' . lang('list_view') ?>"><i class="fa fa-list"></i></a>

                            <div class="pull-right hidden-print" style="padding-top: 0px;padding-bottom: 8px">
                                <a href="<?= base_url() ?>admin/properties/new_attachment/<?= $property_details->prop_id ?>" class="text-purple text-sm" data-toggle="modal" data-placement="top"
                                   data-target="#myModal_extra_lg">
                                <i class="fa fa-plus "></i> <?= lang('new') . ' ' . lang('attachment') ?></a>
                            </div>
                        </h3>
                    </div>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $(".toggle-media-view").click(function () {
                                $(".media-view-container").toggleClass('hidden');
                                $(".toggle-media-view").toggleClass('hidden');
                                $(".media-list-container").toggleClass('hidden');
                                var type = $(this).data('type');
                                var module = 'property';
                                $.get('<?= base_url() ?>admin/global_controller/set_media_view/' + type + '/' + module, function (response) {
                                });
                            });
                        });
                    </script>
                      <?php
                      $this->load->helper('file');
                      if (empty($project_files_info)) {
                          $project_files_info = array();
                      }
                      ?>
                    <div class="p media-view-container <?= (!empty($attach_list) && $attach_list == 'media_view' ? 'hidden' : '') ?>">
                        <div class="row">
                          <?php $this->load->view('admin/properties/attachment_list', array('project_files_info' => $project_files_info)) ?>
                        </div>
                    </div>
                    <div class="media-list-container <?= (!empty($attach_list) && $attach_list == 'list_view' ? 'hidden' : '') ?>">
                          <?php
                          if (!empty($project_files_info)) {
                              foreach ($project_files_info as $key => $v_files_info) {
                                  ?>
                                <div class="panel-group" id="media_list_container-<?= $files_info[$key]->task_attachment_id ?>" style="margin:8px 0px;" role="tablist" aria-multiselectable="true">
                                    <div class="box box-info" style="border-radius: 0px">
                                        <div class="p pb-sm" role="tab" id="headingOne" style="border-bottom: 1px solid #dde6e9">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion" href="#<?php echo $key ?>" aria-expanded="true" aria-controls="collapseOne">
                                                    <strong class="text-alpha-inverse"><?php echo $files_info[$key]->title; ?> </strong>
                                                    <small style="color:#ffffff " class="pull-right">
                                                  <?php if ($files_info[$key]->user_id == $this->session->userdata('user_id')) { ?>
                                                      <?php echo ajax_anchor(base_url("admin/properties/delete_files/" . $files_info[$key]->task_attachment_id), "<i class='text-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#media_list_container-" . $files_info[$key]->task_attachment_id)); ?>
                                                  <?php } ?></small>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="<?php echo $key ?>" class="panel-collapse collapse <?php
                                          if (!empty($in) && $files_info[$key]->files_id == $in) {
                                              echo 'in';
                                          }
                                          ?>" role="tabpanel" aria-labelledby="headingOne">
                                            <div class="content p">
                                                <div class="table-responsive">
                                                    <table id="table-files" class="table table-striped ">
                                                        <thead>
                                                            <tr>
                                                                <th><?= lang('files') ?></th>
                                                                <th class=""><?= lang('size') ?></th>
                                                                <th><?= lang('date') ?></th>
                                                                <th><?= lang('total') . ' ' . lang('comments') ?></th>
                                                                <th><?= lang('uploaded_by') ?></th>
                                                                <th><?= lang('action') ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                                        <?php
                                                                        $this->load->helper('file');
                                                                        if (!empty($v_files_info)) {
                                                                            foreach ($v_files_info as $v_files) {
                                                                                $user_info = $this->db->where(array('user_id' => $files_info[$key]->user_id))->get('tbl_users')->row();
                                                                                $total_file_comment = count($this->db->where(array('uploaded_files_id' => $v_files->uploaded_files_id))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result());
                                                                                ?>
                                                                    <tr class="file-item">
                                                                        <td data-toggle="tooltip" data-placement="top" data-original-title="<?= $files_info[$key]->description ?>">
                                                                            <?php if ($v_files->is_image == 1) : ?>
                                                                                <div class="file-icon"><a data-toggle="modal" data-target="#myModal_extra_lg" href="<?= base_url() ?>admin/properties/attachment_details/r/<?= $files_info[$key]->task_attachment_id . '/' . $v_files->uploaded_files_id ?>">
                                                                                        <img style="width: 50px;border-radius: 5px;" src="<?= base_url() . $v_files->files ?>"/></a>
                                                                                </div>
                                                                              <?php else : ?>
                                                                                <div class="file-icon"><i class="fa fa-file-o"></i>
                                                                                    <a data-toggle="modal" data-target="#myModal_extra_lg" href="<?= base_url() ?>admin/properties/attachment_details/r/<?= $files_info[$key]->task_attachment_id . '/' . $v_files->uploaded_files_id ?>"><?= $v_files->file_name ?></a>
                                                                                </div>
                                                                              <?php endif; ?>
                                                                        </td>
                                                                        <td class=""><?= $v_files->size ?>Kb</td>
                                                                        <td class="col-date"><?= date('Y-m-d' . "<br/> h:m A", strtotime($files_info[$key]->upload_time)); ?></td>
                                                                        <td class=""><?= $total_file_comment ?></td>
                                                                        <td>
                                                                    <?= $user_info->username ?>
                                                                        </td>
                                                                        <td>
                                                                            <a class="btn btn-xs btn-dark" data-toggle="tooltip" data-placement="top" title="Download" href="<?= base_url() ?>admin/properties/download_files/<?= $v_files->uploaded_files_id ?>"><i class="fa fa-download"></i></a>
                                                                        </td>
                                                                    </tr>
                                                                          <?php
                                                                      }
                                                                  } else {
                                                                      ?>
                                                                <tr>
                                                                    <td colspan="5">
                                                                      <?= lang('nothing_to_display') ?>
                                                                    </td>
                                                                </tr>
                                                              <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                  <?php
                              }
                          }
                          ?>
                    </div>
                </div>
            </div>

            <div class="tab-pane <?= $active == 7 ? 'active' : '' ?>" id="activities" style="position: relative;">
                <div class="tab-pane " id="activities">
                    <div class="panel panel-custom">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?= lang('activities') ?>
                            <?php
                            $role = $this->session->userdata('user_type');
                            if ($role == 1) {
                                ?>
                                    <span class="btn-xs pull-right">
                                        <a href="<?= base_url() ?>admin/tasks/claer_activities/properties/<?= $property_details->prop_id ?>"><?= lang('clear') . ' ' . lang('activities') ?></a>
                                    </span>
                                  <?php } ?>
                            </h3>
                        </div>
                        <div class="panel-body" id="chat-box">
                            <?php
                            if (!empty($activities_info)) {
                                foreach ($activities_info as $v_activities) {
                                    $profile_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_account_details')->row();
                                    $user_info = $this->db->where(array('user_id' => $v_activities->user))->get('tbl_users')->row();
                                    ?>
                                    <div class="timeline-2">
                                        <div class="time-item">
                                            <div class="item-info">
                                                <small data-toggle="tooltip" data-placement="top" title="<?= display_datetime($v_activities->activity_date) ?>" class="text-muted"><?= time_ago($v_activities->activity_date); ?></small>
                                                <p><strong>
                                                  <?php if (!empty($profile_info)) { ?>
                                                            <a href="<?= base_url() ?>admin/user/user_details/<?= $profile_info->user_id ?>" class="text-info"><?= $profile_info->fullname ?></a>
                                                             <?php } ?>
                                                    </strong> <?= sprintf(lang($v_activities->activity)) ?>
                                                    <strong><?= $v_activities->value1 ?></strong>
                                                    <?php if (!empty($v_activities->value2)) { ?>
                                                    <p class="m0 p0"><strong><?= $v_activities->value2 ?></strong></p>
                                                  <?php } ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                      <?php
                                  }
                              }
                              ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane <?= $active == 10 ? 'active' : '' ?>" id="reminder">
                <div class="nav-tabs-custom">
                    <!-- Tabs within a box -->
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#reminder_manage" data-toggle="tab"><?= lang('reminder') . ' ' . lang('list') ?></a></li>
                        <li class=""><a href="#reminder_create" data-toggle="tab"><?= lang('set') . ' ' . lang('reminder') ?></a></li>
                    </ul>
                    <div class="tab-content bg-white">
                        <!-- ************** general *************-->
                        <div class="tab-pane active" id="reminder_manage">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?= lang('description') ?></th>
                                            <th><?= lang('date') ?></th>
                                            <th><?= lang('remind') ?></th>
                                            <th><?= lang('notified') ?></th>
                                            <th class="col-options no-sort"><?= lang('action') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $all_reminder = $this->db->where(array('module' => 'properties', 'module_id' => $property_details->prop_id))->get('tbl_reminders')->result();
                                        if (!empty($all_reminder)) {
                                            foreach ($all_reminder as $v_reminder):
                                                $remind_user_info = $this->db->where('user_id', $v_reminder->user_id)->get('tbl_account_details')->row();
                                                ?>
                                                <tr id="property_reminder_<?= $v_reminder->reminder_id ?>">
                                                    <td><?= $v_reminder->description ?></td>
                                                    <td><?= strftime(config_item('date_format'), strtotime($v_reminder->date)) . ' ' . display_time($v_reminder->date) ?></td>
                                                    <td>
                                                        <a href="<?= base_url() ?>admin/user/user_details/<?= $v_reminder->user_id ?>"> <?= $remind_user_info->fullname ?></a>
                                                    </td>
                                                    <td><?= $v_reminder->notified ?></td>
                                                    <td>
                                                <?php echo ajax_anchor(base_url("admin/invoice/delete_reminder/" . $v_reminder->module . '/' . $v_reminder->module_id . '/' . $v_reminder->reminder_id), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#lproperty_reminder_" . $v_reminder->reminder_id)); ?>
                                                    </td>
                                                </tr>
                                                <?php
                                              endforeach;
                                            } else {
                                              ?>
                                            <tr>
                                                <td colspan="5"><?= lang('nothing_to_display') ?></td>
                                            </tr>
                                          <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="reminder_create">
                            <form role="form" data-parsley-validate="" novalidate="" enctype="multipart/form-data" id="form" action="<?php echo base_url(); ?>admin/invoice/reminder/property/<?= $property_details->prop_id ?>/<?php
                                  if (!empty($reminder_info)) {
                                      echo $reminder_info->reminder_id;
                                  }
                                  ?>" method="post" class="form-horizontal  ">
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('date_to_notified') ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-5">
                                        <div class="input-group">
                                            <input type="text" name="date" class="form-control datetimepicker" value="<?php
                                                   if (!empty($reminder_info->date)) {
                                                       echo $reminder_info->date;
                                                   } else {
                                                       echo date('Y-m-d h:i');
                                                   }
                                                   ?>" data-date-min-date="<?= date('Y-m-d'); ?>">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End discount Fields -->
                                <div class="form-group terms">
                                    <label class="col-lg-3 control-label"><?= lang('description') ?> </label>
                                    <div class="col-lg-5">
                                        <textarea name="description" class="form-control"><?php
                                            if (!empty($reminder_info)) {
                                                echo $reminder_info->description;
                                            }
                                            ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"><?= lang('set_reminder_to') ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-5">
                                        <select class="form-control select_box" name="user_id" style="width: 100%">
                                          <?php
                                          $all_user = $this->db->where('company', 0)->get('tbl_account_details')->result();
                                          foreach ($all_user as $v_users) {
                                              ?>
                                                                                          <option <?php
                                              if (!empty($reminder_info)) {
                                                  echo $reminder_info->user_id == $v_users->user_id ? 'selected' : null;
                                              }
                                              ?> value="<?= $v_users->user_id ?>"><?= $v_users->fullname ?></option>
                                                <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group terms">
                                    <label class="col-lg-3 control-label"></label>
                                    <div class="col-lg-5">
                                        <div class="checkbox c-checkbox">
                                            <label class="needsclick">
                                                <input type="checkbox" value="Yes"
                                                <?php
                                                if (!empty($reminder_info) && $reminder_info->notify_by_email == 'Yes') {
                                                    echo 'checked';
                                                }
                                                ?> name="notify_by_email">
                                                <span class="fa fa-check"></span>
                                                <?= lang('send_also_email_this_reminder') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 control-label"></label>
                                    <div class="col-lg-5">
                                        <button type="submit" class="btn btn-purple"><?= lang('upload') ?></button>
                                        <button type="button" class="btn btn-primary pull-right" data-dismiss="modal"><?= lang('close') ?></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <link rel="stylesheet" href="<?= base_url() ?>assets/plugins/datetimepicker/jquery.datetimepicker.min.css">
                      <?php include_once 'assets/plugins/datetimepicker/jquery.datetimepicker.full.php'; ?>
                <script type="text/javascript">
                    init_datepicker();
                    // Date picker init with selected timeformat from settings
                    function init_datepicker() {
                        var datetimepickers = $('.datetimepicker');
                        if (datetimepickers.length == 0) {
                            return;
                        }
                        var opt_time;
                        // Datepicker with time
                        $.each(datetimepickers, function () {
                            opt_time = {
                                lazyInit: true,
                                scrollInput: false,
                                format: 'Y-m-d H:i',
                            };

                            opt_time.formatTime = 'H:i';
                            // Check in case the input have date-end-date or date-min-date
                            var max_date = $(this).data('date-end-date');
                            var min_date = $(this).data('date-min-date');
                            if (max_date) {
                                opt_time.maxDate = max_date;
                            }
                            if (min_date) {
                                opt_time.minDate = min_date;
                            }
                            // Init the picker
                            $(this).datetimepicker(opt_time);
                        });
                    }
                </script>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var maxAppend = 0;
        $("#add_more").click(function () {
            if (maxAppend >= 4) {
                alert("Maximum 5 File is allowed");
            } else {
                var add_new = $('<div class="form-group" style="margin-bottom: 0px">\n\
                    <label for="field-1" class="col-sm-3 control-label"><?= lang('upload_file') ?></label>\n\
                      <div class="col-sm-5">\n\
                      <div class="fileinput fileinput-new" data-provides="fileinput">\n\
                      <span class="btn btn-default btn-file"><span class="fileinput-new" >Select file</span><span class="fileinput-exists" >Change</span><input type="file" name="task_files[]" ></span> <span class="fileinput-filename"></span><a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none;">&times;</a></div></div>\n\<div class="col-sm-2">\n\<strong>\n\
                      <a href="javascript:void(0);" class="remCF"><i class="fa fa-times"></i>&nbsp;Remove</a></strong></div>');
                maxAppend++;
                $("#add_new").append(add_new);
            }
        });

        $("#add_new").on('click', '.remCF', function () {
            $(this).parent().parent().parent().remove();
        });
    });
</script>
