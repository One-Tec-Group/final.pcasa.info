<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * DragDropEditorExt
 *
 * @package MailWizz EMA
 * @author Fiorino De Santo
 * @copyright 2013-2017 MailWizz EMA (http://www.mailwizz.com)
 * @license http://www.mailwizz.com/license/
 */

class DragDropEditorExt extends ExtensionInit
{
    // name of the extension as shown in the backend panel
    public $name = 'DragDropEditor';

    // description of the extension as shown in backend panel
    public $description = 'DragDropEditorExt for MailWizz EMA';

    // current version of this extension
    public $version = '2.1.0';

    // the author name
    public $author = 'Fiorino De Santo';

    // author website
    public $website = 'http://www.mailwizz.com/';

    // contact email address
    public $email = 'fiorino.desanto@gmail.com';

    // in which apps this extension is not allowed to run
    public $allowedApps = array('backend', 'customer');

    // can this extension be deleted? this only applies to core extensions.
    protected $_canBeDeleted = true;

    // can this extension be disabled? this only applies to core extensions.
    protected $_canBeDisabled = true;

    // the detected language
    protected $detectedLanguage = 'en';

    public function run()
    {
        $this->install();

        // the callback to register the editor
        Yii::app()->hooks->addAction('wysiwyg_editor_instance', array($this, 'createNewEditorInstance'));
        $this->registerAssets();
        
        // register the routes
        Yii::app()->urlManager->addRules(array(
            array('ext_dragdropeditor/upload', 'pattern' => 'extensions/drag-drop-editor/upload')
        ));

        // add the controller
        Yii::app()->controllerMap['ext_dragdropeditor'] = array(
            'class' => 'ext-drag-drop-editor.controllers.Ext_dragdropeditorController',
        );
    }

    public function install()
    {
        $filesPathTo  = Yii::getPathOfAlias('root.assets.drag-drop-editor');
        /*$filesPathFrom  = Yii::getPathOfAlias('root.apps.extensions.drag-drop-editor.drag-drop-editor');
        if (file_exists($filesPathFrom) || is_dir($filesPathFrom)) {
            if (file_exists($filesPathTo) || is_dir($filesPathTo)) {
                $this->deleteFolder($filesPathTo);
            }
            if (!file_exists($filesPathTo) || !is_dir($filesPathTo)) {
                rename($filesPathFrom,$filesPathTo);
            }
        }*/
        /*if (file_exists($filesPathTo) || is_dir($filesPathTo)) {
            $this->deleteFolder($filesPathTo);
        }*/
        $filesPathTo  = Yii::getPathOfAlias('root.frontend.assets.gallery.dragdropeditor');
        $filesPathFrom  = Yii::getPathOfAlias('root.apps.extensions.drag-drop-editor.dragdropeditor');
        if (file_exists($filesPathFrom) || is_dir($filesPathFrom)) {
            if (file_exists($filesPathTo) || is_dir($filesPathTo)) {
                $this->deleteFolder($filesPathTo);
            }
            if (!file_exists($filesPathTo) || !is_dir($filesPathTo)) {
                rename($filesPathFrom,$filesPathTo);
            }
        }
    }

    private function deleteFolder($path)
    {
        if (is_dir($path) === true)
        {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($files as $file)
            {
                if (in_array($file->getBasename(), array('.', '..')) !== true)
                {
                    if ($file->isDir() === true)
                    {
                        rmdir($file->getPathName());
                    }

                    else if (($file->isFile() === true) || ($file->isLink() === true))
                    {
                        unlink($file->getPathname());
                    }
                }
            }

            return rmdir($path);
        }

        else if ((is_file($path) === true) || (is_link($path) === true))
        {
            return unlink($path);
        }

        return false;
    }


    public function createNewEditorInstance($editorOptions)
    {
        $extension  = Yii::app()->extensionsManager->getExtensionInstance('ckeditor');
        $filemanager = false;
        if ($extension->isAppName('backend') && $extension->getOption('enable_filemanager_user') && Yii::app()->user->getId() > 0) {
            $filemanager = true;
        } elseif ($extension->isAppName('customer') && $extension->getOption('enable_filemanager_customer') && Yii::app()->customer->getId() > 0) {
            $filemanager = true;
        }

        //$this->registerAssets();
        $url = substr(Yii::app()->getBaseUrl(true), 0, strrpos(Yii::app()->getBaseUrl(true),'/') );
        $rewrite = Yii::app()->options->get('system.common.clean_urls');
        $script = '
        jQuery(document).ready(function ($) {
            if(typeof wysiwygInstanceCampaignTemplate_content !== \'undefined\') {
                if($(\'#CampaignTemplate_content\').length && ($(\'#CampaignTemplate_content\').val().indexOf(\'contentarea\') !== -1 || $(\'#CampaignTemplate_content\').val() == \'\')){
                    wysiwygInstanceCampaignTemplate_content.destroy();
                    $(\'#CampaignTemplate_content,#btn_CampaignTemplate_content,#builder_CampaignTemplate_content\').hide();
                    $(\'#CampaignTemplate_content\').after(\'<iframe id="cb-frame" src="'.$url.'/frontend/assets/gallery/dragdropeditor/email-editor.html?filemanager='.($filemanager ? '1' : '0').'&url='.str_replace('://','___',Yii::app()->getBaseUrl(true)).'&csrf_token='.Yii::app()->request->csrfToken.'&v='.$this->version.'" style="width:100%;height:800px;" frameborder="0" ></iframe>\');
                }
            }
            if(typeof wysiwygInstanceCustomerEmailTemplate_content !== \'undefined\' && (window.location.hash || $(\'#CustomerEmailTemplate_content\').val().indexOf(\'contentarea\') !== -1 )) {
                if($(\'#CustomerEmailTemplate_content\').length){
                    wysiwygInstanceCustomerEmailTemplate_content.destroy();
                    $(\'#CustomerEmailTemplate_content,#btn_CustomerEmailTemplate_content,#builder_CustomerEmailTemplate_content\').hide();
                    $(\'#CustomerEmailTemplate_content\').after(\'<iframe id="cb-frame" src="'.$url.'/frontend/assets/gallery/dragdropeditor/email-editor.html?filemanager='.($filemanager ? '1' : '0').'&url='.str_replace('://','___',Yii::app()->getBaseUrl(true)).'&csrf_token='.Yii::app()->request->csrfToken.'&r='.$rewrite.'&v='.$this->version.'" style="width:100%;height:800px;" frameborder="0" ></iframe>\');
                }
            }
        });
        ';
        Yii::app()->clientScript->registerScript(md5('DragDropEditor'), $script);
    }

    public function registerAssets()
    {
        static $_assetsRegistered = false;
        if ($_assetsRegistered) {
            return $this;
        }
        $_assetsRegistered = true;

        // set a flag to know which editor is active.

        $assetsUrl = $this->getAssetsUrl();
        Yii::app()->clientScript->registerScriptFile($assetsUrl . '/drag-drop-editor.js');

        return $this;
    }

    // the assets url, publish if needed.
    public function getAssetsUrl()
    {
        return Yii::app()->assetManager->publish(dirname(__FILE__).'', false, -1, MW_DEBUG);
    }
}
