<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Properties extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('items_model');
        $this->load->helper('ckeditor');
        $this->data['ckeditor'] = array(
            'id' => 'ck_editor',
            'path' => 'asset/js/ckeditor',
            'config' => array(
                'toolbar' => "Full",
                'width' => "99.8%",
                'height' => "400px"
            )
        );
    }

    public function index($id = NULL) {
        $data['title'] = lang('all_properties');
        if (!empty($id)) {
                $data['active'] = 2;
                $can_edit = $this->items_model->can_action('tbl_properties', 'edit', array('prop_id' => $id));
                if (!empty($can_edit)) {
                    $data['property_info'] = $this->items_model->check_by(array('prop_id' => $id), 'tbl_properties');
                }
        } else {
            $data['active'] = 1;
        }
        $data['nationalities'] = $this->db->get('tbl_countries')->result();
        $data['assign_user'] = $this->items_model->allowad_user('156');
        $data['properties_count'] = $this->db->get('tbl_properties')->num_rows();
        $data['properties_uncategorized'] = $this->db->where("view_in", "all")->get('tbl_properties')->num_rows();
        $data['properties_unpublished_count'] = $this->db->where("view_in", "unpublish")->get('tbl_properties')->num_rows();
        $data['properties_draft_count'] = $this->db->where("view_in", "draft")->get('tbl_properties')->num_rows();
        $data['properties_archive_count'] = $this->db->where("view_in", "archive")->get('tbl_properties')->num_rows();
        $data['properties_live_count'] = $this->db->where("view_in", "live")->get('tbl_properties')->num_rows();
        $data['properties_in_review'] = $this->db->where("view_in", "in_review")->get('tbl_properties')->num_rows();
        $data['subview'] = $this->load->view('admin/properties/all_properties', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function PropertiesList() {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_properties';
            $main_column = array('prop_reference_number', 'prop_title', 'prop_purpose','property_type_id', 'prop_location', 'permission');
            $action_array = array('prop_id');
            $result = array_merge($main_column, $action_array);
            $this->datatables->column_order = $result;
            $this->datatables->column_search = $result;
            $this->datatables->join_where = $this->filters();
            $this->datatables->order = array('prop_id' => 'desc');
            $where = array();
            $fetch_data = $this->datatables->get_properties();
            $data = array();
            $edited = can_action('156', 'edited');
            $deleted = can_action('156', 'deleted');
            foreach ($fetch_data as $_key => $v_property) {
                $action = null;
                if (!empty($v_property)) {
                    $can_edit = $this->items_model->can_action('tbl_properties', 'edit', array('prop_id' => $v_property->prop_id));
                    $can_delete = $this->items_model->can_action('tbl_properties', 'delete', array('prop_id' => $v_property->prop_id));
                    $sub_array = array();
                    $sub_array[] = '<a class="text-info" href="' . base_url() . 'admin/properties/properties_details/' . $v_property->prop_id . '">' . (($v_property->prop_reference_number != NULL && $v_property->prop_reference_number  != "") ? $v_property->prop_reference_number . ". " : "NO REFERENCE") . '</a>';
                    $sub_array[] = ((strtoupper($v_property->prop_purpose) == "SALE") ? "<span class='label label-success'>" . $v_property->prop_purpose . "</span>" : ((strtoupper($v_property->prop_purpose) == "RENT") ? "<span class='label label-warning'>" . $v_property->prop_purpose . "</span>" : "<span class='label label-danger'>" . $v_property->prop_purpose . "</span>"));
                    $type = $this->db->where("property_type_id", $v_property->property_type_id)->get("tbl_properties_types")->row();
                    $sub_array[] = (($type != null) ? $type->property_type : "UNKNOWN");
                    $sub_array[] = $v_property->prop_bedrooms;
                    $sub_array[] =  ((!empty($v_property->prop_location)) ? $v_property->prop_location : "") . ((!empty($v_property->prop_state)) ? ", " . $v_property->prop_state : "")
                    . ((!empty($v_property->prop_city)) ? ", " . $v_property->prop_city : "") . ((!empty($v_property->prop_country)) ? ", " . $v_property->prop_country : "");
                    $sub_array[] = $v_property->prop_size_sqft . " (SQFT)";
                    $sub_array[] = $v_property->prop_bedrooms . " " . config_item('default_currency');
                    $assigned = null;
                    if ($v_property->prop_permission != 'all') {
                        $get_permission = json_decode($v_property->prop_permission);
                        if (!empty($get_permission)) :
                            foreach ($get_permission as $permission => $v_permission) :
                                $user_info = $this->db->where(array('user_id' => $permission))->get('tbl_users')->row();
                                if (!empty($user_info)) {
                                    if ($user_info->role_id == 1) {
                                        $label = 'circle-danger';
                                    } else {
                                        $label = 'circle-success';
                                    }
                                    $assigned .= '<a href="#" data-toggle="tooltip" data-placement="top" title="' . fullname($permission) . '"><img src="' . base_url() . staffImage($permission) . '" class="img-circle img-xs" alt="">
                                                  <span style="margin: 0px 0 8px -10px;" class="circle ' . $label . '  circle-lg"></span> </a>';
                                }
                            endforeach;
                        endif;
                    } else {
                        $assigned .= '<strong>' . lang("everyone") . '</strong><i title="' . lang('permission_for_all') . '" class="fa fa-question-circle" data-toggle="tooltip" data-placement="top"></i>';
                    };
                    if (!empty($can_edit) && !empty($edited)) {
                        $assigned .= '<span data-placement="top" data-toggle="tooltip" title="' . lang('add_more') . '"><a data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/properties/update_users/' . $v_property->prop_id . '" class="text-default ml"><i class="fa fa-plus"></i></a></span>';
                    };
                    $sub_array[] = $assigned;
                    //$action .= '<a href="javascript:;" data-property-id="' . $v_property->prop_id . '" data-property="' . $_key . '" class="btn btn-info btn-xs property_view_btn property_view_' . $v_property->prop_id . '" onclick="propertyFastView(' . $v_property->prop_id . ', ' . $_key . ');" data-toggle="tooltip" data-placement="top" title="View"><span class="fa fa-eye"></span></a>' . ' ';
                    if (!empty($can_edit) && !empty($edited)) {
                        $action .= btn_edit('admin/properties/index/' . $v_property->prop_id) . ' ';
                    }
                    if (!empty($can_delete) && !empty($deleted)) {
                        $action .= ajax_anchor(base_url("admin/properties/delete_property/$v_property->prop_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                    }
                    $sub_array[] = $action;
                    $data[] = $sub_array;
                }

            }
            render_table(array_reverse($data), $where);
        } else {
            redirect('admin/dashboard');
        }
    }

    protected function filters() {
        if ($this->input->post("tableFilters") != NULL && $this->input->post("tableFilters") != "") {
            $filterArray = array("filter_purpose" => "prop_purpose", "filter_assigned_to" => "prop_permission", "filter_types" => "property_type_id",
                "filter_developer" => "prop_developer", "filter_country" => "prop_country", "flter_view_by" => "view_in",
                "filter_location" => "prop_location", "filter_community" => "prop_community", "filter_sub_community" => "prop_sub_community");
            $filterJoinArray = array("filter_contain_lead");
            $filterToString = array("filter_central_ac", "filter_heater", "filter_study_room", "filter_balacony", "filter_private_pool", "filter_storage");
            $filterStartEnds = array("flter_price_start", "flter_price_end", "flter_beds_start", "flter_beds_end", "flter_area_start", "flter_area_end");
            $returnFilters = "";
            $filterCount = 0;
            $acString = "FALSE";
            $heaterString = "FALSE";
            $studyString = "FALSE";
            $balconyString = "FALSE";
            $poolString = "FALSE";
            $storageString = "FALSE";
            $joinFilters = NULL;
            $otherFilter = false;
            $allFilters = $this->input->post("tableFilters");
            $filters = explode("&", $allFilters);
            for ($i = 0; $i < count($filters); $i++) {
                $filter = explode("=", $filters[$i]);
                if ($filter[1] != NULL && $filter[1] != "") {
                    if (in_array($filter[0], $filterJoinArray)) {
                        $joinFilters = array("properties_client", "`properties_client`.`property_id` = `properties`.`prop_id`");
                    } elseif (in_array($filter[0], $filterToString)) {
                        if (str_replace("filter_", "", $filter[0]) == "ac") {
                            $otherFilter = true;
                            $acString = $filter_input[1];
                        } else if (str_replace("filter_", "", $filter[0]) == "heater") {
                            $otherFilter = true;
                            $heaterString = $filter_input[1];
                        } else if (str_replace("filter_", "", $filter[0]) == "study_room") {
                            $otherFilter = true;
                            $studyString = $filter_input[1];
                        } else if (str_replace("filter_", "", $filter[0]) == "balacony") {
                            $otherFilter = true;
                            $balconyString = $filter_input[1];
                        } else if (str_replace("filter_", "", $filter[0]) == "private_pool") {
                            $otherFilter = true;
                            $poolString = $filter_input[1];
                        } else if (str_replace("filter_", "", $filter[0]) == "storage") {
                            $otherFilter = true;
                            $storageString = $filter_input[1];
                        }
                    } elseif (in_array($filter[0], $filterStartEnds)) {
                        if ($filter[0] == "flter_price_start") {
                            if ($filter[1] > 0){
                              $returnFilters .= (($filterCount > 0) ? " AND " : "") . " `tbl_properties`.`prop_price` >= " . $filter[1];
                              $filterCount++;
                            }
                        } else if ($filter[0] == "flter_price_end") {
                                $returnFilters .= (($filterCount > 0) ? " AND " : "") . " `tbl_properties`.`prop_price` <= " . $filter[1];
                                $filterCount++;
                        } else if ($filter[0] == "flter_beds_start") {
                            if ($filter[1] > 0){
                                $returnFilters .= (($filterCount > 0) ? " AND " : "") . " `tbl_properties`.`prop_bedrooms` >= " . $filter[1];
                                $filterCount++;
                              }
                        } else if ($filter[0] == "flter_beds_end") {
                                $returnFilters .= (($filterCount > 0) ? " AND " : "") . " `tbl_properties`.`prop_bedrooms` <= " . $filter[1];
                                $filterCount++;
                        } else if ($filter[0] == "flter_area_start") {
                            if ($filter[1] > 0){
                                $returnFilters .= (($filterCount > 0) ? " AND " : "") . " `tbl_properties`.`prop_size_sqft` >= " . $filter[1];
                                $filterCount++;
                              }
                        } else if ($filter[0] == "flter_area_end") {
                                $returnFilters .= (($filterCount > 0) ? " AND " : "") . " `tbl_properties`.`prop_size_sqft` <= " . $filter[1];
                                $filterCount++;
                        }
                    } else {
                      if($filter[0] == "filter_assigned_to"){
                        $returnFilters .= (($filterCount > 0) ? " AND " : "") . "`tbl_properties`." . $filterArray[$filter[0]] . "` LIKE '%" . '{"' . $filter[1] . '":' . "%'";
                      }else {
                        $returnFilters .= (($filterCount > 0) ? " AND " : "") . "`tbl_properties`.`" . $filterArray[$filter[0]] . "` = '" . str_replace("+", " ", $filter[1]) . "'";
                        $filterCount++;
                      }
                    }
                }
            }

            if (isset($otherFilter) && $otherFilter != NULL) {
                $returnFilters .= (($filterCount > 0) ? " AND " : "") . " `prop_others` = '" . $acString . "-" . $heaterString . "-" . $studyString . "-" . $balconyString . "-" . $poolString . "-" . $storageString . "'";
            }

            return array($returnFilters, $joinFilters);
        } else {
            return array(NULL, NULL);
        }
    }

    public function saved_property($id = NULL) {
        $created = can_action('156', 'created');
        $edited = can_action('156', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $this->items_model->_table_name = 'tbl_properties';
            $this->items_model->_primary_key = 'prop_id';
            $save_as = $this->input->post("save_as");
            $data = $this->items_model->array_from_post(
              array('property_type_id','prop_bedrooms', 'prop_bathrooms', 'prop_lsm', 'prop_purpose', 'prop_price', 'prop_parking', 'prop_year_built', 'prop_transaction',
                    'prop_location', 'prop_developer', 'prop_permit', 'prop_state', 'prop_plot_area', 'prop_owner_id', 'prop_community', 'prop_size_sqft',
                    'prop_rented', 'prop_sub_community', 'property_source_id', 'prop_title', 'prop_country', 'prop_custom_reference', 'prop_description'));
            if($this->input->post("prop_purpose") == "rent"){
              $data['rent_frequency'] = $this->input->post("rent_frequency");
            }
            $data['prop_unit_info'] = json_encode(array($this->input->post("property_unit"), $this->input->post("property_plot"), $this->input->post("property_street")));
            $data['prop_deposit_info'] = json_encode(array($this->input->post("property_deposit"), $this->input->post("property_deposit_money")));
            if(!empty($save_as) && $save_as == lang('save_as_draft')){
              $data['view_in'] = "draft";
            }else{
              $data['view_in'] = "all";
            }
            $where = array("`prop_country` = '" . $data['prop_country'] . "' AND `prop_state` = '" . $data['prop_state'] . "' AND `prop_location` = '" . $data['prop_location'] . "' AND `prop_unit_info` = '" . $data['prop_unit_info'] . "' AND `prop_purpose` = '" . $data['prop_purpose'] . "'");
            if (!empty($id)) {
                $prop_id = array('prop_id !=' => $id);
            } else {
                $prop_id = null;
            }
            // $check_property = $this->items_model->check_update('tbl_properties', $where, $prop_id);
            if (!empty($check_property)) {
                $type = 'error';
                $msg = "<strong style='color:#000'>" . $data['prop_title'] . ' (' . $data['prop_property_number'] . ')</strong>  ' . lang('already_exist');
            } else {
                $permission = $this->input->post('permission', true);
                if (!empty($permission)) {
                    if ($permission == 'everyone') {
                        $assigned = 'all';
                    } else {
                        $assigned_to = $this->items_model->array_from_post(array('assigned_to'));
                        if (!empty($assigned_to['assigned_to'])) {
                            foreach ($assigned_to['assigned_to'] as $assign_user) {
                                $assigned[$assign_user] = $this->input->post('action_' . $assign_user, true);
                            }
                        }
                    }
                    if (!empty($assigned)) {
                        if ($assigned != 'all') {
                            $assigned = json_encode($assigned);
                        }
                    } else {
                        $assigned = 'all';
                    }
                    $data['prop_permission'] = $assigned;
                } else {
                    set_message('error', lang('assigned_to') . ' Field is required');
                    if (empty($_SERVER['HTTP_REFERER'])) {
                        redirect('admin/properties');
                    } else {
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }
                $return_id = $this->items_model->save($data, $id);
                if (!empty($id)) {
                    $id = $id;
                    $action = 'activity_update_property';
                    $description = 'not_update_property';
                    $msg = lang('update_property');
                } else {
                    $id = $return_id;
                    $action = 'activity_save_property';
                    $description = 'not_save_property';
                    $msg = lang('save_property');
                }
                save_custom_field(5, $id);
                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'properties',
                    'module_field_id' => $id,
                    'activity' => $action,
                    'icon' => 'fa-rocket',
                    'link' => 'admin/properties/property_details/' . $id,
                    'value1' => $data['prop_title']
                );
                $this->items_model->_table_name = 'tbl_activities';
                $this->items_model->_primary_key = 'activities_id';
                $this->items_model->save($activity);
                $type = "success";

                $proeprty_info = $this->items_model->check_by(array('prop_id' => $id), 'tbl_properties');
                $notifiedUsers = array();
                if (!empty($proeprty_info->permission) && $proeprty_info->permission != 'all') {
                    $permissionUsers = json_decode($proeprty_info->permission);
                    foreach ($permissionUsers as $user => $v_permission) {
                        array_push($notifiedUsers, $user);
                    }
                } else {
                    $notifiedUsers = $this->items_model->allowad_user_id('156');
                }
                if (!empty($notifiedUsers)) {
                    foreach ($notifiedUsers as $users) {
                        if ($users != $this->session->userdata('user_id')) {
                            add_notification(array(
                                'to_user_id' => $users,
                                'from_user_id' => true,
                                'description' => $description,
                                'link' => 'admin/properties/property_details/' . $proeprty_info->prop_id,
                                'value' => lang('proeprties') . ' ' . $proeprty_info->prop_title,
                            ));
                        }
                    }
                    show_notification($notifiedUsers);
                }
            }
            $message = $msg;
            set_message($type, $message);
        }
        redirect('admin/properties');
    }

    public function property_fast_view(){
      if($this->input->post()){
        $prop_id = $this->input->post("prop_id");
        $leads_details = $this->items_model->check_by(array('prop_id' => $prop_id), 'tbl_properties');
        $leadResponse = "";
        if($leads_details != null){
          $leadResponse .= "<tr id='lead_fast_view' class='lead_fast_view'><td colspan='9' style='background-color: #eeeeee;'>";
          $leadResponse .= '<div class="panel-body row form-horizontal task_details">
              <div class="form-group col-sm-12">
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>'. lang('nationality') .': </strong>
                      </label>
                      <p class="form-control-static">';
                      $lead_nationality = $this->db->where('id', $leads_details->nationality)->get('tbl_countries')->row();
                      if (isset($lead_nationality) && $lead_nationality != NULL) {
                          $leadResponse .=  '<img width="30px" class="img-rounded" src="' . base_url("assets/img/flags/" . $lead_nationality->flag) . '">&nbsp;&nbsp;<i style="margin-left: 10px;">' . $lead_nationality->nationality . '</i>';
                      }else{
                          $leadResponse .=  "UNKNOWN";
                      }
                          $leadResponse .= '</p>
                  </div>
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>'. lang('lead_passport') .': </strong>
                      </label>';
                      $leadResponse .= '<div>
                          <p class="form-control-static">'. $leads_details->passport_number  . " (" . (($leads_details->passport_expire != NULL && $leads_details->passport_expire != "")? date("m-Y", strtotime($leads_details->passport_expire)) : "UNKNOWN") . ")".'</p>
                      </div>';
                  $leadResponse .= '</div>
              </div>
              <div class="form-group col-sm-12">
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>'. lang('email_2') .': </strong></label>
                      <p class="form-control-static">';
                              $leadResponse .=  $leads_details->email2;
                      $leadResponse .= '</p>
                  </div>
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>' . lang('lead_date_of_birth') .': </strong></label>
                      <p class="form-control-static">';
                              $leadResponse .=  date("d-m-Y", strtotime($leads_details->passport_number));
                          $leadResponse .= '</p>
                  </div>
              </div>
              <div class="form-group col-sm-12">
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>'. lang('email_3') .': </strong> </label>
                      <p class="form-control-static">';
                              $leadResponse .=  $leads_details->email3;
                      $leadResponse .= '</p>
                  </div>
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>'. lang('skype_id') .': </strong></label>
                      <a href="skype:'."'";
                      if (!empty($leads_details->skype)) {
                          $leadResponse .=  $leads_details->skype;
                      }
                      $leadResponse .= "'".'">
                          <p class="form-control-static">';
                              if (!empty($leads_details->skype)) {
                                  $leadResponse .=  $leads_details->skype;
                              }
                      $leadResponse .= '</p></a>';
                  $leadResponse .= '</div>
              </div>
              <div class="form-group col-sm-12">
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>'. lang('address') .': </strong></label>
                      <p class="form-control-static">';
                          if (!empty($leads_details->address)) {
                              $leadResponse .=  $leads_details->address;
                          }
                      $leadResponse .= '</p>
                  </div>
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>'. lang('fax') .': </strong></label>
                      <p class="form-control-static">';
                              $leadResponse .=  $leads_details->fax;
                      $leadResponse .= '</p>
                  </div>
              </div>
              <div class="form-group col-sm-12">
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>'. lang('city') .': </strong></label>
                      <p class="form-control-static">';
                          if (!empty($leads_details->city)) {
                              $leadResponse .=  $leads_details->city;
                          }
                      $leadResponse .= '</p>
                  </div>
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>'. lang('landline') .': </strong>
                      </label>
                      <p class="form-control-static">';
                              $leadResponse .=  $leads_details->phone;
                          $leadResponse .= '</p>
                  </div>
              </div>
              <div class="form-group col-sm-12">
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>'. lang('state') .': </strong></label>
                      <p class="form-control-static">';
                          if (!empty($leads_details->state)) {
                              $leadResponse .=  $leads_details->state;
                          }
                      $leadResponse .= '</p>
                  </div>
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>'. lang('phone_2') .': </strong></label>
                      <div class="col-sm-7 "><p class="form-control-static"><strong>';
                      $leads_details->phone2 = ltrim($leads_details->phone2, '0');
                      $phone_information =  $this->db->get('tbl_countries')->result();
                      foreach ($phone_information as $single_phone) {
                          if ((strlen($single_phone->calling_code) + 1) == strlen($single_phone->phone_code)) {
                              if (substr($leads_details->phone2, 0, strlen($single_phone->calling_code)) === $single_phone->calling_code || substr($leads_details->phone2, 0, strlen($single_phone->phone_code)) === $single_phone->phone_code) {
                                  $d = new DateTime("now", new DateTimeZone($single_phone->time_zone));
                                  $leadResponse .= '<img class="img-rounded" src="' . base_url("assets/img/flags/" . $single_phone->flag) . '" style="margin-right: 10px;" height="20px"><i>' . $leads_details->phone2 . '</i>';
                                  $leadResponse .= '<small> (<i>' . $single_phone->long_name . '</i> - <i>Time: ' . $d->format("h:i A") . '</i>)</small>';
                                  break;
                              }
                          }
                      }
                      $leadResponse .= '</strong>
                      </p></div>
                  </div>
              </div>
              <div class="form-group col-sm-12">
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>'. lang('country') .': </strong></label>
                      <p class="form-control-static">';
                          if (!empty($leads_details->country)) {
                              $leadResponse .=  $leads_details->country;
                          }
                      $leadResponse .= '</p>
                  </div>
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>'. lang('phone_3') .': </strong></label>
                      <div class="col-sm-7 "><p class="form-control-static"><strong>';
                          $leads_details->phone3 = ltrim($leads_details->phone3, '0');
                          $phone_information =  $this->db->get('tbl_countries')->result();
                          foreach ($phone_information as $single_phone) {
                              if ((strlen($single_phone->calling_code) + 1) == strlen($single_phone->phone_code)) {
                                  if (substr($leads_details->phone3, 0, strlen($single_phone->calling_code)) === $single_phone->calling_code || substr($leads_details->phone3, 0, strlen($single_phone->phone_code)) === $single_phone->phone_code) {
                                      $d = new DateTime("now", new DateTimeZone($single_phone->time_zone));
                                      $leadResponse .= '<img class="img-rounded" src="' . base_url("assets/img/flags/" . $single_phone->flag) . '" style="margin-right: 10px;" height="20px"><i>' . $leads_details->phone3 . '</i>';
                                      $leadResponse .= '<small> (<i>' . $single_phone->long_name . '</i> - <i>Time: ' . $d->format("h:i A") . '</i>)</small>';
                                      break;
                                  }
                              }
                          }
                          $leadResponse .= '</strong>
                      </p></div>
                  </div>
              </div>
              <div class="form-group col-sm-12">
                  <div class="col-sm-6"><label
                          class="control-label col-sm-5"><strong>'. lang('twitter_profile_link') .': </strong></label>
                      <a target="_blank" href="//';
                      if (!empty($leads_details->twitter)) {
                          $leadResponse .=  $leads_details->twitter;
                      }
                      $leadResponse .= '">
                          <p class="form-control-static">';
                              if (!empty($leads_details->twitter)) {
                                  $leadResponse .=  $leads_details->twitter;
                              }
                              $leadResponse .= '</p></a>
                  </div>
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>'. lang('phone_4') .': </strong></label>
                      <div class="col-sm-7"><p class="form-control-static"><strong>';
                      $leads_details->phone4 = ltrim($leads_details->phone4, '0');
                      $phone_information =  $this->db->get('tbl_countries')->result();
                      foreach ($phone_information as $single_phone) {
                          if ((strlen($single_phone->calling_code) + 1) == strlen($single_phone->phone_code)) {
                              if (substr($leads_details->phone4, 0, strlen($single_phone->calling_code)) === $single_phone->calling_code || substr($leads_details->phone4, 0, strlen($single_phone->phone_code)) === $single_phone->phone_code) {
                                  $d = new DateTime("now", new DateTimeZone($single_phone->time_zone));
                                  $leadResponse .= '<img class="img-rounded" src="' . base_url("assets/img/flags/" . $single_phone->flag) . '" style="margin-right: 10px;" height="20px"><i>' . $leads_details->phone4 . '</i>';
                                  $leadResponse .= '<small> (<i>' . $single_phone->long_name . '</i> - <i>Time: ' . $d->format("h:i A") . '</i>)</small>';
                                  break;
                              }
                          }
                      }
                      $leadResponse .= '</strong>
                      </p></div>
                  </div>
              </div>';
              $leadResponse .= '<div class="form-group col-sm-12">
                      <div class="col-sm-6">
                          <label
                              class="control-label col-sm-5"><strong>'. lang('facebook_profile_link') .': </strong></label>
                          <a target="_blank" href="//';
                          if (!empty($leads_details->facebook)) {
                              $leadResponse .=  $leads_details->facebook;
                          }
                          $leadResponse .= '">
                              <p class="form-control-static">';
                                  if (!empty($leads_details->facebook)) {
                                      $leadResponse .=  $leads_details->facebook;
                                  }
                                  $leadResponse .= '</p></a>
                      </div>
                      <div class="col-sm-6">
                          <label class="control-label col-sm-5"><strong>'. lang('notes') .': </strong></label>
                          <div class="col-sm-7"><p class="form-control-static">'. $leads_details->notes .'</p></div>
                      </div>';
              $leadResponse .= '</div>
              <div class="col-sm-12 text text-right">
              <blockquote class="col-sm-8"></blockquote>
                  <blockquote class="col-sm-2" style="font-size: 14px;margin-bottom: 0px;"><strong>'. lang('created_time') .':  </strong>';
                      if (!empty($leads_details->notes)) {
                          $leadResponse .=  date("d-m-Y", strtotime($leads_details->created_time));
                      }
                      $leadResponse .= '</blockquote>
                  <blockquote class="col-sm-2" style="font-size: 14px;margin-bottom: 0px;"><strong>'. lang('modified_time') .':  </strong>';
                      if (!empty($leads_details->notes)) {
                          $leadResponse .=  (($leads_details->modified_time != NULL && $leads_details->modified_time != "") ? date("d-m-Y H:i:s", strtotime($leads_details->modified_time)) : "NEVER");
                      }
                      $leadResponse .= '</blockquote>
              </div>
          </div>';
          $leadResponse .= "</td></tr>";
        }
        echo $leadResponse;
      }
    }

    public function properties_details($id, $active = NULL, $op_id = NULL) {
        $data['title'] = lang('property_details');
        $data['property_details'] = $this->items_model->check_by(array('prop_id' => $id), 'tbl_properties');
        $this->items_model->_table_name = "tbl_task_attachment";
        $this->items_model->_order_by = "prop_id";
        $data['files_info'] = $this->items_model->get_by(array('prop_id' => $id), FALSE);
        foreach ($data['files_info'] as $key => $v_files) {
            $this->items_model->_table_name = "tbl_task_uploaded_files";
            $this->items_model->_order_by = "task_attachment_id";
            $data['project_files_info'][$key] = $this->items_model->get_by(array('task_attachment_id' => $v_files->task_attachment_id), FALSE);
        }
        $data['dropzone'] = true;
        if ($active == 2) {
            $data['active'] = 2;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
        } elseif ($active == 3) {
            $data['active'] = 3;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
        } elseif ($active == 4) {
            $data['active'] = 4;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
        } elseif ($active == 5) {
            $data['active'] = 5;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
        } else {
            $data['active'] = 1;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
        }
        $data['subview'] = $this->load->view('admin/properties/property_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function update_users($id) {
        $can_edit = $this->items_model->can_action('tbl_properties', 'edit', array('prop_id' => $id));
        if (!empty($can_edit)) {
            $data['assign_user'] = $this->items_model->allowad_user('156');
            $data['property_info'] = $this->items_model->check_by(array('prop_id' => $id), 'tbl_properties');
            $data['modal_subview'] = $this->load->view('admin/properties/_modal_users', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/properties');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function update_member($id) {
        $can_edit = $this->items_model->can_action('tbl_properties', 'edit', array('prop_id' => $id));
        if (!empty($can_edit)) {
            $property_info = $this->items_model->check_by(array('prop_id' => $id), 'tbl_properties');
            $permission = $this->input->post('permission', true);
            if (!empty($permission)) {
                if ($permission == 'everyone') {
                    $assigned = 'all';
                } else {
                    $assigned_to = $this->items_model->array_from_post(array('assigned_to'));
                    if (!empty($assigned_to['assigned_to'])) {
                        foreach ($assigned_to['assigned_to'] as $assign_user) {
                            $assigned[$assign_user] = $this->input->post('action_' . $assign_user, true);
                        }
                    }
                }
                if (!empty($assigned)) {
                    if ($assigned != 'all') {
                        $assigned = json_encode($assigned);
                    }
                } else {
                    $assigned = 'all';
                }
                $data['permission'] = $assigned;
            } else {
                set_message('error', lang('assigned_to') . ' Field is required');
                if (empty($_SERVER['HTTP_REFERER'])) {
                    redirect('admin/properties');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }

            $this->items_model->_table_name = "tbl_properties";
            $this->items_model->_primary_key = "prop_id";
            $this->items_model->save($data, $id);
            $msg = lang('update_property');
            $activity = 'activity_update_property';
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'properties',
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-rocket',
                'link' => 'admin/properties/property_details/' . $id,
                'value1' => $property_info->title,
            );
            $this->items_model->_table_name = "tbl_activities";
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);
            $notifiedUsers = array();
            if (!empty($property_info->permission) && $property_info->permission != 'all') {
                $permissionUsers = json_decode($property_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->items_model->allowad_user_id('156');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'assign_to_you_the_lproperty',
                            'link' => 'admin/properties/property_details/' . $property_info->prop_id,
                            'value' => lang('property') . ' ' . $property_info->title,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
            $type = "success";
            $message = $msg;
            set_message($type, $message);
        } else {
            set_message('error', lang('there_in_no_value'));
        }
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/properties');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function save_comments() {
        $data['prop_id'] = $this->input->post('prop_id', TRUE);
        $data['comment'] = $this->input->post('comment', TRUE);
        $files = $this->input->post("files", true);
        $target_path = getcwd() . "/uploads/";
        if (!empty($files) && is_array($files)) {
            foreach ($files as $key => $file) {
                if (!empty($file)) {
                    $file_name = $this->input->post('file_name_' . $file, true);
                    $new_file_name = move_temp_file($file_name, $target_path);
                    $file_ext = explode(".", $new_file_name);
                    $is_image = check_image_extension($new_file_name);
                    $size = $this->input->post('file_size_' . $file, true) / 1000;
                    if ($new_file_name) {
                        $up_data[] = array(
                            "fileName" => $new_file_name,
                            "path" => "uploads/" . $new_file_name,
                            "fullPath" => getcwd() . "/uploads/" . $new_file_name,
                            "ext" => '.' . end($file_ext),
                            "size" => round($size, 2),
                            "is_image" => $is_image,
                        );
                        $success = true;
                    } else {
                        $success = false;
                    }
                }
            }
        }
        if ($_FILES) {
            $files = $_FILES['manualFiles'];
            if ($files && count($files) > 0) {
                foreach ($files["tmp_name"] as $key => $file) {
                    $temp_file = $file;
                    $file_name = $files["name"][$key];
                    $file_size = $files["size"][$key];
                    $new_file_name = move_temp_file($file_name, $target_path, "", $temp_file);
                    if ($new_file_name) {
                        $file_ext = explode(".", $new_file_name);
                        $is_image = check_image_extension($new_file_name);
                        $up_data[] = array(
                            "fileName" => $new_file_name,
                            "path" => "uploads/" . $new_file_name,
                            "fullPath" => getcwd() . "/uploads/" . $new_file_name,
                            "ext" => '.' . end($file_ext),
                            "size" => round($file_size, 2),
                            "is_image" => $is_image,
                        );
                    }
                }
            }
        }
        if (!empty($up_data)) {
            $data['comments_attachment'] = json_encode($up_data);
        }
        $data['user_id'] = $this->session->userdata('user_id');
        $this->items_model->_table_name = "tbl_task_comment";
        $this->items_model->_primary_key = "task_comment_id";
        $comment_id = $this->items_model->save($data);

        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'properties',
            'module_field_id' => $data['prop_id'],
            'activity' => 'activity_new_property_comment',
            'icon' => 'fa-rocket',
            'link' => 'admin/properties/property_details/' . $data['prop_id'] . '/4',
            'value1' => $data['comment'],
        );

        $this->items_model->_table_name = "tbl_activities";
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);
        if (!empty($comment_id)) {
            $property_info = $this->items_model->check_by(array('prop_id' => $data['prop_id']), 'tbl_properties');
            $notifiedUsers = array();
            if (!empty($property_info->permission) && $property_info->permission != 'all') {
                $permissionUsers = json_decode($property_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->items_model->allowad_user_id('156');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_new_comment',
                            'link' => 'admin/properties/property_details/' . $property_info->prop_id . '/4',
                            'value' => lang('property') . ' ' . $property_info->prop_title,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
            $response_data = "";
            $view_data['comment_details'] = $this->db->where(array('task_comment_id' => $comment_id))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
            $response_data = $this->load->view("admin/properties/comments_list", $view_data, true);
            echo json_encode(array("status" => 'success', "data" => $response_data, 'message' => lang('property_comment_save')));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            exit();
        }
    }

    public function save_comments_reply($task_comment_id) {
        $data['prop_id'] = $this->input->post('prop_id', TRUE);
        $data['comment'] = $this->input->post('reply_comments', TRUE);
        $data['user_id'] = $this->session->userdata('user_id');
        $data['comments_reply_id'] = $task_comment_id;
        $this->items_model->_table_name = "tbl_task_comment";
        $this->items_model->_primary_key = "task_comment_id";
        $comment_id = $this->items_model->save($data);
        if (!empty($comment_id)) {
            $comments_info = $this->items_model->check_by(array('task_comment_id' => $task_comment_id), 'tbl_task_comment');
            $user = $this->items_model->check_by(array('user_id' => $comments_info->user_id), 'tbl_users');
            if ($user->role_id == 2) {
                $url = 'client/';
            } else {
                $url = 'admin/';
            }
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'proeprties',
                'module_field_id' => $data['prop_id'],
                'activity' => 'activity_new_comment_reply',
                'icon' => 'fa-rocket',
                'link' => $url . 'properties/property_details/' . $data['prop_id'] . '/4',
                'value1' => $this->db->where('task_comment_id', $task_comment_id)->get('tbl_task_comment')->row()->comment,
                'value2' => $data['comment'],
            );
            $this->items_model->_table_name = "tbl_activities";
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);
            $property_info = $this->items_model->check_by(array('prop_id' => $data['prop_id']), 'tbl_properties');
            $notifiedUsers = array($comments_info->user_id);
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_new_comment',
                            'link' => $url . 'properties/property_details/' . $property_info->prop_id . '/4',
                            'value' => lang('property') . ' ' . $property_info->prop_title,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
            $response_data = "";
            $view_data['comment_reply_details'] = $this->db->where(array('task_comment_id' => $comment_id))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
            $response_data = $this->load->view("admin/properties/comments_reply", $view_data, true);
            echo json_encode(array("status" => 'success', "data" => $response_data, 'message' => lang('property_comment_save')));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            exit();
        }
    }

    public function delete_comments($task_comment_id = null) {
        $comments_info = $this->items_model->check_by(array('task_comment_id' => $task_comment_id), 'tbl_task_comment');
        if (!empty($comments_info->comments_attachment)) {
            $attachment = json_decode($comments_info->comments_attachment);
            foreach ($attachment as $v_file) {
                remove_files($v_file->fileName);
            }
        }
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'properties',
            'module_field_id' => $comments_info->prop_id,
            'activity' => 'activity_comment_deleted',
            'icon' => 'fa-rocket',
            'link' => 'admin/properties/property_details/' . $comments_info->prop_id . '/4',
            'value1' => $comments_info->comment,
        );
        $this->items_model->_table_name = "tbl_activities";
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);
        $this->items_model->_table_name = "tbl_task_comment";
        $this->items_model->delete_multiple(array('comments_reply_id' => $task_comment_id));
        $this->items_model->_table_name = "tbl_task_comment";
        $this->items_model->_primary_key = "task_comment_id";
        $this->items_model->delete($task_comment_id);
        echo json_encode(array("status" => 'success', 'message' => lang('task_comment_deleted')));
        exit();
    }

    public function save_attachment($task_attachment_id = NULL) {
        $data = $this->items_model->array_from_post(array('title', 'description', 'prop_id'));
        $data['user_id'] = $this->session->userdata('user_id');
        $this->items_model->_table_name = "tbl_task_attachment";
        $this->items_model->_primary_key = "task_attachment_id";
        if (!empty($task_attachment_id)) {
            $id = $task_attachment_id;
            $this->items_model->save($data, $id);
            $msg = lang('property_file_updated');
        } else {
            $id = $this->items_model->save($data);
            $msg = lang('property_file_added');
        }
        $files = $this->input->post("files", true);
        $target_path = getcwd() . "/uploads/";
        if (!empty($files) && is_array($files)) {
            foreach ($files as $key => $file) {
                if (!empty($file)) {
                    $file_name = $this->input->post('file_name_' . $file, true);
                    $new_file_name = move_temp_file($file_name, $target_path);
                    $file_ext = explode(".", $new_file_name);
                    $is_image = check_image_extension($new_file_name);

                    if ($new_file_name) {
                        $up_data = array(
                            "files" => "uploads/" . $new_file_name,
                            "uploaded_path" => getcwd() . "/uploads/" . $new_file_name,
                            "file_name" => $new_file_name,
                            "size" => $this->input->post('file_size_' . $file, true),
                            "ext" => end($file_ext),
                            "is_image" => $is_image,
                            "image_width" => 0,
                            "image_height" => 0,
                            "task_attachment_id" => $id
                        );
                        $this->items_model->_table_name = "tbl_task_uploaded_files";
                        $this->items_model->_primary_key = "uploaded_files_id";
                        $uploaded_files_id = $this->items_model->save($up_data);
                        $comment = $this->input->post('comment_' . $file, true);
                        if (!empty($comment)) {
                            $u_cdata = array(
                                "comment" => $comment,
                                "prop_id" => $data['prop_id'],
                                "user_id" => $this->session->userdata('user_id'),
                                "uploaded_files_id" => $uploaded_files_id,
                            );
                            $this->items_model->_table_name = "tbl_task_comment";
                            $this->items_model->_primary_key = "task_comment_id";
                            $this->items_model->save($u_cdata);
                        }
                        $success = true;
                    } else {
                        $success = false;
                    }
                }
            }
        }
        if ($_FILES) {
            $files = $_FILES['manualFiles'];
            if ($files && count($files) > 0) {
                $comment = $this->input->post('comment', true);
                foreach ($files["tmp_name"] as $key => $file) {
                    $temp_file = $file;
                    $file_name = $files["name"][$key];
                    $file_size = $files["size"][$key];
                    $new_file_name = move_temp_file($file_name, $target_path, "", $temp_file);
                    if ($new_file_name) {
                        $file_ext = explode(".", $new_file_name);
                        $is_image = check_image_extension($new_file_name);
                        $up_data = array(
                            "files" => "uploads/" . $new_file_name,
                            "uploaded_path" => getcwd() . "/uploads/" . $new_file_name,
                            "file_name" => $new_file_name,
                            "size" => $file_size,
                            "ext" => end($file_ext),
                            "is_image" => $is_image,
                            "image_width" => 0,
                            "image_height" => 0,
                            "task_attachment_id" => $id
                        );
                        $this->items_model->_table_name = "tbl_task_uploaded_files";
                        $this->items_model->_primary_key = "uploaded_files_id";
                        $uploaded_files_id = $this->items_model->save($up_data);
                        if (!empty($comment[$key])) {
                            $u_cdata = array(
                                "comment" => $comment[$key],
                                "prop_id" => $data['prop_id'],
                                "user_id" => $this->session->userdata('user_id'),
                                "uploaded_files_id" => $uploaded_files_id,
                            );
                            $this->items_model->_table_name = "tbl_task_comment";
                            $this->items_model->_primary_key = "task_comment_id";
                            $this->items_model->save($u_cdata);
                        }

                    }
                }
            }
        }
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'properties',
            'module_field_id' => $data['prop_id'],
            'activity' => 'activity_new_property_attachment',
            'icon' => 'fa-rocket',
            'link' => 'admin/properties/property_details/' . $data['prop_id'] . '/5',
            'value1' => $data['title'],
        );
        $this->items_model->_table_name = "tbl_activities";
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);
        $property_info = $this->items_model->check_by(array('prop_id' => $data['prop_id']), 'tbl_properties');
        $notifiedUsers = array();
        if (!empty($property_info->permission) && $property_info->permission != 'all') {
            $permissionUsers = json_decode($property_info->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->items_model->allowad_user_id('156');
        }
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_uploaded_attachment',
                        'link' => 'admin/properties/property_details/' . $property_info->prop_id . '/5',
                        'value' => lang('property') . ' ' . $property_info->title,
                    ));
                }
            }
            show_notification($notifiedUsers);
        }
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/properties/property_details/' . $data['prop_id'] . '/' . '5');
    }

    public function new_attachment($id) {
        $data['dropzone'] = true;
        $data['property_details'] = $this->items_model->check_by(array('prop_id' => $id), 'tbl_properties');
        $data['modal_subview'] = $this->load->view('admin/properties/new_attachment', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function attachment_details($type, $id) {
        $data['type'] = $type;
        $data['attachment_info'] = $this->items_model->check_by(array('task_attachment_id' => $id), 'tbl_task_attachment');
        $data['modal_subview'] = $this->load->view('admin/properties/attachment_details', $data, FALSE);
        $this->load->view('admin/_layout_modal_extra_lg', $data);
    }

    public function save_attachment_comments() {
        $task_attachment_id = $this->input->post('task_attachment_id', true);
        if (!empty($task_attachment_id)) {
            $data['task_attachment_id'] = $task_attachment_id;
        } else {
            $data['uploaded_files_id'] = $this->input->post('uploaded_files_id', true);
        }
        $data['prop_id'] = $this->input->post('prop_id', true);
        $data['comment'] = $this->input->post('description', true);
        $files = $this->input->post("files", true);
        $target_path = getcwd() . "/uploads/";
        if (!empty($files) && is_array($files)) {
            foreach ($files as $key => $file) {
                if (!empty($file)) {
                    $file_name = $this->input->post('file_name_' . $file, true);
                    $new_file_name = move_temp_file($file_name, $target_path);
                    $file_ext = explode(".", $new_file_name);
                    $is_image = check_image_extension($new_file_name);
                    $size = $this->input->post('file_size_' . $file, true) / 1000;
                    if ($new_file_name) {
                        $up_data[] = array(
                            "fileName" => $new_file_name,
                            "path" => "uploads/" . $new_file_name,
                            "fullPath" => getcwd() . "/uploads/" . $new_file_name,
                            "ext" => '.' . end($file_ext),
                            "size" => round($size, 2),
                            "is_image" => $is_image,
                        );
                        $success = true;
                    } else {
                        $success = false;
                    }
                }
            }
        }
        if ($_FILES) {
            $files = $_FILES['manualFiles'];
            if ($files && count($files) > 0) {
                $comment = $this->input->post('comment', true);
                foreach ($files["tmp_name"] as $key => $file) {
                    $temp_file = $file;
                    $file_name = $files["name"][$key];
                    $file_size = $files["size"][$key];
                    $new_file_name = move_temp_file($file_name, $target_path, "", $temp_file);
                    if ($new_file_name) {
                        $file_ext = explode(".", $new_file_name);
                        $is_image = check_image_extension($new_file_name);
                        $up_data[] = array(
                            "fileName" => $new_file_name,
                            "path" => "uploads/" . $new_file_name,
                            "fullPath" => getcwd() . "/uploads/" . $new_file_name,
                            "ext" => '.' . end($file_ext),
                            "size" => round($file_size, 2),
                            "is_image" => $is_image,
                        );
                    }
                }
            }
        }
        if (!empty($up_data)) {
            $data['comments_attachment'] = json_encode($up_data);
        }
        $data['user_id'] = $this->session->userdata('user_id');
        $this->items_model->_table_name = "tbl_task_comment";
        $this->items_model->_primary_key = "task_comment_id";
        $comment_id = $this->items_model->save($data);
        if (!empty($comment_id)) {
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'properties',
                'module_field_id' => $data['prop_id'],
                'activity' => 'activity_new_property_comment',
                'icon' => 'fa-filter',
                'link' => 'admin/properties/property_details/' . $data['prop_id'] . '/5',
                'value1' => $data['comment'],
            );
            $this->items_model->_table_name = "tbl_activities";
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);
            $notifiedUsers = array();
            $property_info = $this->items_model->check_by(array('prop_id' => $data['prop_id']), 'tbl_properties');
            $notifiedUsers = array();
            if (!empty($property_info->permission) && $property_info->permission != 'all') {
                $permissionUsers = json_decode($property_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->items_model->allowad_user_id('156');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_new_comment',
                            'link' => 'admin/properties/property_details/' . $property_info->prop_id . '/5',
                            'value' => lang('property') . ' ' . $property_info->prop_title,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
            $response_data = "";
            $view_data['comment_details'] = $this->db->where(array('task_comment_id' => $comment_id))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
            $response_data = $this->load->view("admin/properties/comments_list", $view_data, true);
            echo json_encode(array("status" => 'success', "data" => $response_data, 'message' => lang('property_comment_save')));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            exit();
        }
    }

    public function delete_files($task_attachment_id) {
        $file_info = $this->items_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'properties',
            'module_field_id' => $file_info->prop_id,
            'activity' => 'activity_property_attachfile_deleted',
            'icon' => 'fa-rocket',
            'link' => 'admin/properties/property_details/' . $file_info->prop_id . '/5',
            'value1' => $file_info->title,
        );
        $this->items_model->_table_name = "tbl_activities";
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);
        $this->items_model->_table_name = "tbl_task_attachment";
        $this->items_model->delete_multiple(array('task_attachment_id' => $task_attachment_id));
        $uploadFileinfo = $this->db->where('task_attachment_id', $task_attachment_id)->get('tbl_task_uploaded_files')->result();
        if (!empty($uploadFileinfo)) {
            foreach ($uploadFileinfo as $Fileinfo) {
                remove_files($Fileinfo->file_name);
            }
        }
        $this->items_model->_table_name = "tbl_task_uploaded_files";
        $this->items_model->delete_multiple(array('task_attachment_id' => $task_attachment_id));
        echo json_encode(array("status" => 'success', 'message' => lang('property_attachfile_deleted')));
        exit();
    }

    public function download_files($uploaded_files_id, $comments = null) {
        $this->load->helper('download');
        if (!empty($comments)) {
            if ($uploaded_files_id) {
                $down_data = file_get_contents('uploads/' . $uploaded_files_id);
                force_download($uploaded_files_id, $down_data);
            } else {
                $type = "error";
                $message = 'Operation Fieled !';
                set_message($type, $message);
                if (empty($_SERVER['HTTP_REFERER'])) {
                    redirect('admin/properties');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
        } else {
            $uploaded_files_info = $this->items_model->check_by(array('uploaded_files_id' => $uploaded_files_id), 'tbl_task_uploaded_files');
            if ($uploaded_files_info->uploaded_path) {
                $data = file_get_contents($uploaded_files_info->uploaded_path);
                force_download($uploaded_files_info->file_name, $data);
            } else {
                $type = "error";
                $message = lang('operation_failed');
                set_message($type, $message);
                if (empty($_SERVER['HTTP_REFERER'])) {
                    redirect('admin/properties');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
        }
    }

    public function download_all_files($attachment_id) {
        $uploaded_files_info = $this->db->where('task_attachment_id', $attachment_id)->get('tbl_task_uploaded_files')->result();

        $attachment_info = $this->db->where('task_attachment_id', $attachment_id)->get('tbl_task_attachment')->row();
        $this->load->library('zip');
        if (!empty($uploaded_files_info)) {
            $filename = slug_it($attachment_info->title);
            foreach ($uploaded_files_info as $v_files) {
                $down_data = ($v_files->files);
                $this->zip->read_file($down_data);
            }
            $this->zip->download($filename . '.zip');
        } else {
            $type = "error";
            $message = lang('operation_failed');
            set_message($type, $message);
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/properties');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function delete_property($id) {
        $can_delete = $this->items_model->can_action('tbl_properties', 'delete', array('prop_id' => $id));
        if (!empty($can_delete)) {
            $property_info = $this->items_model->check_by(array('prop_id' => $id), 'tbl_properties');
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'properties',
                'module_field_id' => $id,
                'activity' => 'activity_property_deleted',
                'icon' => 'fa-rocket',
                'value1' => $property_info->prop_title
            );
            $this->items_model->_table_name = 'tbl_activities';
            $this->items_model->_primary_key = 'activities_id';
            $this->items_model->save($activity);
            $this->items_model->_table_name = 'tbl_properties';
            $this->items_model->_primary_key = 'prop_id';
            $deleteData = array("view_in" => "archive");
            $this->items_model->save($deleteData, $property_info->prop_id);
            $type = 'success';
            $message = lang('property_deleted');
            echo json_encode(array("status" => $type, 'message' => $message));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('there_in_no_value')));
            exit();
        }
    }

    public function getOwners(){
        $searchWhere = $this->input->post("searchValue");
        $query = $this->db->select("leads_id, salutaiton, lead_name")->like("lead_name", $searchWhere)->limit(50)->get("tbl_leads");
        $reply = "<!--[if IE]><select><!--<![endif]-->";
        foreach($query->result() as $lead){
          $reply .= '<option value="' . ((!empty($lead->salutaiton)) ? $lead->salutaiton . ". " : "") . $lead->lead_name . '" data-owner-id="' . $lead->leads_id . '">';
        }
        $reply .= "<!--[if IE]><select><!--<![endif]-->";

        echo $reply;
    }

    public function new_landlord(){
        $data['title'] = lang('lead_status');
        $status_info = $this->db->order_by('order_no', 'ASC')->get('tbl_lead_status')->result();
        if (!empty($status_info)) {
            foreach ($status_info as $v_status) {
                $data['status_info'][$v_status->lead_type][] = $v_status;
            }
        }
        $data['nationalities'] = $this->db->get('tbl_countries')->result();
        $data['assign_user'] = $this->items_model->allowad_user('55');
        $data['subview'] = $this->load->view('admin/properties/_modal_new_landlord', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function property_addition($addON){
      $data['dropzone'] = true;
      $data['modalTitle'] = lang($addON);
      $data['subview'] = $this->load->view('admin/properties/_modal_property_addidtion', $data, FALSE);
      $this->load->view('admin/_layout_modal', $data);
    }
}
