<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Smart_import extends Admin_Controller
{

    public function __construct()
    {
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

        $this->tableContent = array('DEVELOPER', 'COMMUNITY', 'SUB COMMUNITY', 'PROPERTY NUMBER', 'PROPERTY TYPE', "PROPERTY PURPOSE", "PROPERTY REFERENCE", 'PROPERTY SIZE (SQFT)', 'PROPERTY SIZE (SQM)', 'BEDROOM/S', 'BATHROOM/S', 'PARKING', 'OTHERS',
            'SALUTATION', 'FULL NAME', 'PARTNER NAME', 'E-MAIL', 'E-MAIL 2', 'E-MAIL 3', 'PHONE NUMBER 1', 'PHONE NUMBER 2', 'PHONE NUMBER 3',
            'PHONE NUMBER 4', 'SKYPE ID', 'LANDLINE', 'FAX', 'ADDRESS', 'P.O BOX', 'NATIONALITY', 'DATE OF BIRTH', 'PASSPORT NUMBER', 'PASSPORT EXPIRATION DATE');

        $this->dbFieldsLeads = array("SALUTATION" => 'salutaiton', "FULL NAME" => 'lead_name',
            "PARTNER NAME" => 'partner_name', "E-MAIL" => 'email', "E-MAIL 2" => 'email2', "E-MAIL 3" => 'email3', "PHONE NUMBER 1" => 'mobile', "PHONE NUMBER 2" => 'phone2', "PHONE NUMBER 3" => 'phone3',
            "PHONE NUMBER 4" => 'phone4', "SKYPE ID" => 'skype', "LANDLINE" => 'phone', "FAX" => 'fax', "ADDRESS" => 'address', "P.O BOX" => 'po_box', "NATIONALITY" => 'nationality',
            "DATE OF BIRTH" => 'date_of_birth', "PASSPORT NUMBER" => 'passport_number', "PASSPORT EXPIRATION DATE" => 'passport_expire');

        $this->dbFieldsProp = array('DEVELOPER' => "prop_developer", 'PROPERTY REFERENCE' => "prop_custom_reference", 'COMMUNITY' => "prop_community", 'SUB COMMUNITY' => "prop_sub_community", 'PROPERTY NUMBER' => "prop_property_number",
            'PROPERTY TYPE' => "property_type_id", 'PROPERTY PURPOSE' => "prop_purpose", 'PROPERTY SIZE (SQFT)' => "prop_size_sqft", 'PROPERTY SIZE (SQM)' => "prop_size_sqm", 'BEDROOM/S' => "prop_bedrooms", 'BATHROOM/S' => "prop_bathrooms", 'PARKING' => "prop_parking", 'OTHERS' => "prop_others");
    }

    public function index() {
        $data['title'] = config_item('company_name') . ' | ' . lang('smart_bulk_import');

        if ($this->input->post()) {
            if ($this->input->post("leads_import") == "true") {
                $this->save_imported();
            } else if ($this->input->post("download_sample") == "true") {
                $this->downloadSample();
            }
        } else {
          // get all leads status
          $status_info = $this->db->order_by('order_no', 'ASC')->get('tbl_lead_status')->result();
          if (!empty($status_info)) {
              foreach ($status_info as $v_status) {
                  $data['status_info'][$v_status->lead_type][] = $v_status;
              }
          }
          // get all Nationalities
          $data['nationalities'] = $this->db->get('tbl_countries')->result();

          $data['assign_user'] = $this->items_model->allowad_user('55');

          $data['subview'] = $this->load->view('admin/smart_import/smart_import', $data, TRUE);
          $this->load->view('admin/_layout_main', $data); //page load
        }
    }

    protected function downloadSample() {
        redirect(base_url("assets/sample/smart_import_sample.xls"));
        exit();
    }

    protected function save_imported() {
        //load the excel library
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

                $propertyExists = 0;
                $propertyInserted = 0;
                $properFailed = 0;
                $properWithExistLead = 0;
                $leadsExists = 0;
                $leadsInserted = 0;
                $leadsFailed = 0;
                $propertyNumberOnly = array("prop_size_sqft", "prop_size_sqm", "prop_bedrooms", "prop_bathrooms");
                //All data from excel
                $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

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

                for ($x = 2; $x <= count($sheetData); $x++) {
                  $leadData = $this->items_model->array_from_post(array('lead_status_id', 'lead_source_id', 'lead_category_id'));
                  foreach($sheetData[$x] as $key => $value){
                    $Ddata = $sheetData[1][$key];
                    if (array_key_exists($sheetData[1][$key], $this->dbFieldsLeads)) {
                      $leadData[$this->dbFieldsLeads["$Ddata"]] = (($sheetData[$x]["$key"] != NULL && $sheetData[$x]["$key"] != "") ? $sheetData[$x]["$key"] : "");
                    }else if (array_key_exists($sheetData[1][$key], $this->dbFieldsProp)) {
                      $propertyData[$this->dbFieldsProp["$Ddata"]] = (($sheetData[$x]["$key"] != NULL && $sheetData[$x]["$key"] != "") ? $sheetData[$x]["$key"] : "");
                    }
                  }
                  $leadData['date_assigned'] = date("Y-m-d");
                  $leadData['created_time'] = date("Y-m-d");
                  $nationality = $this->db->where("`nationality` = '" . $leadData['nationality'] . "' OR `id` = '" . $leadData['nationality'] . "';")->get("tbl_countries")->row();
                  $leadData['nationality'] = (($nationality != NULL) ? $nationality->id : "UNKNOWN");
                  $leadData['permission'] = $assigned;

                  $leadCheck = $this->db->where("`lead_name` = " . '"' . $leadData['lead_name'] . '"' . " AND (`email` = " . '"' . $leadData['email'] . '"' . " OR `mobile` = " . '"' . $leadData['mobile'] . '"' . " OR `passport_number` = " . '"' . $leadData['passport_number'] . '"' . ")")->get("tbl_leads")->row();
                    if ($leadCheck != NULL) {
                        $leadsExists++;
                        $properWithExistLead++;
                        $ownerID = $leadCheck->leads_id;
                    } else {
                    // save to tbl_leads
                    $this->items_model->_table_name = 'tbl_leads';
                    $this->items_model->_primary_key = 'leads_id';
                    $ownerID = $this->items_model->save($leadData);
                    }

                    for($ni = 0; $ni < count($propertyNumberOnly); $ni++){
                      preg_match_all('!\d+!', $propertyData["$propertyNumberOnly[$ni]"], $matches);
                        if (isset($matches[0][0]) && $matches[0][0] != NULL && $matches[0][0] != "") {
                            $propertyData["$propertyNumberOnly[$ni]"] = $matches[0][0];
                        } else {
                            $propertyData["$propertyNumberOnly[$ni]"] = 0;
                        }
                    }
                    $types = $this->db->where("`property_type` = '" . $propertyData['property_type_id'] . "' OR `property_type_id` = '" . $propertyData['property_type_id'] . "';")->get("tbl_properties_types")->row();
                    $propertyData['property_type_id'] = (($types != NULL) ? $types->id : "UNKNOWN");
                    $propertyData['prop_reference_number'] = "NA-" . strtoupper(substr($propertyData['property_type_id'], 0, 2)) . "-" . strtoupper(substr($this->session->userdata("name"), 0, 2)) . "-" . rand(100000, 999999);
                    $propertyData['prop_owner_id'] = $ownerID;
                    $propertyData['prop_status'] = "UNKNOWN";
                    $propertyData['property_source_id'] = $this->input->post("property_source_id");
                    $propertyData['prop_others'] = "FALSE-FALSE-FALSE-FALSE-FALSE-FALSE";
                    $propertyData['prop_created_time'] = date("Y-m-d");
                    $propertyData['prop_permission'] = $assigned;
                    if (strtoupper($propertyData['prop_purpose']) != "SALE" && strtoupper($propertyData['prop_purpose']) != "RENT") {
                        $propertyData['prop_purpose'] = "UNKNOWN";
                    }
                    $propertyEx = $this->proeprtyExists($propertyData);
                    if ($propertyEx[0] >= 2) {
                        $propertyExists++;
                    } else {
                        $propertyData['prop_purpose'] = $propertyEx[1];
                        // save to tbl_leads
                        $this->items_model->_table_name = 'tbl_properties';
                        $this->items_model->_primary_key = 'prop_id';
                        if ($this->items_model->save($propertyData) != NULL) {
                            $propertyInserted++;
                        } else {
                            $properFailed++;
                        }
                    }
                }
                if ($properFailed > 0 && $propertyInserted == 0 && $propertyExists == 0 && $leadsFailed > 0 && $leadsInserted == 0 && $leadsExists == 0) {
                    $type = 'error';
                  } else if ($properFailed == 0 && $propertyInserted > 0 && $propertyExists == 0 && $leadsFailed == 0 && $leadsInserted > 0 && $leadsExists == 0) {
                    $type = 'success';
                  } else {
                    $type = 'warning';
                  }
                  $message = "LEADS IMPORTED:(Successfully Imported => <b>" . $leadsInserted . "</b> | Already Exists => <b>" . $leadsExists . "</b> | Failed => <b>" . $leadsFailed . "</b>)"
                          . " | PROPERTIES IMPORTED:( Successfully Imported => <b>" . $propertyInserted . "</b> | Already Exists => <b>" . $propertyExists . "</b> | Failed => <b>" . $properFailed . "</b>"
                          . " | Property with exist lead => <b>" . $properWithExistLead . "</b>)";
            } else {
                $type = 'error';
                $message = "Sorry your uploaded file type not allowed!, Please upload XLS/CSV File ";
            }
        } else {
            $type = 'error';
            $message = "You did not Select File! Please upload XLS/CSV File ";
        }
        set_message($type, $message);
        redirect('admin/smart_import');
    }

    protected function proeprtyExists($propertyInsertData) {
        $sale = 0;
        $rent = 0;
        $unknown = 0;
        $propertyNo = $propertyInsertData['prop_property_number'];
        $propertyComm = $propertyInsertData['prop_community'];
        $propertySComm = $propertyInsertData['prop_sub_community'];
        $sale = $this->db->where("`prop_property_number` = '" . $propertyNo . "' AND `prop_community` = '" . $propertyComm . "' AND `prop_sub_community` = '" . $propertySComm . "' AND `prop_purpose` = 'SALE'")->get("tbl_properties")->num_rows();
        $rent = $this->db->where("`prop_property_number` = '" . $propertyNo . "' AND `prop_community` = '" . $propertyComm . "' AND `prop_sub_community` = '" . $propertySComm . "' AND `prop_purpose` = 'RENT'")->get("tbl_properties")->num_rows();
        $unknown = $this->db->where("`prop_property_number` = '" . $propertyNo . "' AND `prop_community` = '" . $propertyComm . "' AND `prop_sub_community` = '" . $propertySComm . "' AND `prop_purpose` = 'UNKNOWN'")->get("tbl_properties")->num_rows();
        if (($sale + $rent + $unknown) >= 2) {
            return array(5, "UNKNOWN");
        } else {
            if ($sale == 0 && $rent == 0 && $unknown >= 1 && $propertyInsertData['prop_purpose'] == "UNKNOWN") {
                return array(5, "UNKNOWN");
            } else if ($sale == 0 && $rent >= 1 && $unknown == 0 && $propertyInsertData['prop_purpose'] != "UNKNOWN") {
                return array(1, "SALE");
            } else if ($sale >= 1 && $rent == 0 && $unknown == 0 && $propertyInsertData['prop_purpose'] != "UNKNOWN") {
                return array(1, "RENT");
            } else {
                return array(0, $propertyInsertData['prop_purpose']);
            }
        }
    }

}
