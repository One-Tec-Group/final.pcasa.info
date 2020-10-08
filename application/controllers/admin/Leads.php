<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Leads extends Admin_Controller {

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
        $data['title'] = lang('all_leads');
        if (!empty($id)) {
            if ($id == 'kanban') {
                $data['active'] = 1;
                $k_session['leads_kanban'] = $id;
                $this->session->set_userdata($k_session);
            } elseif ($id == 'list') {
                $data['active'] = 1;
                $this->session->unset_userdata('leads_kanban');
            } else {
                $data['active'] = 2;
                $can_edit = $this->items_model->can_action('tbl_leads', 'edit', array('leads_id' => $id));
                if (!empty($can_edit)) {
                    $data['leads_info'] = $this->items_model->check_by(array('leads_id' => $id), 'tbl_leads');
                }
                $this->session->unset_userdata('leads_kanban');
            }
        } else {
            $data['active'] = 1;
        }
        $status_info = $this->db->order_by('order_no', 'ASC')->get('tbl_lead_status')->result();
        if (!empty($status_info)) {
            foreach ($status_info as $v_status) {
                $data['status_info'][$v_status->lead_type][] = $v_status;
            }
        }
        $data['nationalities'] = $this->db->get('tbl_countries')->result();
        $data['assign_user'] = $this->items_model->allowad_user('55');
        $data['subview'] = $this->load->view('admin/leads/all_leads', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function leadList() {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_leads';
            $main_column = array('lead_name', 'email', 'phone', 'lead_source_id', 'lead_category_id', 'lead_status_id', 'permission');
            $action_array = array('leads_id');
            $result = array_merge($main_column, $action_array);
            $this->datatables->column_order = $result;
            $this->datatables->column_search = $result;
            $this->datatables->join_where = $this->filters();
            $this->datatables->order = array('leads_id' => 'desc');
            $where = array();
            $fetch_data = $this->datatables->get_leads();
            $data = array();
            $edited = can_action('55', 'edited');
            $deleted = can_action('55', 'deleted');
            foreach ($fetch_data as $_key => $v_leads) {
                $action = null;
                if (!empty($v_leads) && $v_leads->converted_client_id == 0) {
                    $can_edit = $this->items_model->can_action('tbl_leads', 'edit', array('leads_id' => $v_leads->leads_id));
                    $can_delete = $this->items_model->can_action('tbl_leads', 'delete', array('leads_id' => $v_leads->leads_id));
                    $sub_array = array();
                    $name = null;
                    $name .= '<a class="text-info" href="' . base_url() . 'admin/leads/leads_details/' . $v_leads->leads_id . '">' . (($v_leads->salutaiton != NULL && $v_leads->salutaiton != "") ? $v_leads->salutaiton . ". " : "") . $v_leads->lead_name . '</a>';
                    $sub_array[] = $name;
                    $sub_array[] = $v_leads->email;
                    $phoneInformation = "";
                    $phonenumber = (($v_leads->mobile != NULL && $v_leads->mobile != "") ? $v_leads->mobile : (($v_leads->phone2 != NULL && $v_leads->phone2 != "") ? $v_leads->phone2 : (($v_leads->phone3 != NULL && $v_leads->phone3 != "") ? $v_leads->phone3 : (($v_leads->phone4 != NULL && $v_leads->phone4 != "") ? $v_leads->phone4 : "NO PHONE NUMBER FOUND"))));
                    $phonenumber = ltrim($phonenumber, '0');
                    $phone_information = $this->db->get('tbl_countries')->result();
                    foreach ($phone_information as $single_phone) {
                        if ((strlen($single_phone->calling_code) + 1) == strlen($single_phone->phone_code)) {
                            if (substr($phonenumber, 0, strlen($single_phone->calling_code)) === $single_phone->calling_code || substr($phonenumber, 0, strlen($single_phone->phone_code)) === $single_phone->phone_code) {
                                $d = new DateTime("now", new DateTimeZone($single_phone->time_zone));
                                $phoneInformation .= '<div align="left" style="display: inline-flex;"><img height="50px" class="img-rounded" src="' . base_url("assets/img/flags/" . $single_phone->flag) . '"><div style="margin-left:10px;"><i>' . $phonenumber . '</i>';
                                $phoneInformation .= '<div style="font-size:10px;"><i>' . $single_phone->long_name . '</i><i><br/>Time: ' . $d->format("h:i A") . '</i></div></div></div>';
                                break;
                            }
                        }
                    }
                    $phoneInformation = (($phoneInformation != NULL && $phoneInformation != "") ? $phoneInformation : $phonenumber);
                    $sub_array[] = $phoneInformation;
                    $change_source = null;
                    $ch_url = base_url() . 'admin/leads/change_source/';
                    $asource_info = $this->db->get('tbl_lead_source')->result();
                    if (!empty($v_leads->lead_source_id)) {
                        $lead_source = $this->db->where('lead_source_id', $v_leads->lead_source_id)->get('tbl_lead_source')->row();
                    }
                    $change_source .= '<div class="btn-group"><button class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                                  ' . ((isset($lead_source) && $lead_source->lead_source != NULL && $lead_source->lead_source != "") ? $lead_source->lead_source : "NOT SET") . '
                                  <span class="caret"></span></button><ul class="dropdown-menu animated zoomIn" style="margin:0;max-height:300px;overflow:auto;">';
                    foreach ($asource_info as $v_source) {
                        $change_source .= '<li><a href="' . $ch_url . $v_leads->leads_id . '/' . $v_source->lead_source_id . '">' . lang($v_source->lead_source) . '</a></li>';
                    }
                    $change_source .= '</ul></div>';
                    $sub_array[] = $change_source;
                    $change_category = null;
                    $ch_url = base_url() . 'admin/leads/change_category/';
                    $acategory_info = $this->db->get('tbl_lead_category')->result();
                    if (!empty($v_leads->lead_category_id)) {
                        $lead_category = $this->db->where('lead_category_id', $v_leads->lead_category_id)->get('tbl_lead_category')->row();
                    }
                    $change_category .= '<div class="btn-group"><button class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                                  ' . ((isset($lead_category) && $lead_category->lead_category != NULL && $lead_category->lead_category != "") ? $lead_category->lead_category : "NOT SET") . '
                                  <span class="caret"></span></button><ul class="dropdown-menu animated zoomIn" style="margin:0;max-height:300px;overflow:auto;">';
                    foreach ($acategory_info as $v_category) {
                        $change_category .= '<li><a href="' . $ch_url . $v_leads->leads_id . '/' . $v_category->lead_category_id . '">' . lang($v_category->lead_category) . '</a></li>';
                    }
                    $change_category .= '</ul></div>';
                    $sub_array[] = $change_category;
                    $change_status = null;
                    $ch_url = base_url() . 'admin/leads/change_status_modal/';
                    $astatus_info = $this->db->get('tbl_lead_status')->result();
                    if (!empty($v_leads->lead_status_id)) {
                        $lead_status = $this->db->where('lead_status_id', $v_leads->lead_status_id)->get('tbl_lead_status')->row();
                        if ($lead_status->lead_type == 'open') {
                            $change_status .= '<div class="btn-group"><button class="btn btn-xs btn-success dropdown-toggle" data-toggle="dropdown" title="' . lang($lead_status->lead_type) . '">';
                        } else {
                            $change_status .= '<div class="btn-group"><button class="btn btn-xs btn-warning dropdown-toggle" data-toggle="dropdown" title="' . lang($lead_status->lead_type) . '">';
                        }
                    }
                    $change_status .= ((isset($lead_status) && $lead_status->lead_status != NULL && $lead_status->lead_status != "") ? $lead_status->lead_status : "NOT SET") . '
                                      <span class="caret"></span></button><ul class="dropdown-menu animated zoomIn" style="margin:0;max-height:300px;overflow:auto;">';
                    foreach ($astatus_info as $v_status) {
                        $change_status .= '<li><a href="' . $ch_url . $v_leads->leads_id . '/' . $v_status->lead_status_id . '" data-toggle="modal" data-target="#myModal_lg" title="' . $v_status->lead_status . '">' . lang($v_status->lead_type) . '-' . $v_status->lead_status . '</a></li>';
                        // $change_status .= '<li><a href="' . $ch_url . $v_leads->leads_id . '/' . $v_status->lead_status_id . '">' . lang($v_status->lead_type) . '-' . $v_status->lead_status . '</a></li>';
                    }
                    $change_status .= '</ul></div>';
                    $sub_array[] = $change_status;
                    $assigned = null;
                    if ($v_leads->permission != 'all') {
                        $get_permission = json_decode($v_leads->permission);
                        if (!empty($get_permission)) :
                            foreach ($get_permission as $permission => $v_permission) :
                                $user_info = $this->db->where(array('user_id' => $permission))->get('tbl_users')->row();
                                if (!empty($user_info)) {
                                    if ($user_info->role_id == 1) {
                                        $label = 'circle-danger';
                                    } else {
                                        $label = 'circle-success';
                                    }
                                    $assigned .= '<a href="#" data-toggle="tooltip" data-placement="top" title="' . fullname($permission) . '">
                                                      <img src="' . base_url() . staffImage($permission) . '" class="img-circle img-xs" alt="">
                                                <span style="margin: 0px 0 8px -10px;" class="circle ' . $label . '  circle-lg"></span></a>';
                                }
                            endforeach;
                        endif;
                    } else {
                        $assigned .= '<strong>' . lang("everyone") . '</strong><i title="' . lang('permission_for_all') . '" class="fa fa-question-circle" data-toggle="tooltip" data-placement="top"></i>';
                    };
                    if (!empty($can_edit) && !empty($edited)) {
                        $assigned .= '<span data-placement="top" data-toggle="tooltip" title="' . lang('add_more') . '"><a data-toggle="modal" data-target="#myModal" href="' . base_url() . 'admin/leads/update_users/' . $v_leads->leads_id . '" class="text-default ml"><i class="fa fa-plus"></i></a></span>';
                    };
                    $sub_array[] = $assigned;
                    $action .= '<a href="' . base_url("admin/CrmMailServerAPI/lead_new_email/" . $v_leads->leads_id) . '" class="btn btn-success btn-xs" data-toggle="modal" data-target="#myModal" title="Send E-mail"><span class="fa fa-envelope"></span></a>' . ' ';
                    $action .= '<a href="javascript:;" data-lead-id="' . $v_leads->leads_id . '" data-lead="' . $_key . '" class="btn btn-info btn-xs lead_view_btn lead_view_' . $v_leads->leads_id . '" onclick="leadFastView(' . $v_leads->leads_id . ', ' . $_key . ');" data-toggle="tooltip" data-placement="top" title="View"><span class="fa fa-eye"></span></a>' . ' ';
                    if (!empty($can_edit) && !empty($edited)) {
                        $action .= btn_edit('admin/leads/index/' . $v_leads->leads_id) . ' ';
                    }
                    $action .= '<a href="' . base_url("admin/opportunities/leadConvert/" . $v_leads->leads_id) . '" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#myModal_large" title="Convert to opportunity"><span class="fa fa-eercast"></span></a>' . ' ';
                    if (!empty($can_delete) && !empty($deleted)) {
                        $action .= ajax_anchor(base_url("admin/leads/delete_leads/$v_leads->leads_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                    }
                    $sub_array[] = $action;
                    $data[] = $sub_array;
                }
            }
            render_table($data, $where);
        } else {
            redirect('admin/dashboard');
        }
    }

    protected function filters() {
        if ($this->input->post("tableFilters") != NULL && $this->input->post("tableFilters") != "") {
            $filterArray = array("filter_assigned_to" => "permission", "filter_category" => "lead_category_id", "filter_source" => "lead_source_id", "filter_nationality" => "nationality",
                "filter_country" => "country", "filter_full_name" => "lead_name", "filter_email" => "email", "filter_mobile" => "mobile");
            $filterJoinArray = array("filter_community", "filter_sub_community");
            $filterCheckBox = array("filter_own_property");
            $returnFilters = "";
            $joinFilters = NULL;
            $filterCount = 0;
            $allFilters = $this->input->post("tableFilters");
            $filters = explode("&", $allFilters);
            for ($i = 0; $i < count($filters); $i++) {
                $filter = explode("=", $filters[$i]);
                if ($filter[1] != NULL && $filter[1] != "") {
                    if (!(in_array($filter[0], $filterJoinArray)) && !(in_array($filter[0], $filterCheckBox))) {
                        if ($filter[0] == "filter_assigned_to") {
                            $returnFilters .= (($filterCount > 0) ? " AND " : "") . "`tbl_leads`.`" . $filterArray[$filter[0]] . "` LIKE '%" . '{"' . $filter[1] . '":' . "%'";
                        } else if (in_array($filter[0], array("filter_full_name", "filter_email", "filter_mobile"))) {
                            $returnFilters .= (($filterCount > 0) ? " AND " : "") . "`tbl_leads`.`" . $filterArray[$filter[0]] . "` LIKE '%" . (($filter[0] == "filter_email") ? str_replace("%40", "@", str_replace("+", " ", $filter[1])) : str_replace("+", " ", $filter[1])) . "%'";
                        } else {
                            $returnFilters .= (($filterCount > 0) ? " AND " : "") . "`tbl_leads`.`" . $filterArray[$filter[0]] . "` = '" . str_replace("+", " ", $filter[1]) . "'";
                        }
                        $filterCount++;
                    } else if (in_array($filter[0], $filterCheckBox)) {
                        $joinFilter = "`tbl_properties`.`prop_owner_id` = `tbl_leads`.`leads_id`";
                        $joinFilters = array("`tbl_properties`", $joinFilter);
                    } else {
                        $joinFilter = "`tbl_properties`.`prop_owner_id` = `tbl_leads`.`leads_id`";
                        if ($filter[0] == "filter_community") {
                            $joinFilter .= " AND `tbl_properties`.`prop_community` = '" . str_replace("+", " ", $filter[1]) . "'";
                        } else {
                            $joinFilter .= " AND `tbl_properties`.`prop_sub_community` = '" . str_replace("+", " ", $filter[1]) . "'";
                        }
                        $joinFilters = array("`tbl_properties`", $joinFilter);
                    }
                }
            }
            $returnFilters .= (($filterCount > 0) ? " AND " : "") . "`tbl_leads`.`show_in` = 'in_leads'";
            return array($returnFilters, $joinFilters);
        } else {
            return array(NULL, NULL);
        }
    }

    public function lead_status() {
        $data['title'] = lang('lead_status');
        $data['subview'] = $this->load->view('admin/leads/lead_status', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function update_lead_status($id = null) {
        $this->items_model->_table_name = 'tbl_lead_status';
        $this->items_model->_primary_key = 'lead_status_id';
        $cate_data['lead_status'] = $this->input->post('lead_status', TRUE);
        $cate_data['lead_type'] = $this->input->post('lead_type', TRUE);
        $cate_data['order_no'] = $this->input->post('order_no', TRUE);
        $where = array('lead_status' => $cate_data['lead_status']);
        if (!empty($id)) {
            $lead_status_id = array('lead_status_id !=' => $id);
        } else {
            $lead_status_id = null;
        }
        $check_lead_status = $this->items_model->check_update('tbl_lead_status', $where, $lead_status_id);
        if (!empty($check_lead_status)) {
            $type = 'error';
            $msg = "<strong style='color:#000'>" . $cate_data['lead_status'] . '</strong>  ' . lang('already_exist');
        } else {
            $id = $this->items_model->save($cate_data, $id);
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'settings',
                'module_field_id' => $id,
                'activity' => ('activity_added_a_lead_status'),
                'value1' => $cate_data['lead_status']
            );
            $this->items_model->_table_name = 'tbl_activities';
            $this->items_model->_primary_key = 'activities_id';
            $this->items_model->save($activity);
            $type = "success";
            $msg = lang('lead_status_added');
        }
        if (!empty($id)) {
            $result = array(
                'id' => $id,
                'lead_status' => $cate_data['lead_status'],
                'status' => $type,
                'message' => $msg,
            );
        } else {
            $result = array(
                'status' => $type,
                'message' => $msg,
            );
        }
        echo json_encode($result);
        exit();
    }

    public function lead_source() {
        $data['title'] = lang('lead_source');
        $data['subview'] = $this->load->view('admin/leads/lead_source', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function update_lead_source($id = null) {
        $this->items_model->_table_name = 'tbl_lead_source';
        $this->items_model->_primary_key = 'lead_source_id';
        $source_data['lead_source'] = $this->input->post('lead_source', TRUE);
        $where = array('lead_source' => $source_data['lead_source']);
        if (!empty($id)) {
            $lead_source_id = array('lead_source_id !=' => $id);
        } else {
            $lead_source_id = null;
        }
        $check_lead_status = $this->items_model->check_update('tbl_lead_source', $where, $lead_source_id);
        if (!empty($check_lead_status)) {
            $type = 'error';
            $msg = "<strong style='color:#000'>" . $source_data['lead_source'] . '</strong>  ' . lang('already_exist');
        } else {
            $id = $this->items_model->save($source_data, $id);
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'settings',
                'module_field_id' => $id,
                'activity' => ('activity_added_a_lead_source'),
                'value1' => $source_data['lead_source']
            );
            $this->items_model->_table_name = 'tbl_activities';
            $this->items_model->_primary_key = 'activities_id';
            $this->items_model->save($activity);
            $type = "success";
            $msg = lang('lead_source_added');
        }
        if (!empty($id)) {
            $result = array(
                'id' => $id,
                'lead_source' => $source_data['lead_source'],
                'status' => $type,
                'message' => $msg,
            );
        } else {
            $result = array(
                'status' => $type,
                'message' => $msg,
            );
        }
        echo json_encode($result);
        exit();
    }

    public function import_leads() {
        $data['title'] = lang('import_leads');
        $data['assign_user'] = $this->items_model->allowad_user('55');
        $status_info = $this->db->get('tbl_lead_status')->result();
        if (!empty($status_info)) {
            foreach ($status_info as $v_status) {
                $data['status_info'][$v_status->lead_type][] = $v_status;
            }
        }
        $data['subview'] = $this->load->view('admin/leads/import_leads', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function save_imported() {
        $this->load->library('excel');
        ob_start();
        $file = $_FILES["upload_file"]["tmp_name"];
        if (!empty($file)) {
            $valid = false;
            $types = array('Excel2007', 'Excel5');
            foreach ($types as $type) {
                $reader = PHPExcel_IOFactory::createReader($type);
                if ($reader->canRead($file)) {
                    $valid = true;
                }
            }
            if (!empty($valid)) {
                try {
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                } catch (Exception $e) {
                    die("Error loading file: " . $e->getMessage());
                }
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                for ($x = 2; $x <= count($sheetData); $x++) {
                    $data = $this->items_model->array_from_post(array('client_id', 'lead_status_id', 'lead_source_id'));
                    $data['lead_name'] = trim($sheetData[$x]["A"]);
                    $data['organization'] = trim($sheetData[$x]["B"]);
                    $data['contact_name'] = trim($sheetData[$x]["C"]);
                    $data['email'] = trim($sheetData[$x]["D"]);
                    $data['phone'] = trim($sheetData[$x]["E"]);
                    $data['mobile'] = trim($sheetData[$x]["F"]);
                    $data['address'] = trim($sheetData[$x]["G"]);
                    $data['city'] = trim($sheetData[$x]["H"]);
                    $data['country'] = trim($sheetData[$x]["I"]);
                    $data['facebook'] = trim($sheetData[$x]["J"]);
                    $data['skype'] = trim($sheetData[$x]["K"]);
                    $data['twitter'] = trim($sheetData[$x]["L"]);
                    $data['notes'] = trim($sheetData[$x]["M"]);
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
                    } else {
                        $assigned = 'all';
                    }
                    $data['permission'] = $assigned;
                    $this->items_model->_table_name = 'tbl_leads';
                    $this->items_model->_primary_key = 'leads_id';
                    $this->items_model->save($data);
                }
                $type = 'success';
                $message = lang('save_leads');
            } else {
                $type = 'error';
                $message = "Sorry your uploaded file type not allowed ! please upload XLS/CSV File ";
            }
        } else {
            $type = 'error';
            $message = "You did not Select File! please upload XLS/CSV File ";
        }
        set_message($type, $message);
        redirect('admin/leads');
    }

    public function saved_leads($id = NULL) {
        $created = can_action('55', 'created');
        $edited = can_action('55', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $this->items_model->_table_name = 'tbl_leads';
            $this->items_model->_primary_key = 'leads_id';
            $data = $this->items_model->array_from_post(
                    array('salutaiton', 'lead_name', 'organization', 'lead_status_id', 'lead_category_id', 'lead_source_id', 'nationality', 'contact_name',
                        'email', 'email2', 'email3', 'phone', 'mobile', 'phone2', 'phone3', 'phone4', 'passport_number', 'passport_expire', 'address', 'city',
                        'state', 'country', 'skype', 'fax', 'date_of_birth', 'facebook', 'twitter', 'notes'));
            $where = array('client_id' => $data['client_id'], 'lead_name' => $data['lead_name']);
            if (!empty($id)) {
                $leads_id = array('leads_id !=' => $id);
            } else {
                $leads_id = null;
            }
            $check_leads = $this->items_model->check_update('tbl_leads', $where, $leads_id);
            if (!empty($check_leads)) {
                $type = 'error';
                $msg = "<strong style='color:#000'>" . $data['lead_name'] . '</strong>  ' . lang('already_exist');
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
                    $data['permission'] = $assigned;
                } else {
                    set_message('error', lang('assigned_to') . ' Field is required');
                    if (empty($_SERVER['HTTP_REFERER'])) {
                        redirect('admin/leads');
                    } else {
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }
                $return_id = $this->items_model->save($data, $id);
                if (!empty($id)) {
                    $id = $id;
                    $action = 'activity_update_leads';
                    $description = 'not_update_leads';
                    $msg = lang('update_leads');
                } else {
                    $id = $return_id;
                    $action = 'activity_save_leads';
                    $description = 'not_save_leads';
                    $msg = lang('save_leads');
                }
                $u_data['index_no'] = $id;
                $id = $this->items_model->save($u_data, $id);
                save_custom_field(5, $id);
                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'leads',
                    'module_field_id' => $id,
                    'activity' => $action,
                    'icon' => 'fa-rocket',
                    'link' => 'admin/leads/leads_details/' . $id,
                    'value1' => $data['lead_name']
                );
                $this->items_model->_table_name = 'tbl_activities';
                $this->items_model->_primary_key = 'activities_id';
                $this->items_model->save($activity);
                $type = "success";
                $leads_info = $this->items_model->check_by(array('leads_id' => $id), 'tbl_leads');
                $notifiedUsers = array();
                if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                    $permissionUsers = json_decode($leads_info->permission);
                    foreach ($permissionUsers as $user => $v_permission) {
                        array_push($notifiedUsers, $user);
                    }
                } else {
                    $notifiedUsers = $this->items_model->allowad_user_id('55');
                }
                if (!empty($notifiedUsers)) {
                    foreach ($notifiedUsers as $users) {
                        if ($users != $this->session->userdata('user_id')) {
                            add_notification(array(
                                'to_user_id' => $users,
                                'from_user_id' => true,
                                'description' => $description,
                                'link' => 'admin/leads/leads_details/' . $leads_info->leads_id,
                                'value' => lang('lead') . ' ' . $leads_info->lead_name,
                            ));
                        }
                    }
                    show_notification($notifiedUsers);
                }
            }
            $message = $msg;
            set_message($type, $message);
        }
        redirect('admin/leads');
    }

    public function lead_fast_view() {
        if ($this->input->post()) {
            $lead_id = $this->input->post("lead_id");
            $leads_details = $this->items_model->check_by(array('leads_id' => $lead_id), 'tbl_leads');
            $leadResponse = "";
            if ($leads_details != null) {
                $leadResponse .= "<tr id='lead_fast_view' class='lead_fast_view'><td colspan='9' style='background-color: #eeeeee;'>";
                $leadResponse .= '<div class="panel-body row form-horizontal task_details">
              <div class="form-group col-sm-12">
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>' . lang('nationality') . ': </strong>
                      </label>
                      <p class="form-control-static">';
                $lead_nationality = $this->db->where('id', $leads_details->nationality)->get('tbl_countries')->row();
                if (isset($lead_nationality) && $lead_nationality != NULL) {
                    $leadResponse .= '<img width="30px" class="img-rounded" src="' . base_url("assets/img/flags/" . $lead_nationality->flag) . '">&nbsp;&nbsp;<i style="margin-left: 10px;">' . $lead_nationality->nationality . '</i>';
                } else {
                    $leadResponse .= "UNKNOWN";
                }
                $leadResponse .= '</p>
                  </div>
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>' . lang('lead_passport') . ': </strong>
                      </label>';
                $leadResponse .= '<div>
                          <p class="form-control-static">' . $leads_details->passport_number . " (" . (($leads_details->passport_expire != NULL && $leads_details->passport_expire != "") ? date("m-Y", strtotime($leads_details->passport_expire)) : "UNKNOWN") . ")" . '</p>
                      </div>';
                $leadResponse .= '</div>
              </div>
              <div class="form-group col-sm-12">
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>' . lang('email_2') . ': </strong></label>
                      <p class="form-control-static">';
                $leadResponse .= $leads_details->email2;
                $leadResponse .= '</p>
                  </div>
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>' . lang('lead_date_of_birth') . ': </strong></label>
                      <p class="form-control-static">';
                $leadResponse .= date("d-m-Y", strtotime($leads_details->passport_number));
                $leadResponse .= '</p>
                  </div>
              </div>
              <div class="form-group col-sm-12">
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>' . lang('email_3') . ': </strong> </label>
                      <p class="form-control-static">';
                $leadResponse .= $leads_details->email3;
                $leadResponse .= '</p>
                  </div>
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>' . lang('skype_id') . ': </strong></label>
                      <a href="skype:' . "'";
                if (!empty($leads_details->skype)) {
                    $leadResponse .= $leads_details->skype;
                }
                $leadResponse .= "'" . '">
                          <p class="form-control-static">';
                if (!empty($leads_details->skype)) {
                    $leadResponse .= $leads_details->skype;
                }
                $leadResponse .= '</p></a>';
                $leadResponse .= '</div>
              </div>
              <div class="form-group col-sm-12">
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>' . lang('address') . ': </strong></label>
                      <p class="form-control-static">';
                if (!empty($leads_details->address)) {
                    $leadResponse .= $leads_details->address;
                }
                $leadResponse .= '</p>
                  </div>
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>' . lang('fax') . ': </strong></label>
                      <p class="form-control-static">';
                $leadResponse .= $leads_details->fax;
                $leadResponse .= '</p>
                  </div>
              </div>
              <div class="form-group col-sm-12">
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>' . lang('city') . ': </strong></label>
                      <p class="form-control-static">';
                if (!empty($leads_details->city)) {
                    $leadResponse .= $leads_details->city;
                }
                $leadResponse .= '</p>
                  </div>
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>' . lang('landline') . ': </strong>
                      </label>
                      <p class="form-control-static">';
                $leadResponse .= $leads_details->phone;
                $leadResponse .= '</p>
                  </div>
              </div>
              <div class="form-group col-sm-12">
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>' . lang('state') . ': </strong></label>
                      <p class="form-control-static">';
                if (!empty($leads_details->state)) {
                    $leadResponse .= $leads_details->state;
                }
                $leadResponse .= '</p>
                  </div>
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>' . lang('phone_2') . ': </strong></label>
                      <div class="col-sm-7 "><p class="form-control-static"><strong>';
                $leads_details->phone2 = ltrim($leads_details->phone2, '0');
                $phone_information = $this->db->get('tbl_countries')->result();
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
                      <label class="control-label col-sm-5"><strong>' . lang('country') . ': </strong></label>
                      <p class="form-control-static">';
                if (!empty($leads_details->country)) {
                    $leadResponse .= $leads_details->country;
                }
                $leadResponse .= '</p>
                  </div>
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>' . lang('phone_3') . ': </strong></label>
                      <div class="col-sm-7 "><p class="form-control-static"><strong>';
                $leads_details->phone3 = ltrim($leads_details->phone3, '0');
                $phone_information = $this->db->get('tbl_countries')->result();
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
                          class="control-label col-sm-5"><strong>' . lang('twitter_profile_link') . ': </strong></label>
                      <a target="_blank" href="//';
                if (!empty($leads_details->twitter)) {
                    $leadResponse .= $leads_details->twitter;
                }
                $leadResponse .= '">
                          <p class="form-control-static">';
                if (!empty($leads_details->twitter)) {
                    $leadResponse .= $leads_details->twitter;
                }
                $leadResponse .= '</p></a>
                  </div>
                  <div class="col-sm-6">
                      <label class="control-label col-sm-5"><strong>' . lang('phone_4') . ': </strong></label>
                      <div class="col-sm-7"><p class="form-control-static"><strong>';
                $leads_details->phone4 = ltrim($leads_details->phone4, '0');
                $phone_information = $this->db->get('tbl_countries')->result();
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
                              class="control-label col-sm-5"><strong>' . lang('facebook_profile_link') . ': </strong></label>
                          <a target="_blank" href="//';
                if (!empty($leads_details->facebook)) {
                    $leadResponse .= $leads_details->facebook;
                }
                $leadResponse .= '">
                              <p class="form-control-static">';
                if (!empty($leads_details->facebook)) {
                    $leadResponse .= $leads_details->facebook;
                }
                $leadResponse .= '</p></a>
                      </div>
                      <div class="col-sm-6">
                          <label class="control-label col-sm-5"><strong>' . lang('notes') . ': </strong></label>
                          <div class="col-sm-7"><p class="form-control-static">' . $leads_details->notes . '</p></div>
                      </div>';
                $leadResponse .= '</div>
              <div class="col-sm-12 text text-right">
              <blockquote class="col-sm-8"></blockquote>
                  <blockquote class="col-sm-2" style="font-size: 14px;margin-bottom: 0px;"><strong>' . lang('created_time') . ':  </strong>';
                if (!empty($leads_details->notes)) {
                    $leadResponse .= date("d-m-Y", strtotime($leads_details->created_time));
                }
                $leadResponse .= '</blockquote>
                  <blockquote class="col-sm-2" style="font-size: 14px;margin-bottom: 0px;"><strong>' . lang('modified_time') . ':  </strong>';
                if (!empty($leads_details->notes)) {
                    $leadResponse .= (($leads_details->modified_time != NULL && $leads_details->modified_time != "") ? date("d-m-Y H:i:s", strtotime($leads_details->modified_time)) : "NEVER");
                }
                $leadResponse .= '</blockquote>
              </div>
          </div>';
                $leadResponse .= "</td></tr>";
            }
            echo $leadResponse;
        }
    }

    public function leads_details($id, $active = NULL, $op_id = NULL) {
        $data['title'] = lang('leads_details');
        $data['leads_details'] = $this->items_model->check_by(array('leads_id' => $id), 'tbl_leads');
        $this->items_model->_table_name = "tbl_task_attachment";
        $this->items_model->_order_by = "leads_id";
        $data['files_info'] = $this->items_model->get_by(array('leads_id' => $id), FALSE);
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
        } elseif ($active == 'metting') {
            $data['active'] = 3;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 2;
            $data['mettings_info'] = $this->items_model->check_by(array('mettings_id' => $op_id), 'tbl_mettings');
        } elseif ($active == 'call') {
            $data['active'] = 2;
            $data['sub_active'] = 2;
            $data['call_info'] = $this->items_model->check_by(array('calls_id' => $op_id), 'tbl_calls');
            $data['sub_metting'] = 1;
        } else {
            $data['active'] = 1;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
        }
        $data['subview'] = $this->load->view('admin/leads/leads_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function update_users($id) {
        $can_edit = $this->items_model->can_action('tbl_leads', 'edit', array('leads_id' => $id));
        if (!empty($can_edit)) {
            $data['assign_user'] = $this->items_model->allowad_user('55');
            $data['leads_info'] = $this->items_model->check_by(array('leads_id' => $id), 'tbl_leads');
            $data['modal_subview'] = $this->load->view('admin/leads/_modal_users', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/leads');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function update_member($id) {
        $can_edit = $this->items_model->can_action('tbl_leads', 'edit', array('leads_id' => $id));
        if (!empty($can_edit)) {
            $leads_info = $this->items_model->check_by(array('leads_id' => $id), 'tbl_leads');
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
                    redirect('admin/leads');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
            $this->items_model->_table_name = "tbl_leads";
            $this->items_model->_primary_key = "leads_id";
            $this->items_model->save($data, $id);

            $msg = lang('update_leads');
            $activity = 'activity_update_leads';
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'leads',
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-rocket',
                'link' => 'admin/leads/leads_details/' . $id,
                'value1' => $leads_info->lead_name,
            );
            $this->items_model->_table_name = "tbl_activities";
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);
            $notifiedUsers = array();
            if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                $permissionUsers = json_decode($leads_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->items_model->allowad_user_id('55');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'assign_to_you_the_lead',
                            'link' => 'admin/leads/leads_details/' . $leads_info->leads_id,
                            'value' => lang('lead') . ' ' . $leads_info->lead_name,
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
            redirect('admin/leads');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function change_source($leads_id, $lead_source_id) {
        $can_edit = $this->items_model->can_action('tbl_leads', 'edit', array('leads_id' => $leads_id));
        if (!empty($can_edit)) {
            $data['lead_source_id'] = $lead_source_id;
            $this->items_model->_table_name = 'tbl_leads';
            $this->items_model->_primary_key = 'leads_id';
            $this->items_model->save($data, $leads_id);
            $leads_info = $this->items_model->check_by(array('leads_id' => $leads_id), 'tbl_leads');
            $notifiedUsers = array();
            if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                $permissionUsers = json_decode($leads_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->items_model->allowad_user_id('55');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_changed_source',
                            'link' => 'admin/leads/leads_details/' . $leads_info->leads_id,
                            'value' => lang('lead') . ' ' . $leads_info->lead_name,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
            $type = "success";
            $message = lang('change_source');
            set_message($type, $message);
        } else {
            set_message('error', lang('there_in_no_value'));
        }
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/leads');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function change_category($leads_id, $lead_category_id) {
        $can_edit = $this->items_model->can_action('tbl_leads', 'edit', array('leads_id' => $leads_id));
        if (!empty($can_edit)) {
            $data['lead_category_id'] = $lead_category_id;
            $this->items_model->_table_name = 'tbl_leads';
            $this->items_model->_primary_key = 'leads_id';
            $this->items_model->save($data, $leads_id);
            $leads_info = $this->items_model->check_by(array('leads_id' => $leads_id), 'tbl_leads');
            $notifiedUsers = array();
            if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                $permissionUsers = json_decode($leads_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->items_model->allowad_user_id('55');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_changed_source',
                            'link' => 'admin/leads/leads_details/' . $leads_info->leads_id,
                            'value' => lang('lead') . ' ' . $leads_info->lead_name,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
            $type = "success";
            $message = lang('change_category');
            set_message($type, $message);
        } else {
            set_message('error', lang('there_in_no_value'));
        }
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/leads');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function change_status_modal($leads_id, $lead_status_id) {
        $can_edit = $this->items_model->can_action('tbl_leads', 'edit', array('leads_id' => $leads_id));
        if (!empty($can_edit)) {
              $ddata['title'] = lang("change_status");
              $ddata['lead_data'] = $this->items_model->check_by(array('leads_id' => $leads_id), 'tbl_leads');;
              $ddata['status_data'] = $this->items_model->check_by(array('lead_status_id' => $lead_status_id), 'tbl_lead_status');;
              $data['modal_subview'] = $this->load->view('admin/leads/_modal_status_change', $ddata, FALSE);
              $this->load->view('admin/_layout_modal', $data);
            // $data['lead_status_id'] = $lead_status_id;
            // $this->items_model->_table_name = 'tbl_leads';
            // $this->items_model->_primary_key = 'leads_id';
            // $this->items_model->save($data, $leads_id);
            // $leads_info = $this->items_model->check_by(array('leads_id' => $leads_id), 'tbl_leads');
            // $notifiedUsers = array();
            // if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
            //     $permissionUsers = json_decode($leads_info->permission);
            //     foreach ($permissionUsers as $user => $v_permission) {
            //         array_push($notifiedUsers, $user);
            //     }
            // } else {
            //     $notifiedUsers = $this->items_model->allowad_user_id('55');
            // }
            // if (!empty($notifiedUsers)) {
            //     foreach ($notifiedUsers as $users) {
            //         if ($users != $this->session->userdata('user_id')) {
            //             add_notification(array(
            //                 'to_user_id' => $users,
            //                 'from_user_id' => true,
            //                 'description' => 'not_changed_status',
            //                 'link' => 'admin/leads/leads_details/' . $leads_info->leads_id,
            //                 'value' => lang('lead') . ' ' . $leads_info->lead_name,
            //             ));
            //         }
            //     }
            //     show_notification($notifiedUsers);
            // }
            // $type = "success";
            // $message = lang('change_status');
            // set_message($type, $message);
        } else {
            set_message('error', lang('there_in_no_value'));
        }
        // if (empty($_SERVER['HTTP_REFERER'])) {
        //     redirect('admin/leads');
        // } else {
        //     redirect($_SERVER['HTTP_REFERER']);
        // }
    }

    public function saved_call($leads_id, $id = NULL) {
        $data = $this->items_model->array_from_post(array('date', 'call_summary', 'client_id', 'user_id'));
        $data['leads_id'] = $leads_id;
        $this->items_model->_table_name = 'tbl_calls';
        $this->items_model->_primary_key = 'calls_id';
        $return_id = $this->items_model->save($data, $id);
        if (!empty($id)) {
            $id = $id;
            $action = 'activity_update_leads_call';
            $msg = lang('update_leads_call');
        } else {
            $id = $return_id;
            $action = 'activity_save_leads_call';
            $msg = lang('save_leads_call');
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $leads_id,
            'activity' => $action,
            'icon' => 'fa-rocket',
            'link' => 'admin/leads/leads_details/' . $leads_id . '/2',
            'value1' => $data['call_summary']
        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);
        $leads_info = $this->items_model->check_by(array('leads_id' => $leads_id), 'tbl_leads');
        $notifiedUsers = array();
        if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
            $permissionUsers = json_decode($leads_info->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->items_model->allowad_user_id('55');
        }
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_add_call',
                        'link' => 'admin/leads/leads_details/' . $leads_info->leads_id . '/2',
                        'value' => lang('lead') . ' ' . $leads_info->lead_name,
                    ));
                }
            }
            show_notification($notifiedUsers);
        }
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $leads_id . '/' . '2');
    }

    public function delete_leads_call($leads_id, $id) {
        $calls_info = $this->items_model->check_by(array('calls_id' => $id), 'tbl_calls');
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $leads_id,
            'activity' => 'activity_leads_call_deleted',
            'icon' => 'fa-rocket',
            'link' => 'admin/leads/leads_details/' . $leads_id . '/2',
            'value1' => $calls_info->call_summary
        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);
        $this->items_model->_table_name = 'tbl_calls';
        $this->items_model->_primary_key = 'calls_id';
        $this->items_model->delete($id);
        $type = 'success';
        $message = lang('leads_call_deleted');
        echo json_encode(array("status" => $type, 'message' => $message));
        exit();
    }

    public function delete_leads_mettings($leads_id, $id) {
        $mettings_info = $this->items_model->check_by(array('mettings_id' => $id), 'tbl_mettings');
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $leads_id,
            'activity' => 'activity_leads_call_deleted',
            'icon' => 'fa-rocket',
            'link' => 'admin/leads/leads_details/' . $leads_id . '/3',
            'value1' => $mettings_info->meeting_subject
        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);
        $this->items_model->_table_name = 'tbl_mettings';
        $this->items_model->_primary_key = 'mettings_id';
        $this->items_model->delete($id);
        $type = 'success';
        $message = lang('leads_mettings_deleted');
        echo json_encode(array("status" => $type, 'message' => $message));
        exit();
    }

    public function saved_metting($leads_id, $id = NULL) {
        $this->items_model->_table_name = 'tbl_mettings';
        $this->items_model->_primary_key = 'mettings_id';

        $data = $this->items_model->array_from_post(array('meeting_subject', 'user_id', 'location', 'description'));
        $data['start_date'] = strtotime($this->input->post('start_date', true) . ' ' . display_time($this->input->post('start_time', true)));
        $data['end_date'] = strtotime($this->input->post('end_date', true) . ' ' . display_time($this->input->post('end_time', true)));
        $data['leads_id'] = $leads_id;
        $user_id = serialize($this->items_model->array_from_post(array('attendees')));
        if (!empty($user_id)) {
            $data['attendees'] = $user_id;
        } else {
            $data['attendees'] = '-';
        }
        $return_id = $this->items_model->save($data, $id);
        if (!empty($id)) {
            $id = $id;
            $action = 'activity_update_leads_metting';
            $msg = lang('update_leads_metting');
        } else {
            $id = $return_id;
            $action = 'activity_save_leads_metting';
            $msg = lang('save_leads_metting');
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $leads_id,
            'activity' => $action,
            'icon' => 'fa-rocket',
            'link' => 'admin/leads/leads_details/' . $leads_id . '/3',
            'value1' => $data['meeting_subject']
        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);
        $leads_info = $this->items_model->check_by(array('leads_id' => $leads_id), 'tbl_leads');
        $notifiedUsers = array();
        if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
            $permissionUsers = json_decode($leads_info->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->items_model->allowad_user_id('55');
        }
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_add_meetings',
                        'link' => 'admin/leads/leads_details/' . $leads_info->leads_id . '/3',
                        'value' => lang('lead') . ' ' . $leads_info->lead_name,
                    ));
                }
            }
            show_notification($notifiedUsers);
        }
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $leads_id . '/' . '3');
    }

    public function save_comments() {
        $data['leads_id'] = $this->input->post('leads_id', TRUE);
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
            'module' => 'leads',
            'module_field_id' => $data['leads_id'],
            'activity' => 'activity_new_leads_comment',
            'icon' => 'fa-rocket',
            'link' => 'admin/leads/leads_details/' . $data['leads_id'] . '/4',
            'value1' => $data['comment'],
        );
        $this->items_model->_table_name = "tbl_activities";
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);
        if (!empty($comment_id)) {
            $leads_info = $this->items_model->check_by(array('leads_id' => $data['leads_id']), 'tbl_leads');
            $notifiedUsers = array();
            if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                $permissionUsers = json_decode($leads_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->items_model->allowad_user_id('55');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_new_comment',
                            'link' => 'admin/leads/leads_details/' . $leads_info->leads_id . '/4',
                            'value' => lang('lead') . ' ' . $leads_info->lead_name,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
            $response_data = "";
            $view_data['comment_details'] = $this->db->where(array('task_comment_id' => $comment_id))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
            $response_data = $this->load->view("admin/leads/comments_list", $view_data, true);
            echo json_encode(array("status" => 'success', "data" => $response_data, 'message' => lang('leads_comment_save')));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            exit();
        }
    }

    public function save_comments_reply($task_comment_id) {
        $data['leads_id'] = $this->input->post('leads_id', TRUE);
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
                'module' => 'leads',
                'module_field_id' => $data['leads_id'],
                'activity' => 'activity_new_comment_reply',
                'icon' => 'fa-rocket',
                'link' => $url . 'leads/leads_details/' . $data['leads_id'] . '/4',
                'value1' => $this->db->where('task_comment_id', $task_comment_id)->get('tbl_task_comment')->row()->comment,
                'value2' => $data['comment'],
            );
            $this->items_model->_table_name = "tbl_activities";
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);
            $leads_info = $this->items_model->check_by(array('leads_id' => $data['leads_id']), 'tbl_leads');
            $notifiedUsers = array($comments_info->user_id);
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_new_comment',
                            'link' => $url . 'leads/leads_details/' . $leads_info->leads_id . '/4',
                            'value' => lang('lead') . ' ' . $leads_info->lead_name,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
            $response_data = "";
            $view_data['comment_reply_details'] = $this->db->where(array('task_comment_id' => $comment_id))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
            $response_data = $this->load->view("admin/leads/comments_reply", $view_data, true);
            echo json_encode(array("status" => 'success', "data" => $response_data, 'message' => lang('leads_comment_save')));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            exit();
        }
    }

    public
            function delete_comments($task_comment_id = null) {
        $comments_info = $this->items_model->check_by(array('task_comment_id' => $task_comment_id), 'tbl_task_comment');

        if (!empty($comments_info->comments_attachment)) {
            $attachment = json_decode($comments_info->comments_attachment);
            foreach ($attachment as $v_file) {
                remove_files($v_file->fileName);
            }
        }
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'leads',
            'module_field_id' => $comments_info->leads_id,
            'activity' => 'activity_comment_deleted',
            'icon' => 'fa-rocket',
            'link' => 'admin/leads/leads_details/' . $comments_info->leads_id . '/4',
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
        $data = $this->items_model->array_from_post(array('title', 'description', 'leads_id'));
        $data['user_id'] = $this->session->userdata('user_id');
        $this->items_model->_table_name = "tbl_task_attachment";
        $this->items_model->_primary_key = "task_attachment_id";
        if (!empty($task_attachment_id)) {
            $id = $task_attachment_id;
            $this->items_model->save($data, $id);
            $msg = lang('leads_file_updated');
        } else {
            $id = $this->items_model->save($data);
            $msg = lang('leads_file_added');
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
                                "leads_id" => $data['leads_id'],
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
                                "leads_id" => $data['leads_id'],
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
            'module' => 'leads',
            'module_field_id' => $data['leads_id'],
            'activity' => 'activity_new_leads_attachment',
            'icon' => 'fa-rocket',
            'link' => 'admin/leads/leads_details/' . $data['leads_id'] . '/5',
            'value1' => $data['title'],
        );
        $this->items_model->_table_name = "tbl_activities";
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);
        $leads_info = $this->items_model->check_by(array('leads_id' => $data['leads_id']), 'tbl_leads');
        $notifiedUsers = array();
        if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
            $permissionUsers = json_decode($leads_info->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->items_model->allowad_user_id('55');
        }
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_uploaded_attachment',
                        'link' => 'admin/leads/leads_details/' . $leads_info->leads_id . '/5',
                        'value' => lang('lead') . ' ' . $leads_info->lead_name,
                    ));
                }
            }
            show_notification($notifiedUsers);
        }
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/leads/leads_details/' . $data['leads_id'] . '/' . '5');
    }

    public function new_attachment($id) {
        $data['dropzone'] = true;
        $data['leads_details'] = $this->items_model->check_by(array('leads_id' => $id), 'tbl_leads');
        $data['modal_subview'] = $this->load->view('admin/leads/new_attachment', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function attachment_details($type, $id) {
        $data['type'] = $type;
        $data['attachment_info'] = $this->items_model->check_by(array('task_attachment_id' => $id), 'tbl_task_attachment');
        $data['modal_subview'] = $this->load->view('admin/leads/attachment_details', $data, FALSE);
        $this->load->view('admin/_layout_modal_extra_lg', $data);
    }

    public function save_attachment_comments() {
        $task_attachment_id = $this->input->post('task_attachment_id', true);
        if (!empty($task_attachment_id)) {
            $data['task_attachment_id'] = $task_attachment_id;
        } else {
            $data['uploaded_files_id'] = $this->input->post('uploaded_files_id', true);
        }
        $data['leads_id'] = $this->input->post('leads_id', true);
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
                'module' => 'leads',
                'module_field_id' => $data['leads_id'],
                'activity' => 'activity_new_leads_comment',
                'icon' => 'fa-filter',
                'link' => 'admin/leads/leads_details/' . $data['leads_id'] . '/5',
                'value1' => $data['comment'],
            );
            $this->items_model->_table_name = "tbl_activities";
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);
            $notifiedUsers = array();
            $leads_info = $this->items_model->check_by(array('leads_id' => $data['leads_id']), 'tbl_leads');
            $notifiedUsers = array();
            if (!empty($leads_info->permission) && $leads_info->permission != 'all') {
                $permissionUsers = json_decode($leads_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->items_model->allowad_user_id('55');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_new_comment',
                            'link' => 'admin/leads/leads_details/' . $leads_info->leads_id . '/5',
                            'value' => lang('lead') . ' ' . $leads_info->lead_name,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
            $response_data = "";
            $view_data['comment_details'] = $this->db->where(array('task_comment_id' => $comment_id))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
            $response_data = $this->load->view("admin/leads/comments_list", $view_data, true);
            echo json_encode(array("status" => 'success', "data" => $response_data, 'message' => lang('leads_comment_save')));
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
            'module' => 'leads',
            'module_field_id' => $file_info->leads_id,
            'activity' => 'activity_leads_attachfile_deleted',
            'icon' => 'fa-rocket',
            'link' => 'admin/leads/leads_details/' . $file_info->leads_id . '/5',
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
        echo json_encode(array("status" => 'success', 'message' => lang('leads_attachfile_deleted')));
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
                    redirect('admin/leads');
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
                    redirect('admin/leads');
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
                redirect('admin/leads');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function delete_leads($id) {
        $can_delete = $this->items_model->can_action('tbl_leads', 'delete', array('leads_id' => $id));
        if (!empty($can_delete)) {
            $leads_info = $this->items_model->check_by(array('leads_id' => $id), 'tbl_leads');
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'leads',
                'module_field_id' => $id,
                'activity' => 'activity_leads_deleted',
                'icon' => 'fa-rocket',
                'value1' => $leads_info->lead_name
            );
            $this->items_model->_table_name = 'tbl_activities';
            $this->items_model->_primary_key = 'activities_id';
            $this->items_model->save($activity);
            $this->items_model->_table_name = "tbl_calls";
            $this->items_model->delete_multiple(array('leads_id' => $id));
            $this->items_model->_table_name = "tbl_mettings";
            $this->items_model->delete_multiple(array('leads_id' => $id));
            $all_comments_info = $this->db->where(array('leads_id' => $id))->get('tbl_task_comment')->result();
            if (!empty($all_comments_info)) {
                foreach ($all_comments_info as $comments_info) {
                    if (!empty($comments_info->comments_attachment)) {
                        $attachment = json_decode($comments_info->comments_attachment);
                        foreach ($attachment as $v_file) {
                            remove_files($v_file->fileName);
                        }
                    }
                }
                $this->items_model->_table_name = "tbl_task_comment";
                $this->items_model->delete_multiple(array('leads_id' => $id));
            }
            $this->items_model->_table_name = "tbl_task_attachment";
            $this->items_model->_order_by = "leads_id";
            $files_info = $this->items_model->get_by(array('leads_id' => $id), FALSE);
            if (!empty($files_info)) {
                foreach ($files_info as $v_files) {
                    $uploadFileinfo = $this->db->where('task_attachment_id', $v_files->task_attachment_id)->get('tbl_task_uploaded_files')->result();
                    if (!empty($uploadFileinfo)) {
                        foreach ($uploadFileinfo as $Fileinfo) {
                            remove_files($Fileinfo->file_name);
                        }
                    }
                    $this->items_model->_table_name = "tbl_task_uploaded_files";
                    $this->items_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                }
                $this->items_model->_table_name = "tbl_task_attachment";
                $this->items_model->delete_multiple(array('leads_id' => $id));
            }
            $leads_tasks = $this->db->where('leads_id', $id)->get('tbl_task')->result();
            if (!empty($leads_tasks)) {
                foreach ($leads_tasks as $v_taks) {
                    $all_comments_info = $this->db->where(array('task_id' => $v_taks->task_id))->get('tbl_task_comment')->result();
                    if (!empty($all_comments_info)) {
                        foreach ($all_comments_info as $comments_info) {
                            if (!empty($comments_info->comments_attachment)) {
                                $attachment = json_decode($comments_info->comments_attachment);
                                foreach ($attachment as $v_file) {
                                    remove_files($v_file->fileName);
                                }
                            }
                        }
                    }
                    $this->items_model->_table_name = "tbl_task_comment";
                    $this->items_model->delete_multiple(array('task_id' => $v_taks->task_id));
                    $this->items_model->_table_name = "tbl_task_attachment";
                    $this->items_model->_order_by = "task_id";
                    $files_info = $this->items_model->get_by(array('task_id' => $v_taks->task_id), FALSE);
                    if (!empty($files_info)) {
                        foreach ($files_info as $t_v_files) {
                            $uploadFileinfo = $this->db->where('task_attachment_id', $t_v_files->task_attachment_id)->get('tbl_task_uploaded_files')->result();
                            if (!empty($uploadFileinfo)) {
                                foreach ($uploadFileinfo as $Fileinfo) {
                                    remove_files($Fileinfo->file_name);
                                }
                            }
                            $this->items_model->_table_name = "tbl_task_uploaded_files";
                            $this->items_model->delete_multiple(array('task_attachment_id' => $t_v_files->task_attachment_id));
                        }
                    }
                    $this->items_model->_table_name = "tbl_task_attachment";
                    $this->items_model->delete_multiple(array('task_id' => $v_taks->task_id));
                    $this->items_model->_table_name = "tbl_tasks_timer";
                    $this->items_model->delete_multiple(array('task_id' => $v_taks->task_id));
                    $pin_info = $this->items_model->check_by(array('module_name' => 'tasks', 'module_id' => $v_taks->task_id), 'tbl_pinaction');
                    if (!empty($pin_info)) {
                        $this->items_model->_table_name = 'tbl_pinaction';
                        $this->items_model->delete_multiple(array('module_name' => 'tasks', 'module_id' => $v_taks->task_id));
                    }
                }
            }
            $this->items_model->_table_name = "tbl_task";
            $this->items_model->delete_multiple(array('leads_id' => $id));
            $proposal_info = $this->items_model->get_result(array('module' => 'leads', 'module_id' => $id), 'tbl_proposals');
            if (!empty($proposal_info)) {
                foreach ($proposal_info as $v_proposal) {
                    $this->items_model->_table_name = 'tbl_proposals_items';
                    $this->items_model->delete_multiple(array('proposals_id' => $v_proposal->proposals_id));

                    $this->items_model->_table_name = 'tbl_proposals';
                    $this->items_model->delete_multiple(array('proposals_id' => $v_proposal->proposals_id));
                }
            }
            $this->items_model->_table_name = 'tbl_reminders';
            $this->items_model->delete_multiple(array('module' => 'leads', 'module_id' => $id));
            $this->items_model->_table_name = 'tbl_pinaction';
            $this->items_model->delete_multiple(array('module_name' => 'leads', 'module_id' => $id));
            $this->items_model->_table_name = 'tbl_leads';
            $this->items_model->_primary_key = 'leads_id';
            $this->items_model->delete($id);
            $type = 'success';
            $message = lang('leads_deleted');
            echo json_encode(array("status" => $type, 'message' => $message));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('there_in_no_value')));
            exit();
        }
    }

    public function change_leads_status($lead_status_id) {
        $leads_id = $this->input->post('leads_id', true);
        foreach ($leads_id as $key => $id) {
            $data['index_no'] = $key + 1;
            $data['lead_status_id'] = $lead_status_id;
            $this->items_model->_table_name = 'tbl_leads';
            $this->items_model->_primary_key = 'leads_id';
            $this->items_model->save($data, $id);
        }
        $type = "success";
        $message = lang('update_leads');
        echo json_encode(array("status" => $type, "message" => $message));
        exit();
    }
}
