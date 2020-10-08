<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Ext_dragdropeditorController
 *
 * @package MailWizz EMA
 * @author Fiorino De Santo
 * @copyright 2013-2017 MailWizz EMA (http://www.mailwizz.com)
 * @license http://www.mailwizz.com/license/
 */

class Ext_dragdropeditorController extends Controller
{
    public function actionUpload()
    {
    	if(Yii::app()->user->getId() > 0 || Yii::app()->customer->getId() > 0) {
    		$extension      = Yii::app()->extensionsManager->getExtensionInstance('ckeditor');
    		if ($extension->isAppName('backend') && Yii::app()->user->getId() > 0) {
	            $filesPath  = Yii::getPathOfAlias('root.frontend.assets.files');
	            if (!file_exists($filesPath) || !is_dir($filesPath)) {
	                @mkdir($filesPath, 0777, true);
	            }
	            $filesUrl   = Yii::app()->apps->getAppUrl('frontend', 'frontend/assets/files', true, true);

	        } elseif ($extension->isAppName('customer') && Yii::app()->customer->getId() > 0) {
	            $customerFolderName = Yii::app()->customer->getModel()->customer_uid;

	            $filesPath  = Yii::getPathOfAlias('root.frontend.assets.files');
	            if (!file_exists($filesPath) || !is_dir($filesPath)) {
	                @mkdir($filesPath, 0777, true);
	            }
	            $filesUrl   = Yii::app()->apps->getAppUrl('frontend', 'frontend/assets/files/customer/' . $customerFolderName, true, true);

	            $filesPath .= '/customer';
	            if (!file_exists($filesPath) || !is_dir($filesPath)) {
	                @mkdir($filesPath, 0777, true);
	            }
	            $filesPath .= '/' . $customerFolderName;
	            if (!file_exists($filesPath) || !is_dir($filesPath)) {
	                @mkdir($filesPath, 0777, true);
	            }
	        }

	    	header('Cache-Control: no-cache, must-revalidate');
	    	
			//Specify url path
			$path = $filesPath.'/'; 

			//Read image
			$count = $_REQUEST['count'];
			$b64str = $_REQUEST['hidimg-' . $count]; 
			$imgname = $_REQUEST['hidname-' . $count]; 
			$imgtype = $_REQUEST['hidtype-' . $count]; 

			//Generate random file name here
			if($imgtype == 'png'){
				$image = time().'-'.$imgname . '-' . base_convert(rand(),10,36) . '.png'; 
			} else {
				$image = time().'-'.$imgname . '-' . base_convert(rand(),10,36) . '.jpg'; 
			}

			//Save image

			$success = file_put_contents($path . $image, base64_decode($b64str)); 
			if ($success === FALSE) {

			  if (!file_exists($path)) {
			    echo "<html><body onload=\"alert('Saving image to folder failed. Folder ".$path." not exists.')\"></body></html>";
			  } else {
			    echo "<html><body onload=\"alert('Saving image to folder failed. Please check write permission on " .$path. "')\"></body></html>";
			  }
			    
			} else {
			  //Replace image src with the new saved file
			  echo "<html><body onload=\"parent.document.getElementById('img-" . $count . "').setAttribute('src','" . $filesUrl . '/' .$image . "');  parent.document.getElementById('img-" . $count . "').removeAttribute('id') \"></body></html>";
			}
		}
    }
}
