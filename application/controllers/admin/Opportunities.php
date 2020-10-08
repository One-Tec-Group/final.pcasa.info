<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Opportunities extends Admin_Controller {

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
        $data['title'] = lang('all_opportunities');
        $opportunities_state_reason = $this->db->get('tbl_opportunities_state_reason')->result();
        foreach ($opportunities_state_reason as $opportunities_state) {
            $data['all_state'][$opportunities_state->opportunities_state][] = $opportunities_state;
        }
        $data['assign_user'] = $this->items_model->allowad_user('56');
        if (!empty($id)) {
            $data['active'] = 2;
            $can_edit = $this->items_model->can_action('tbl_opportunities', 'edit', array('opportunities_id' => $id));
            if (!empty($can_edit)) {
                $data['opportunity_info'] = $this->items_model->check_by(array('opportunities_id' => $id), 'tbl_opportunities');
            }
        } else {
            $data['active'] = 1;
        }
        // $data['all_opportunity'] = $this->items_model->get_permission('tbl_opportunities');
        $status_info = $this->db->order_by('order_no', 'ASC')->get('tbl_lead_status')->result();
        if (!empty($status_info)) {
            foreach ($status_info as $v_status) {
                $data['status_info'][$v_status->lead_type][] = $v_status;
            }
        }
        $data['nationalities'] = $this->db->get('tbl_countries')->result();
        $data['assign_user'] = $this->items_model->allowad_user('55');
        $data['subview'] = $this->load->view('admin/opportunities/all_opportunities', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function opportunitiesList($type = null) {
        if ($this->input->is_ajax_request()) {
            $this->load->model('datatables');
            $this->datatables->table = 'tbl_opportunities';
            $custom_field = custom_form_table_search(8);
            $main_column = array('opportunity_name', 'stages', 'opportunities_state', 'expected_revenue', 'next_action', 'next_action_date', 'converted_by', 'permission');
            $action_array = array('opportunities_id');
            $result = array_merge($main_column, $custom_field, $action_array);
            $this->datatables->column_order = $result;
            $this->datatables->column_search = $result;
            $this->datatables->order = array('opportunities_id' => 'desc');
            $fetch_data = $this->datatables->get_datatable_permission();
            $data = array();
            $edited = can_action('56', 'edited');
            $deleted = can_action('56', 'deleted');
            foreach ($fetch_data as $_key => $v_opportunity) {
                $v_lead = $this->items_model->check_by(array('leads_id' => $v_opportunity->opportunity_lead_id), 'tbl_leads');
                $action = null;
                $can_edit = $this->items_model->can_action('tbl_opportunities', 'edit', array('opportunities_id' => $v_opportunity->opportunities_id));
                $can_delete = $this->items_model->can_action('tbl_opportunities', 'delete', array('opportunities_id' => $v_opportunity->opportunities_id));
                $opportunities_state_info = $this->db->where('opportunities_state_reason_id', $v_opportunity->opportunities_state_reason_id)->get('tbl_opportunities_state_reason')->row();
                if ($opportunities_state_info->opportunities_state == 'open') {
                    $label = 'primary';
                } elseif ($opportunities_state_info->opportunities_state == 'won') {
                    $label = 'success';
                } elseif ($opportunities_state_info->opportunities_state == 'suspended') {
                    $label = 'info';
                } else {
                    $label = 'danger';
                }
                $sub_array = array();
                $sub_array[] = $_key + 1;
                $name = null;
                $name .= '<a class="text-info" href="' . base_url() . 'admin/opportunities/opportunity_details/' . $v_opportunity->opportunities_id . '">' . $v_opportunity->opportunity_name . '</a>';
                if (strtotime($v_opportunity->close_date) < strtotime(date('Y-m-d')) && $v_opportunity->probability < 100) {
                    $name .= '<span class="label label-danger pull-right">' . lang('overdue') . '</span>';
                }
                $name .= '<div class="progress progress-xs progress-striped active">
                <div class="progress-bar progress-bar-' . (($v_opportunity->probability >= 100) ? 'success' : 'primary') . '" data-toggle="tooltip"
                data-original-title="' . lang("probability") . ' ' . $v_opportunity->probability . '%" style="width: ' . $v_opportunity->probability . '%"></div></div>';
                $sub_array[] = $name;
                $sub_array[] = lang($v_opportunity->stages);
                $change_status = null;
                $ch_url = base_url() . 'admin/opportunities/change_state/';
                $all_opportunities_state = $this->db->get('tbl_opportunities_state_reason')->result();
                $change_status .= '<div class="btn-group"><button class="btn btn-xs btn-' . $label . ' dropdown-toggle"data-toggle="dropdown" title="' . lang($opportunities_state_info->opportunities_state) . ' (' . $opportunities_state_info->opportunities_state_reason . ')">' . lang($opportunities_state_info->opportunities_state) . '<span class="caret"></span></button><ul class="dropdown-menu animated zoomIn">';
                if (!empty($all_opportunities_state)) {
                    foreach ($all_opportunities_state as $v_opportunities_state) {
                        $change_status .= '<li><a href="' . $ch_url . $v_opportunity->opportunities_id . '/' . $v_opportunities_state->opportunities_state_reason_id . '">' . lang($v_opportunities_state->opportunities_state) . ' (' . $v_opportunities_state->opportunities_state_reason . ')' . '</a></li>';
                    }
                }
                $change_status .= '</ul></div>';
                $sub_array[] = ((!empty($can_edit) && !empty($edited)) ? $change_status : '<span data-toggle="tooltip" data-placement="top" title="' . $opportunities_state_info->opportunities_state_reason . '" class="label label-' . $label . '">' . lang($opportunities_state_info->opportunities_state) . '</span>');
                $phoneInformation = "";
                $phonenumber = (($v_lead->mobile != NULL && $v_lead->mobile != "") ? $v_lead->mobile : (($v_lead->phone2 != NULL && $v_lead->phone2 != "") ? $v_lead->phone2 : (($v_lead->phone3 != NULL && $v_lead->phone3 != "") ? $v_lead->phone3 : (($v_lead->phone4 != NULL && $v_lead->phone4 != "") ? $v_lead->phone4 : "NO PHONE NUMBER FOUND"))));
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
                $lead_nationality = $this->db->where('id', $v_lead->nationality)->get('tbl_countries')->row();
                if (isset($lead_nationality) && $lead_nationality != NULL) {
                    $sub_array[] = '<img height="50px" title="' . $lead_nationality->nationality . '" tag="' . $lead_nationality->nationality . '" class="img-rounded" src="' . base_url("assets/img/flags/" . $lead_nationality->flag) . '">';
                } else {
                    $sub_array[] = "UNKNOWN";
                }
                $converted_by = null;
                $user_info = $this->db->where(array('user_id' => $v_opportunity->converted_by))->get('tbl_users')->row();
                if (!empty($user_info)) {
                    if ($user_info->role_id == 1) {
                        $label = 'circle-danger';
                    } else {
                        $label = 'circle-success';
                    }
                    $converted_by .= '<a href="#" data-toggle="tooltip" data-placement="top" title="' . fullname($user_info->user_id) . '">
                                      <img src="' . base_url() . staffImage($user_info->user_id) . '" class="img-circle img-xs" alt="">
                                <span style="margin: 0px 0 8px -10px;" class="circle ' . $label . '  circle-lg"></span></a>';
                }
                $sub_array[] = $converted_by;
                $assigned = null;
                if ($v_opportunity->permission != 'all') {
                    $get_permission = json_decode($v_opportunity->permission);
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
                $sub_array[] = $assigned;
                $custom_form_table = custom_form_table(8, $v_opportunity->opportunities_id);
                if (!empty($custom_form_table)) {
                    foreach ($custom_form_table as $c_label => $v_fields) {
                        $sub_array[] = $v_fields;
                    }
                }
                $action .= '<a href="' . base_url("admin/leads/leads_details/" . $v_opportunity->opportunity_lead_id) . '" class="btn-xs btn btn-info" title="View Lead"><i class="fa fa-eye"></i></a> ';
                $action .= '<a data-toggle="modal" data-target="#myModal" href="' . base_url("admin/opportunities/opportunityConvert/" . $v_opportunity->opportunity_lead_id . "/" . $v_opportunity->opportunities_id) . '" class="btn-xs btn btn-warning" title="Convert to client"><i class="fa fa-user"></i></a> ';
                if (!empty($can_edit) && !empty($edited)) {
                    $action .= btn_edit('admin/opportunities/index/' . $v_opportunity->opportunities_id) . ' ';
                }
                if (!empty($can_delete) && !empty($deleted)) {
                    $action .= ajax_anchor(base_url("admin/opportunities/delete_opportunity/$v_opportunity->opportunities_id"), "<i class='btn btn-xs btn-danger fa fa-trash-o'></i>", array("class" => "", "title" => lang('delete'), "data-fade-out-on-success" => "#table_" . $_key)) . ' ';
                }
                $sub_array[] = $action;
                $data[] = $sub_array;
            }
            render_table($data);
        } else {
            redirect('admin/dashboard');
        }
    }

    public function saved_opportunity($id = NULL) {
        $created = can_action('56', 'created');
        $edited = can_action('56', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $this->items_model->_table_name = 'tbl_opportunities';
            $this->items_model->_primary_key = 'opportunities_id';
            $data = $this->items_model->array_from_post(array('opportunity_lead_id', 'opportunity_name', 'stages', 'probability', 'close_date', 'opportunities_state_reason_id', 'expected_revenue', 'new_link', 'next_action', 'next_action_date', 'notes'));
            $data['converted_by'] = $this->session->userdata('user_id');
            $leadData = $this->items_model->array_from_post(array('salutaiton', 'lead_name', 'organization', 'lead_status_id', 'lead_category_id', 'lead_source_id', 'nationality', 'contact_name',
                        'email', 'email2', 'email3', 'phone', 'mobile', 'phone2', 'phone3', 'phone4', 'passport_number', 'passport_expire', 'address', 'city',
                        'state', 'country', 'skype', 'fax', 'date_of_birth', 'facebook', 'twitter'));
            $leadData['notes'] = $this->input->post("lead_notes");
            $leadWhere = array('client_id' => $leadData['client_id'], 'lead_name' => $leadData['lead_name']);
            $check_leads = $this->items_model->check_update('tbl_leads', $leadWhere, $data['opportunity_lead_id']);
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
                $leadData['permission'] = $assigned;
            } else {
                set_message('error', lang('assigned_to') . ' Field is required');
                if (empty($_SERVER['HTTP_REFERER'])) {
                    redirect('admin/opportunities');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
            $where = array('opportunity_name' => $data['opportunity_name']);
            if (!empty($id)) {
                $opportunities_id = array('opportunities_id !=' => $id);
            } else {
                $opportunities_id = null;
            }
            $check_opportunity = $this->items_model->check_update('tbl_opportunities', $where, $opportunities_id);
            if (!empty($check_opportunity)) {
                $type = 'error';
                $msg = "<strong style='color:#000'>" . $data['opportunity_name'] . '</strong>  ' . lang('already_exist');
            } else {
                $return_id = $this->items_model->save($data, $id);
                $this->items_model->_table_name = 'tbl_leads';
                $this->items_model->_primary_key = 'leads_id';
                $leadReturn_id = $this->items_model->save($leadData, $data['opportunity_lead_id']);
                if (!empty($id)) {
                    $id = $id;
                    $action = 'activity_update_opportunity_and_lead_data';
                    $msg = lang('update_opportunity');
                    $description = 'not_update_opportunity';
                } else {
                    if (!empty($return_id)) {
                        $this->items_model->_table_name = 'tbl_leads';
                        $this->items_model->_primary_key = 'leads_id';
                        $ldata = array(
                            "show_in" => "in_opportunities"
                        );
                        $this->items_model->save($ldata, $data['opportunity_lead_id']);
                        $this->items_model->_table_name = 'tbl_opportunities';
                        $this->items_model->_primary_key = 'opportunities_id';
                    }
                    $id = $return_id;
                    $action = 'activity_save_opportunity';
                    $description = 'not_save_opportunity';
                    $msg = lang('save_opportunity');
                }
                save_custom_field(8, $id);
                $activity = array(
                    'user' => $this->session->userdata('user_id'),
                    'module' => 'opportunities',
                    'module_field_id' => $id,
                    'activity' => $action,
                    'icon' => 'fa-filter',
                    'link' => 'admin/opportunities/opportunity_details/' . $id,
                    'value1' => $data['opportunity_name']
                );
                $this->items_model->_table_name = 'tbl_activities';
                $this->items_model->_primary_key = 'activities_id';
                $this->items_model->save($activity);
                $type = "success";
            }
            $message = $msg;
            set_message($type, $message);
            $opportunity_info = $this->items_model->check_by(array('opportunities_id' => $id), 'tbl_opportunities');
            $notifiedUsers = array();
            if (!empty($opportunity_info->permission) && $opportunity_info->permission != 'all') {
                $permissionUsers = json_decode($opportunity_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->items_model->allowad_user_id('56');
            }
            if (!empty($notifiedUsers) && !empty($opportunity_info)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => $description,
                            'link' => 'admin/opportunities/opportunity_details/' . $id,
                            'value' => lang('opportunity') . ' ' . $data['opportunity_name'],
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
        }
        if (!empty($id)) {
            redirect('admin/opportunities/opportunity_details/' . $id);
        } else {
            redirect('admin/opportunities');
        }
    }

    public function opportunities_state_reason() {
        $data['title'] = lang('opportunities_state_reason');
        $data['subview'] = $this->load->view('admin/opportunities/opportunities_state_reason', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function update_state_reason($id = NULL) {
        $created = can_action('129', 'created');
        $edited = can_action('129', 'edited');
        if (!empty($created) || !empty($edited) && !empty($id)) {
            $input_data = $this->items_model->array_from_post(array('opportunities_state', 'opportunities_state_reason'));
            $this->items_model->_table_name = 'tbl_opportunities_state_reason';
            $this->items_model->_primary_key = 'opportunities_state_reason_id';
            $id = $this->items_model->save($input_data, $id);
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'settings',
                'module_field_id' => $id,
                'activity' => ('activity_update_state_reason'),
                'value1' => $input_data['opportunities_state_reason'],
            );
            $this->items_model->_table_name = 'tbl_activities';
            $this->items_model->_primary_key = 'activities_id';
            $this->items_model->save($activity);
            $type = "success";
            $message = lang('update_state_reason_success');
        }
        if (!empty($id)) {
            $result = array(
                'id' => $id,
                'state' => $input_data['opportunities_state'],
                'reason' => $input_data['opportunities_state_reason'],
                'status' => $type,
                'message' => $message,
            );
        } else {
            $result = array();
        }
        echo json_encode($result);
        exit();
    }

    public function update_users($id) {
        $edited = can_action('56', 'edited');
        $can_edit = $this->items_model->can_action('tbl_opportunities', 'edit', array('opportunities_id' => $id));
        if (!empty($can_edit) && !empty($edited)) {
            $data['assign_user'] = $this->items_model->allowad_user('56');
            $data['opportunities_info'] = $this->items_model->check_by(array('opportunities_id' => $id), 'tbl_opportunities');
            $data['modal_subview'] = $this->load->view('admin/opportunities/_modal_users', $data, FALSE);
            $this->load->view('admin/_layout_modal', $data);
        } else {
            set_message('error', lang('there_in_no_value'));
            if (empty($_SERVER['HTTP_REFERER'])) {
                redirect('admin/opportunities');
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function update_member($id) {
        $can_edit = $this->items_model->can_action('tbl_opportunities', 'edit', array('opportunities_id' => $id));
        $edited = can_action('56', 'edited');
        if (!empty($can_edit) && !empty($edited)) {
            $opp_info = $this->items_model->check_by(array('opportunities_id' => $id), 'tbl_opportunities');
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
                    redirect('admin/opportunities');
                } else {
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
            $this->items_model->_table_name = "tbl_opportunities";
            $this->items_model->_primary_key = "opportunities_id";
            $this->items_model->save($data, $id);

            $msg = lang('update_opportunity');
            $activity = 'activity_update_opportunity';
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'opportunities',
                'module_field_id' => $id,
                'activity' => $activity,
                'icon' => 'fa-filter',
                'link' => 'admin/opportunities/opportunity_details/' . $id,
                'value1' => $opp_info->opportunity_name,
            );
            $this->items_model->_table_name = "tbl_activities";
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);
            $type = "success";
            $message = $msg;
            set_message($type, $message);
            $notifiedUsers = array();
            if (!empty($opp_info->permission) && $opp_info->permission != 'all') {
                $permissionUsers = json_decode($opp_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->items_model->allowad_user_id('56');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'assign_to_you_the_opportunity',
                            'link' => 'admin/opportunities/opportunity_details/' . $id,
                            'value' => lang('opportunity') . ' ' . $opp_info->opportunity_name,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
        } else {
            set_message('error', lang('there_in_no_value'));
        }
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/opportunities');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function change_state($id, $opportunities_state_reason_id) {
        $can_edit = $this->items_model->can_action('tbl_opportunities', 'edit', array('opportunities_id' => $id));
        $edited = can_action('56', 'edited');
        if (!empty($can_edit) && !empty($edited)) {
            $data['opportunities_state_reason_id'] = $opportunities_state_reason_id;
            $this->items_model->_table_name = 'tbl_opportunities';
            $this->items_model->_primary_key = 'opportunities_id';
            $this->items_model->save($data, $id);
            $opp_info = $this->items_model->check_by(array('opportunities_id' => $id), 'tbl_opportunities');
            $notifiedUsers = array();
            if (!empty($opp_info->permission) && $opp_info->permission != 'all') {
                $permissionUsers = json_decode($opp_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->items_model->allowad_user_id('56');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_changed_state',
                            'link' => 'admin/opportunities/opportunity_details/' . $opp_info->opportunities_id,
                            'value' => lang('opportunity') . ' ' . $opp_info->opportunity_name,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
            $type = "success";
            $message = lang('change_status');
            set_message($type, $message);
        } else {
            set_message('error', lang('there_in_no_value'));
        }
        if (empty($_SERVER['HTTP_REFERER'])) {
            redirect('admin/opportunities');
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function opportunity_details($id, $active = NULL, $op_id = NULL) {
        $data['title'] = lang('opportunity_details');
        $data['page_header'] = lang('task_management');
        $data['opportunity_details'] = $this->items_model->check_by(array('opportunities_id' => $id), 'tbl_opportunities');
        $this->items_model->_table_name = "tbl_task_attachment";
        $this->items_model->_order_by = "task_id";
        $data['files_info'] = $this->items_model->get_by(array('opportunities_id' => $id), FALSE);
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
            $data['task_active'] = 1;
            $data['bugs_active '] = 1;
        } elseif ($active == 3) {
            $data['active'] = 3;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active '] = 1;
        } elseif ($active == 4) {
            $data['active'] = 4;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active '] = 1;
        } elseif ($active == 5) {
            $data['active'] = 5;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active '] = 1;
        } elseif ($active == 'metting') {
            $data['active'] = 3;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 2;
            $data['task_active'] = 1;
            $data['bugs_active '] = 1;
            $data['mettings_info'] = $this->items_model->check_by(array('mettings_id' => $op_id), 'tbl_mettings');
        } elseif ($active == 'call') {
            $data['active'] = 2;
            $data['sub_active'] = 2;
            $data['call_info'] = $this->items_model->check_by(array('calls_id' => $op_id), 'tbl_calls');
            $data['sub_metting'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active '] = 1;
        } else {
            $data['active'] = 1;
            $data['sub_active'] = 1;
            $data['sub_metting'] = 1;
            $data['task_active'] = 1;
            $data['bugs_active '] = 1;
        }
        $data['subview'] = $this->load->view('admin/opportunities/opportunities_details', $data, TRUE);
        $this->load->view('admin/_layout_main', $data);
    }

    public function saved_call($opportunities_id, $id = NULL) {
        $data = $this->items_model->array_from_post(array('date', 'call_summary', 'client_id', 'user_id'));
        $data['opportunities_id'] = $opportunities_id;
        $this->items_model->_table_name = 'tbl_calls';
        $this->items_model->_primary_key = 'calls_id';
        $return_id = $this->items_model->save($data, $id);
        if (!empty($id)) {
            $id = $id;
            $action = 'activity_update_opportunity_call';
            $msg = lang('update_opportunity_call');
        } else {
            $id = $return_id;
            $action = 'activity_save_opportunity_call';
            $msg = lang('save_opportunity_call');
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'opportunities',
            'module_field_id' => $opportunities_id,
            'activity' => $action,
            'icon' => 'fa-filter',
            'link' => 'admin/opportunities/opportunity_details/' . $opportunities_id . '/2',
            'value1' => $data['call_summary']
        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);
        $opportunity_info = $this->items_model->check_by(array('opportunities_id' => $opportunities_id), 'tbl_opportunities');
        $notifiedUsers = array();
        if (!empty($opportunity_info->permission) && $opportunity_info->permission != 'all') {
            $permissionUsers = json_decode($opportunity_info->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->items_model->allowad_user_id('56');
        }
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_add_call',
                        'link' => 'admin/opportunities/opportunity_details/' . $opportunity_info->opportunities_id . '/2',
                        'value' => lang('opportunity') . ' ' . $opportunity_info->opportunity_name,
                    ));
                }
            }
            show_notification($notifiedUsers);
        }
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/opportunities/opportunity_details/' . $opportunities_id . '/' . '2');
    }

    public function delete_opportunity_call($opportunities_id, $id) {
        $calls_info = $this->items_model->check_by(array('calls_id' => $id), 'tbl_calls');
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'opportunities',
            'module_field_id' => $opportunities_id,
            'activity' => 'activity_opportunity_call_deleted',
            'icon' => 'fa-filter',
            'link' => 'admin/opportunities/opportunity_details/' . $opportunities_id . '/2',
            'value1' => $calls_info->call_summary
        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);
        $this->items_model->_table_name = 'tbl_calls';
        $this->items_model->_primary_key = 'calls_id';
        $this->items_model->delete($id);
        $type = 'success';
        $message = lang('opportunity_call_deleted');
        set_message($type, $message);
        redirect('admin/opportunities/opportunity_details/' . $opportunities_id . '/' . '2');
    }

    public function saved_metting($opportunities_id, $id = NULL) {
        $this->items_model->_table_name = 'tbl_mettings';
        $this->items_model->_primary_key = 'mettings_id';
        $data = $this->items_model->array_from_post(array('meeting_subject', 'user_id', 'location', 'description'));
        $data['start_date'] = strtotime($this->input->post('start_date', true) . ' ' . display_time($this->input->post('start_time', true)));
        $data['end_date'] = strtotime($this->input->post('end_date', true) . ' ' . display_time($this->input->post('end_time', true)));
        $data['opportunities_id'] = $opportunities_id;
        $user_id = serialize($this->items_model->array_from_post(array('attendees')));
        if (!empty($user_id)) {
            $data['attendees'] = $user_id;
        } else {
            $data['attendees'] = '-';
        }
        $return_id = $this->items_model->save($data, $id);

        if (!empty($id)) {
            $id = $id;
            $action = 'activity_update_opportunity_metting';
            $msg = lang('update_opportunity_metting');
        } else {
            $id = $return_id;
            $action = 'activity_save_opportunity_metting';
            $msg = lang('save_opportunity_metting');
        }
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'opportunities',
            'module_field_id' => $opportunities_id,
            'activity' => $action,
            'icon' => 'fa-filter',
            'link' => 'admin/opportunities/opportunity_details/' . $opportunities_id . '/3',
            'value1' => $data['meeting_subject']
        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);
        $opportunity_info = $this->items_model->check_by(array('opportunities_id' => $opportunities_id), 'tbl_opportunities');
        $notifiedUsers = array();
        if (!empty($opportunity_info->permission) && $opportunity_info->permission != 'all') {
            $permissionUsers = json_decode($opportunity_info->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->items_model->allowad_user_id('56');
        }
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_add_meetings',
                        'link' => 'admin/opportunities/opportunity_details/' . $opportunity_info->opportunities_id . '/3',
                        'value' => lang('opportunity') . ' ' . $opportunity_info->opportunity_name,
                    ));
                }
            }
            show_notification($notifiedUsers);
        }
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/opportunities/opportunity_details/' . $opportunities_id . '/' . '3');
    }

    public function delete_opportunity_mettings($opportunities_id, $id) {
        $mettings_info = $this->items_model->check_by(array('mettings_id' => $id), 'tbl_mettings');
        $activity = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'opportunities',
            'module_field_id' => $opportunities_id,
            'activity' => 'activity_meeting_deleted',
            'icon' => 'fa-filter',
            'link' => 'admin/opportunities/opportunity_details/' . $opportunities_id . '/3',
            'value1' => $mettings_info->meeting_subject
        );
        $this->items_model->_table_name = 'tbl_activities';
        $this->items_model->_primary_key = 'activities_id';
        $this->items_model->save($activity);
        $this->items_model->_table_name = 'tbl_mettings';
        $this->items_model->_primary_key = 'mettings_id';
        $this->items_model->delete($id);
        $type = 'success';
        $message = lang('mettings_deleted');
        set_message($type, $message);
        redirect('admin/opportunities/opportunity_details/' . $opportunities_id . '/' . '3');
    }

    public function save_comments() {
        $data['opportunities_id'] = $this->input->post('opportunities_id', TRUE);
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
                'module' => 'opportunities',
                'module_field_id' => $data['opportunities_id'],
                'activity' => 'activity_new_opportunity_comment',
                'icon' => 'fa-filter',
                'link' => 'admin/opportunities/opportunity_details/' . $data['opportunities_id'] . '/4',
                'value1' => $data['comment'],
            );
            $this->items_model->_table_name = "tbl_activities";
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);
            $opportunity_info = $this->items_model->check_by(array('opportunities_id' => $data['opportunities_id']), 'tbl_opportunities');
            $notifiedUsers = array();
            if (!empty($opportunity_info->permission) && $opportunity_info->permission != 'all') {
                $permissionUsers = json_decode($opportunity_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->items_model->allowad_user_id('56');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_new_comment',
                            'link' => 'admin/opportunities/opportunity_details/' . $opportunity_info->opportunities_id . '/4',
                            'value' => lang('opportunity') . ' ' . $opportunity_info->opportunity_name,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
            $response_data = "";
            $view_data['comment_details'] = $this->db->where(array('task_comment_id' => $comment_id))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
            $response_data = $this->load->view("admin/opportunities/comments_list", $view_data, true);
            echo json_encode(array("status" => 'success', "data" => $response_data, 'message' => lang('opportunity_comment_save')));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            exit();
        }
    }

    public function save_comments_reply($task_comment_id) {
        $data['opportunities_id'] = $this->input->post('opportunities_id', TRUE);
        $data['comment'] = $this->input->post('reply_comments', TRUE);
        $data['user_id'] = $this->session->userdata('user_id');
        $data['comments_reply_id'] = $task_comment_id;
        $this->items_model->_table_name = "tbl_task_comment";
        $this->items_model->_primary_key = "task_comment_id";
        $comment_id = $this->items_model->save($data);
        if (!empty($comment_id)) {
            $opportunity_info = $this->items_model->check_by(array('opportunities_id' => $data['opportunities_id']), 'tbl_opportunities');
            $comments_info = $this->items_model->check_by(array('task_comment_id' => $task_comment_id), 'tbl_task_comment');
            $user = $this->items_model->check_by(array('user_id' => $comments_info->user_id), 'tbl_users');
            if ($user->role_id == 2) {
                $url = 'client/';
            } else {
                $url = 'admin/';
            }
            $activities = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'opportunities',
                'module_field_id' => $data['opportunities_id'],
                'activity' => 'activity_new_comment_reply',
                'icon' => 'fa-filter',
                'link' => $url . 'opportunities/opportunity_details/' . $data['opportunities_id'] . '/4',
                'value1' => $this->db->where('task_comment_id', $task_comment_id)->get('tbl_task_comment')->row()->comment,
                'value2' => $data['comment'],
            );
            $this->items_model->_table_name = "tbl_activities";
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);
            $notifiedUsers = array($comments_info->user_id);
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_new_comment',
                            'link' => $url . 'opportunities/opportunity_details/' . $opportunity_info->opportunities_id . '/4',
                            'value' => lang('opportunity') . ' ' . $opportunity_info->opportunity_name,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
            $response_data = "";
            $view_data['comment_reply_details'] = $this->db->where(array('task_comment_id' => $comment_id))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
            $response_data = $this->load->view("admin/opportunities/comments_reply", $view_data, true);
            echo json_encode(array("status" => 'success', "data" => $response_data, 'message' => lang('opportunity_comment_save')));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            exit();
        }
    }

    public function delete_comments($task_comment_id) {
        $comments_info = $this->items_model->check_by(array('task_comment_id' => $task_comment_id), 'tbl_task_comment');
        if (!empty($comments_info->comments_attachment)) {
            $attachment = json_decode($comments_info->comments_attachment);
            foreach ($attachment as $v_file) {
                remove_files($v_file->fileName);
            }
        }
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'opportunities',
            'module_field_id' => $comments_info->opportunities_id,
            'activity' => 'activity_comment_deleted',
            'icon' => 'fa-filter',
            'link' => 'admin/opportunities/opportunity_details/' . $comments_info->opportunities_id . '/' . '4',
            'value1' => $comments_info->comment,
        );
        $this->items_model->_table_name = "tbl_activities";
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);
        $this->items_model->_table_name = "tbl_task_comment";
        $this->items_model->_primary_key = "task_comment_id";
        $this->items_model->delete($task_comment_id);
        $this->items_model->_table_name = "tbl_task_comment";
        $this->items_model->delete_multiple(array('comments_reply_id' => $task_comment_id));
        echo json_encode(array("status" => 'success', 'message' => lang('task_comment_deleted')));
        exit();
    }

    public function new_attachment($id) {
        $data['dropzone'] = true;
        $data['opportunity_details'] = $this->items_model->check_by(array('opportunities_id' => $id), 'tbl_opportunities');
        $data['modal_subview'] = $this->load->view('admin/opportunities/new_attachment', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function attachment_details($type, $id) {
        $data['type'] = $type;
        $data['attachment_info'] = $this->items_model->check_by(array('task_attachment_id' => $id), 'tbl_task_attachment');
        $data['modal_subview'] = $this->load->view('admin/opportunities/attachment_details', $data, FALSE);
        $this->load->view('admin/_layout_modal_extra_lg', $data);
    }

    public function save_attachment($task_attachment_id = NULL) {
        $data = $this->items_model->array_from_post(array('title', 'description', 'opportunities_id'));
        $data['user_id'] = $this->session->userdata('user_id');
        $this->items_model->_table_name = "tbl_task_attachment";
        $this->items_model->_primary_key = "task_attachment_id";
        if (!empty($task_attachment_id)) {
            $id = $task_attachment_id;
            $this->items_model->save($data, $id);
            $msg = lang('opportunity_file_updated');
        } else {
            $id = $this->items_model->save($data);
            $msg = lang('opportunity_file_added');
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
                                "opportunities_id" => $data['opportunities_id'],
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
                                "opportunities_id" => $data['opportunities_id'],
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
            'module' => 'opportunities',
            'module_field_id' => $id,
            'activity' => 'activity_new_opportunity_attachment',
            'icon' => 'fa-filter',
            'link' => 'admin/opportunities/opportunity_details/' . $data['opportunities_id'] . '/5',
            'value1' => $data['title'],
        );
        $this->items_model->_table_name = "tbl_activities";
        $this->items_model->_primary_key = "activities_id";
        $this->items_model->save($activities);
        $opportunity_info = $this->items_model->check_by(array('opportunities_id' => $data['opportunities_id']), 'tbl_opportunities');
        $notifiedUsers = array();
        if (!empty($opportunity_info->permission) && $opportunity_info->permission != 'all') {
            $permissionUsers = json_decode($opportunity_info->permission);
            foreach ($permissionUsers as $user => $v_permission) {
                array_push($notifiedUsers, $user);
            }
        } else {
            $notifiedUsers = $this->items_model->allowad_user_id('56');
        }
        if (!empty($notifiedUsers)) {
            foreach ($notifiedUsers as $users) {
                if ($users != $this->session->userdata('user_id')) {
                    add_notification(array(
                        'to_user_id' => $users,
                        'from_user_id' => true,
                        'description' => 'not_uploaded_attachment',
                        'link' => 'admin/opportunities/opportunity_details/' . $opportunity_info->opportunities_id . '/5',
                        'value' => lang('opportunity') . ' ' . $opportunity_info->opportunity_name,
                    ));
                }
            }
            show_notification($notifiedUsers);
        }
        $type = "success";
        $message = $msg;
        set_message($type, $message);
        redirect('admin/opportunities/opportunity_details/' . $data['opportunities_id'] . '/' . '5');
    }

    public function save_attachment_comments() {
        $task_attachment_id = $this->input->post('task_attachment_id', true);
        if (!empty($task_attachment_id)) {
            $data['task_attachment_id'] = $task_attachment_id;
        } else {
            $data['uploaded_files_id'] = $this->input->post('uploaded_files_id', true);
        }
        $data['opportunities_id'] = $this->input->post('opportunities_id', true);
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
                'module' => 'opportunities',
                'module_field_id' => $data['opportunities_id'],
                'activity' => 'activity_new_opportunity_comment',
                'icon' => 'fa-filter',
                'link' => 'admin/opportunities/opportunity_details/' . $data['opportunities_id'] . '/5',
                'value1' => $data['comment'],
            );
            $this->items_model->_table_name = "tbl_activities";
            $this->items_model->_primary_key = "activities_id";
            $this->items_model->save($activities);
            $notifiedUsers = array();
            $opportunity_info = $this->items_model->check_by(array('opportunities_id' => $data['opportunities_id']), 'tbl_opportunities');
            $notifiedUsers = array();
            if (!empty($opportunity_info->permission) && $opportunity_info->permission != 'all') {
                $permissionUsers = json_decode($opportunity_info->permission);
                foreach ($permissionUsers as $user => $v_permission) {
                    array_push($notifiedUsers, $user);
                }
            } else {
                $notifiedUsers = $this->items_model->allowad_user_id('56');
            }
            if (!empty($notifiedUsers)) {
                foreach ($notifiedUsers as $users) {
                    if ($users != $this->session->userdata('user_id')) {
                        add_notification(array(
                            'to_user_id' => $users,
                            'from_user_id' => true,
                            'description' => 'not_new_comment',
                            'link' => 'admin/opportunities/opportunity_details/' . $data['opportunities_id'] . '/5',
                            'value' => lang('opportunity') . ' ' . $opportunity_info->opportunity_name,
                        ));
                    }
                }
                show_notification($notifiedUsers);
            }
            $response_data = "";
            $view_data['comment_details'] = $this->db->where(array('task_comment_id' => $comment_id))->order_by('comment_datetime', 'DESC')->get('tbl_task_comment')->result();
            $response_data = $this->load->view("admin/opportunities/comments_list", $view_data, true);
            echo json_encode(array("status" => 'success', "data" => $response_data, 'message' => lang('opportunity_comment_save')));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            exit();
        }
    }

    public function download_files($uploaded_files_id, $comments = null) {
        $this->load->helper('download');
        if (!empty($comments)) {
            if ($uploaded_files_id) {
                $down_data = file_get_contents('uploads/' . $uploaded_files_id);
                force_download($uploaded_files_id, $down_data);
            } else {
                $type = "error";
                $message = lang('operation_failed');
                set_message($type, $message);
                if (empty($_SERVER['HTTP_REFERER'])) {
                    redirect('admin/opportunities');
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
                    redirect('admin/opportunities');
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
            redirect('admin/opportunities/opportunity_details/' . $attachment_info->opportunities_id . '/5');
        }
    }

    public function delete_files($task_attachment_id) {
        $file_info = $this->items_model->check_by(array('task_attachment_id' => $task_attachment_id), 'tbl_task_attachment');
        $activities = array(
            'user' => $this->session->userdata('user_id'),
            'module' => 'opportunities',
            'module_field_id' => $file_info->opportunities_id,
            'activity' => 'activity_opportunity_attachfile_deleted',
            'icon' => 'fa-filter',
            'link' => 'admin/opportunities/opportunity_details/' . $file_info->opportunities_id . '/5',
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
        echo json_encode(array("status" => 'success', 'message' => lang('opportunity_attachfile_deleted')));
        exit();
    }

    public function delete_opportunity($id) {
        $can_delete = $this->items_model->can_action('tbl_opportunities', 'delete', array('opportunities_id' => $id));
        $deleted = can_action('56', 'deleted');
        if (!empty($can_delete) && !empty($deleted)) {
            $opportunity_info = $this->items_model->check_by(array('opportunities_id' => $id), 'tbl_opportunities');
            $activity = array(
                'user' => $this->session->userdata('user_id'),
                'module' => 'opportunities',
                'module_field_id' => $id,
                'activity' => 'activity_opportunity_deleted',
                'icon' => 'fa-filter',
                'value1' => $opportunity_info->opportunity_name
            );
            $this->items_model->_table_name = 'tbl_activities';
            $this->items_model->_primary_key = 'activities_id';
            $this->items_model->save($activity);
            $this->items_model->_table_name = "tbl_calls";
            $this->items_model->delete_multiple(array('opportunities_id' => $id));
            $this->items_model->_table_name = "tbl_mettings";
            $this->items_model->delete_multiple(array('opportunities_id' => $id));
            $all_comments_info = $this->db->where(array('opportunities_id' => $id))->get('tbl_task_comment')->result();
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
            $this->items_model->delete_multiple(array('opportunities_id' => $id));
            $this->items_model->_table_name = "tbl_task_attachment";
            $this->items_model->_order_by = "task_id";
            $files_info = $this->items_model->get_by(array('opportunities_id' => $id), FALSE);
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
            $this->items_model->delete_multiple(array('opportunities_id' => $id));
            $opportunity_tasks = $this->db->where('opportunities_id', $id)->get('tbl_task')->result();
            if (!empty($opportunity_tasks)) {
                foreach ($opportunity_tasks as $v_taks) {
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
            $this->items_model->delete_multiple(array('opportunities_id' => $id));
            $opportunity_bugs = $this->db->where('opportunities_id', $id)->get('tbl_bug')->result();
            if (!empty($opportunity_bugs)) {
                foreach ($opportunity_bugs as $v_opportunity) {
                    $all_comments_info = $this->db->where(array('bug_id' => $v_opportunity->bug_id))->get('tbl_task_comment')->result();
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
                    $this->bugs_model->_table_name = "tbl_task_comment";
                    $this->bugs_model->delete_multiple(array('bug_id' => $v_opportunity->bug_id));
                    $this->bugs_model->_table_name = "tbl_task_attachment";
                    $this->bugs_model->_order_by = "bug_id";
                    $files_info = $this->bugs_model->get_by(array('bug_id' => $v_opportunity->bug_id), FALSE);
                    foreach ($files_info as $b_v_files) {
                        $uploadFileinfo = $this->db->where('task_attachment_id', $b_v_files->task_attachment_id)->get('tbl_task_uploaded_files')->result();
                        if (!empty($uploadFileinfo)) {
                            foreach ($uploadFileinfo as $Fileinfo) {
                                remove_files($Fileinfo->file_name);
                            }
                        }
                        $this->bugs_model->_table_name = "tbl_task_uploaded_files";
                        $this->bugs_model->delete_multiple(array('task_attachment_id' => $b_v_files->task_attachment_id));
                    }
                    $this->bugs_model->_table_name = "tbl_task_attachment";
                    $this->bugs_model->delete_multiple(array('bug_id' => $v_opportunity->bug_id));
                    $this->bugs_model->_table_name = 'tbl_pinaction';
                    $this->bugs_model->delete_multiple(array('module_name' => 'bugs', 'module_id' => $v_opportunity->bug_id));
                    $bug_tasks = $this->db->where('bug_id', $v_opportunity->bug_id)->get('tbl_task')->result();
                    if (!empty($bug_tasks)) {
                        foreach ($bug_tasks as $tasks_bugs) {
                            $all_comments_info = $this->db->where(array('task_id' => $tasks_bugs->task_id))->get('tbl_task_comment')->result();
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
                            $this->items_model->delete_multiple(array('task_id' => $tasks_bugs->task_id));
                            $this->items_model->_table_name = "tbl_task_attachment";
                            $this->items_model->_order_by = "task_id";
                            $files_info = $this->items_model->get_by(array('task_id' => $tasks_bugs->task_id), FALSE);
                            if (!empty($files_info)) {
                                foreach ($files_info as $t_v_files) {
                                    $uploadFileinfo = $this->db->where('task_attachment_id', $t_v_files->task_attachment_id)->get('tbl_task_uploaded_files')->result();
                                    if (!empty($uploadFileinfo)) {
                                        foreach ($uploadFileinfo as $Fileinfo) {
                                            remove_files($Fileinfo->file_name);
                                        }
                                    }
                                    $this->items_model->_table_name = "tbl_task_uploaded_files";
                                    $this->items_model->delete_multiple(array('task_attachment_id' => $v_files->task_attachment_id));
                                }
                            }
                            $this->items_model->_table_name = "tbl_task_attachment";
                            $this->items_model->delete_multiple(array('task_id' => $tasks_bugs->task_id));
                            $pin_info = $this->items_model->check_by(array('module_name' => 'tasks', 'module_id' => $tasks_bugs->task_id), 'tbl_pinaction');
                            if (!empty($pin_info)) {
                                $this->items_model->_table_name = 'tbl_pinaction';
                                $this->items_model->delete_multiple(array('module_name' => 'tasks', 'module_id' => $tasks_bugs->task_id));
                            }
                        }
                    }
                    $this->items_model->_table_name = "tbl_task";
                    $this->items_model->delete_multiple(array('bug_id' => $v_opportunity->bug_id));
                }
            }
            $this->items_model->_table_name = "tbl_bug";
            $this->items_model->delete_multiple(array('opportunities_id' => $id));
            $this->items_model->_table_name = 'tbl_opportunities';
            $this->items_model->_primary_key = 'opportunities_id';
            $this->items_model->delete($id);
            $type = 'success';
            $message = lang('opportunity_deleted');
            echo json_encode(array("status" => $type, 'message' => $message));
            exit();
        } else {
            echo json_encode(array("status" => 'error', 'message' => lang('error_occurred')));
            exit();
        }
    }

    public function leadConvert() {
        $opportunities_state_reason = $this->db->get('tbl_opportunities_state_reason')->result();
        foreach ($opportunities_state_reason as $opportunities_state) {
            $ddata['all_state'][$opportunities_state->opportunities_state][] = $opportunities_state;
        }
        $ddata['assign_user'] = $this->items_model->allowad_user('56');
        $ddata['lead_details'] = $this->items_model->check_by(array('leads_id' => $this->uri->segment(4)), 'tbl_leads');
        $data['subview'] = $this->load->view('admin/opportunities/_modal_conver_lead_opprtunity', $ddata, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function opportunityConvert($id) {
        $data['title'] = lang('convert_to_client');
        $data['person'] = 1;
        $this->items_model->_table_name = "tbl_countries";
        $this->items_model->_order_by = "id";
        $data['countries'] = $this->items_model->get();
        $this->items_model->_table_name = 'tbl_currencies';
        $this->items_model->_order_by = 'name';
        $data['currencies'] = $this->items_model->get();
        $data['languages'] = $this->db->where('active', 1)->order_by('name', 'ASC')->get('tbl_languages')->result();
        $data['leads_info'] = $this->items_model->check_by(array('leads_id' => $id), 'tbl_leads');
        $data['opportunity_info'] = $this->items_model->check_by(array('opportunities_id' => $this->uri->segment(5)), 'tbl_opportunities');
        $data['modal_subview'] = $this->load->view('admin/leads/_modal_convert', $data, FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

}
