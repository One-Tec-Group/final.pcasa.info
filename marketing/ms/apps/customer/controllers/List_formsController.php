<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * List_formsController
 * 
 * Handles the actions for list forms related tasks
 * 
 * @package MailWizz EMA
 * @author Serban George Cristian <cristian.serban@mailwizz.com> 
 * @link https://www.mailwizz.com/
 * @copyright 2013-2018 MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 1.0
 */
 
class List_formsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('list-forms.js')));
        parent::init();    
    }

    /**
     * @param $list_uid
     * @throws CHttpException
     */
    public function actionIndex($list_uid)
    {
        $list = $this->loadListModel($list_uid);
        $request = Yii::app()->request;
        
        $criteria = new CDbCriteria();
        $criteria->compare('list_id', $list->list_id);
        $criteria->order = 'sort_order ASC';
        $fields = ListField::model()->findAll($criteria);
        
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('list_forms', 'Your mail list forms'),
            'pageHeading'       => Yii::t('list_forms', 'Embed list forms'), 
            'pageBreadcrumbs'   => array(
                Yii::t('lists', 'Lists') => $this->createUrl('lists/index'),
                $list->name => $this->createUrl('lists/overview', array('list_uid' => $list->list_uid)),
                Yii::t('list_forms', 'Embed list forms')
            )
        ));

        $this->render('index', compact('list', 'fields'));
    }

    /**
     * Helper method to load the list AR model
     */
    public function loadListModel($list_uid)
    {
        $model = Lists::model()->findByAttributes(array(
            'list_uid'      => $list_uid,
            'customer_id'   => (int)Yii::app()->customer->getId(),
        ));
        
        if ($model === null) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }
        
        return $model;
    }
}