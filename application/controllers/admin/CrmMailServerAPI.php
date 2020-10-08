<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class CrmMailServerAPI extends Admin_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function bulk_mail(){
      $can_do = can_do(161);
      if (!empty($can_do)) {
        echo '<form id="markettingLogin" method="POST" action="' . base_url() . 'marketing/ms/customer/guest/index">
            <input type="hidden" name="CustomerLogin[email]" value="a.ayad@pcasa.ae"/>
            <input type="hidden" name="CustomerLogin[password]" value="hple1901w"/>
            <input type="hidden" name="backURLFromMarketting" value="' . base_url("admin/dashboard") . '"/>
            <input type="hidden" name="FromName" value="CRM"/>
          </form><script>document.getElementById("markettingLogin").submit();</script>';
        exit();
      }
    }

    public function newCampaign(){
      $can_do = can_do(163);
      if (!empty($can_do)) {
        echo '<form id="markettingLogin" method="POST" action="' . base_url() . 'marketing/ms/customer/guest/index">
            <input type="hidden" name="CustomerLogin[email]" value="a.ayad@pcasa.ae"/>
            <input type="hidden" name="CustomerLogin[password]" value="hple1901w"/>
            <input type="hidden" name="backURLFromMarketting" value="' . base_url("admin/dashboard") . '"/>
            <input type="hidden" name="FromName" value="CRM"/>
            <input type="hidden" name="redirectURL" value="CampaignCreate"/>
            <input type="hidden" name="redirect" value="TRUE"/>
          </form><script>document.getElementById("markettingLogin").submit();</script>';
        exit();
      }
    }

    public function templateList(){
      $can_do = can_do(164);
      if (!empty($can_do)) {
        echo '<form id="markettingLogin" method="POST" action="' . base_url() . 'marketing/ms/customer/guest/index">
            <input type="hidden" name="CustomerLogin[email]" value="a.ayad@pcasa.ae"/>
            <input type="hidden" name="CustomerLogin[password]" value="hple1901w"/>
            <input type="hidden" name="backURLFromMarketting" value="' . base_url("admin/dashboard") . '"/>
            <input type="hidden" name="FromName" value="CRM"/>
            <input type="hidden" name="redirectURL" value="TemplatesList"/>
            <input type="hidden" name="redirect" value="TRUE"/>
          </form><script>document.getElementById("markettingLogin").submit();</script>';
        exit();
      }
    }

    public function templateNew(){
      $can_do = can_do(165);
      if (!empty($can_do)) {
        echo '<form id="markettingLogin" method="POST" action="' . base_url() . 'marketing/ms/customer/guest/index">
            <input type="hidden" name="CustomerLogin[email]" value="a.ayad@pcasa.ae"/>
            <input type="hidden" name="CustomerLogin[password]" value="hple1901w"/>
            <input type="hidden" name="backURLFromMarketting" value="' . base_url("admin/dashboard") . '"/>
            <input type="hidden" name="FromName" value="CRM"/>
            <input type="hidden" name="redirectURL" value="NewTemplate"/>
            <input type="hidden" name="redirect" value="TRUE"/>
          </form><script>document.getElementById("markettingLogin").submit();</script>';
        exit();
      }
    }

    public function lead_new_email() {
        $ddata['lead_id'] = $this->uri->segment(4);
        $ddata['emailTemplates'] = $this->sendToAPI("getAllTemplatesInfoList");
        $data['subview'] = $this->load->view('admin/leads/_modal_lead_new_email',$ddata , FALSE);
        $this->load->view('admin/_layout_modal', $data);
    }

    public function lead_send_email(){
        $response = array();
        $lead_id = $this->input->post("lead_id");
        $lead_email = $this->input->post("lead_email");
        $lead_name = $this->input->post("lead_name");
        $emailTemplate = $this->input->post("emailTenplate");
        $emailSubject = $this->input->post("emailSubject");

        if(empty($lead_email)) {
          $response = array(array(
              'status'    => '406 ERROR',
              'reply'     => "Lead e-mail is not set."
          ), 406);
          echo json_encode($response);
          exit();
        }

        if(empty($lead_name)) {
          $response = array(array(
              'status'    => '406 ERROR',
              'reply'     => "Lead name is not set."
          ), 406);
          echo json_encode($response);
          exit();
        }

        if(empty($emailTemplate)) {
          $response = array(array(
              'status'    => '406 ERROR',
              'reply'     => "E-mail template is not set."
          ), 406);
          echo json_encode($response);
          exit();
        }

        if(empty($emailSubject)) {
          $response = array(array(
              'status'    => '406 ERROR',
              'reply'     => "E-mail subject is not set."
          ), 406);
          echo json_encode($response);
          exit();
        }

        $params = array(
          'CRM-TEMPLATE-UID' => $emailTemplate,
          'CRM-EMAIL-SUBJECT' => $emailSubject,
          'CRM-LEAD-EMAIL' => $lead_email,
          'CRM-LEAD-NAME' => $lead_name
        );

        $APIReply = $this->sendToAPI("sendEmailToLead", $params);
        $response = array(array(
            'status'    => $APIReply->status,
            'reply'     => $APIReply->reply
        ), $APIReply->statusCode);
        echo json_encode($response);
        exit();
    }

    protected function sendToAPI($action, $externalParam = NULL){
      $publicKey = "2687211f183340bcdded6498a29debcd9c68856c";
      $privateKey = "372d9641cc3e2a157e0faf37bd73f26feb2bfca0";
      $params = array(
        'CRM-APPLICATINO-NAME' => "Perfecta Casa",
        'CRM-PUBLIC-KEY' => $publicKey,
        'CRM-PRIVATE-KEY' => $privateKey,
        'CRM-TIMESTAMP' => time(),
        'CRM-SIGNATURE-KEY' => hash("tiger192,3", $publicKey + $privateKey + md5($publicKey + $privateKey) + date("Y-m-d")),
        'CRM-ACTION' => md5($action)
      );

      if(!empty($externalParam)){
        $params = array_merge($params, $externalParam);
      }

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "http://final.pcasa.info/marketing/ms/api/crm");
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_AUTOREFERER, true);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $response = json_decode(curl_exec($ch));
      curl_close($ch);

      if(isset($response->status) && $response->status == "SuccessWithDataReply"){
        return $response->reply;
      }else{
        return $response;
      }
    }
}
