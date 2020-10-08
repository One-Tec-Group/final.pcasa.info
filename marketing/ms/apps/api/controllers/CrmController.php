<?php defined('MW_PATH') || exit('No direct script access allowed');

class CrmController extends Controller {

  protected $CustomerID;

  public function init() {
      parent::init();

      $options = Yii::app()->options;
      $request = Yii::app()->request->getPost();

      if ($options->get('system.common.api_status', 'online') != 'online' || $options->get('system.common.site_status', 'online') != 'online') {
          return $this->renderJson(array(
              'status'    => 'error',
              'reply'     => Yii::t('api', 'Service Unavailable.')
          ), 503);
      }

      $timeStamp = time();
      $companyInformation = Yii::app()->db->createCommand("SELECT * FROM `mw_customer_api_key` WHERE `name` LIKE '" . $request["CRM-APPLICATINO-NAME"] . "';")->queryRow();
      $companySecret = hash("tiger192,3", $companyInformation['public'] + $companyInformation['private'] + md5($companyInformation['public'] + $companyInformation['private']) + date("Y-m-d"));
      $this->CustomerID = $companyInformation['customer_id'];

      if(!empty($companyInformation) && $companyInformation['public'] == $request["CRM-PUBLIC-KEY"] && $companyInformation['private'] == $request["CRM-PRIVATE-KEY"] && $companySecret == $request["CRM-SIGNATURE-KEY"]){
        if(($request["CRM-PUBLIC-KEY"] - 5) <= $timeStamp){
          if($request['CRM-ACTION'] == md5("getAllTemplatesInfoList")){
            $this->allTemplatesInfoList($this->CustomerID);
          } else if ($request['CRM-ACTION'] == md5("sendEmailToLead")){
            $this->SendEmail();
          } else {
            return $this->renderJson(array(
                'status'    => 'Method Not Allowed',
                'reply'     => "The method specified in the Request-Line is not allowed for the specified resource."
            ), 405);
          }
        } else {
          return $this->renderJson(array(
              'status'    => 'Request Timeout',
              'reply'     => "Your browser failed to send a request in the time allowed by the server."
          ), 408);
        }
      } else {
        return $this->renderJson(array(
            'status'    => 'Invalid Params',
            'reply'     => "Invalid API request params. Please check you info."
        ), 408);
      }
      exit();
  }

    public function accessRules() {
        return array(
            array('allow'),
        );
    }

    protected function allTemplatesInfoList($customerID){
      $templatesReturn = "";
      $templates = Yii::app()->db->createCommand("SELECT `template_uid`, `name` FROM `mw_customer_email_template` WHERE `customer_id` = " . $customerID . ";")->queryAll();

      for($i = 0; $i < count($templates); $i++){
        $templatesReturn .= "<option value='" . $templates[$i]['template_uid'] . "'>" . $templates[$i]['name'];
      }

      return $this->renderJson(array(
          'status' => 'SuccessWithDataReply',
          'reply' => $templatesReturn
      ), 200);
    }

    protected function SendEmail() {
        $request = Yii::app()->request;
        $client_name = $request->getPost("CRM-LEAD-NAME");
        $client_email = $request->getPost("CRM-LEAD-EMAIL");
        $emailTemplate = $request->getPost("CRM-TEMPLATE-UID");
        $emailSubject = $request->getPost("CRM-EMAIL-SUBJECT");

        if (empty($client_email)) {
          return $this->renderJson(array(
              'statusCode' => '406',
              'status' => '406 ERROR',
              'reply' => "Lead e-mail is not set."
          ), 406);
        }

        if (empty($client_name)) {
          return $this->renderJson(array(
              'statusCode' => '406',
              'status' => '406 ERROR',
              'reply' => "Lead name is not set."
          ), 406);
        }

        if (empty($emailTemplate)) {
          return $this->renderJson(array(
              'statusCode' => '406',
              'status' => '406 ERROR',
              'reply' => "E-mail template is not set."
          ), 406);
          exit();
        }

        if (empty($emailSubject)) {
          return $this->renderJson(array(
              'statusCode' => '406',
              'status' => '406 ERROR',
              'reply' => "E-mail subject is not set."
          ), 406);
          exit();
        }

        $template   = $this->loadModel($emailTemplate);
        $dsParams = array('useFor' => array(DeliveryServer::USE_FOR_EMAIL_TESTS));
        $server   = DeliveryServer::pickServer(0, $template, $dsParams);

        if (empty($server)) {
          return $this->renderJson(array(
              'statusCode' => '451',
              'status' => '451 ERROR',
              'reply' => "Email delivery is temporary disabled."
          ), 451);
          exit();
        }

        if (!FilterVarHelper::email($client_email)) {
            return $this->renderJson(array(
                'statusCode' => '400',
                'status' => '400 ERROR',
                'reply' => $template->content
            ), 400);
            exit();
        }

        $customer = Yii::app()->db->createCommand("SELECT * FROM `mw_delivery_server` WHERE `customer_id` LIKE '" . $this->CustomerID . "';")->queryRow();
        $fromName = $customer['from_name'];
        $fromEmail = $customer['from_email'];

        $params = array(
            'to' => $client_email,
            'toName' => $client_name,
            'from' => $fromEmail,
            'fromName' => $fromName,
            'subject' => $emailSubject,
            'body' => $template->content,
        );

        $sent = false;
        for ($i = 0; $i < 3; ++$i) {
            if ($sent = $server->setDeliveryFor(DeliveryServer::DELIVERY_FOR_TEMPLATE_TEST)->setDeliveryObject($template)->sendEmail($params)) {
                break;
            }
            if (!($server = DeliveryServer::pickServer($server->server_id, $template, $dsParams))) {
                break;
            }
        }

        if (!$sent) {
            return $this->renderJson(array(
                'statusCode' => '304',
                'status' => '304 ERROR',
                'reply' => "Unable to send the test email to " . $client_email . "!"
            ), 304);
            exit();
        } else {
            return $this->renderJson(array(
                'statusCode' => '200',
                'status' => '200 ERROR',
                'reply' => "E-mail successfully sent to " . $client_email . "!"
            ), 200);
            exit();
        }
    }


    public function loadModel($template_uid) {
        $model = CustomerEmailTemplate::model()->findByAttributes(array(
            'template_uid' => $template_uid,
            'customer_id' => (int) $this->CustomerID
        ));
        if($model === null) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }
        return $model;
    }

    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if ($error['code'] === 404) {
                $error['message'] = Yii::t('app', 'Page not found.');
            }
            return $this->renderJson(array(
                'status'    => 'error',
                'error'        => CHtml::encode($error['message']),
            ), $error['code']);
        }
    }
}
