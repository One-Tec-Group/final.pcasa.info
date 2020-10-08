<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 *
 * @package MailWizz EMA
 * @author Serban George Cristian <cristian.serban@mailwizz.com>
 * @link https://www.mailwizz.com/
 * @copyright 2013-2018 MailWizz EMA (https://www.mailwizz.com)
 * @license https://www.mailwizz.com/license/
 * @since 1.0
 */

/**
 * This hook gives a chance to prepend content or to replace the default view content with a custom content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->data}
 * In case the content is replaced, make sure to set {@CAttributeCollection $collection->renderContent} to false
 * in order to stop rendering the default content.
 * @since 1.3.3.1
 */
$hooks->doAction('before_view_file_content', $viewCollection = new CAttributeCollection(array(
    'controller'    => $this,
    'renderContent' => true,
)));

// and render if allowed
if ($viewCollection->renderContent) {
    ?>
    <div class="pull-left">
        <?php $this->widget('customer.components.web.widgets.MailListSubNavWidget', array(
            'list' => $list,
        ))?>
    </div>
    <div class="clearfix"><!-- --></div>
    <hr />
    <div class="clearfix"><!-- --></div>
    <ul class="nav nav-tabs list-forms-nav" style="border-bottom: 0px;">
        <li class="active"><a href="#subscribe-form"><?php echo Yii::t('list_forms', 'Subscribe form');?></a></li>
        <li class="inactive"><a href="#unsubscribe-form"><?php echo Yii::t('list_forms', 'Unsubscribe form');?></a></li>
    </ul>
    <div class="form-container" id="subscribe-form">
        <div class="box box-primary borderless">
            <div class="box-header">
                <div class="pull-left">
                    <h3 class="box-title"><?php echo Yii::t('list_forms', 'Subscribe form');?></h3>
                </div>
                <div class="pull-right"></div>
                <div class="clearfix"><!-- --></div>
            </div>
            <div class="box-body">
                <div class="form-group">
 
<textarea class="form-control" rows="20">
<form action="<?php echo Yii::app()->apps->getAppUrl('frontend', 'lists/' . $list->list_uid . '/subscribe', true);?>" method="post" accept-charset="utf-8" target="_blank">
    <?php foreach ($fields as $field) { ?>
    <div class="form-group"<?php if ($field->visibility == ListField::VISIBILITY_HIDDEN) {?> style="display:none"<?php }?>>
        <?php if ($field->type->identifier == 'dropdown' || $field->type->identifier == 'multiselect') { ?>
        <label><?php echo CHtml::encode($field->label);?><?php if ($field->required == ListField::TEXT_YES){?> <span class="required">*</span><?php }?></label>
        <select class="form-control" name="<?php echo CHtml::encode($field->tag) . ($field->type->identifier == 'multiselect' ? '[]' : '');?>" placeholder="<?php echo CHtml::encode($field->help_text);?>"<?php if ($field->required == ListField::TEXT_YES) {?> required <?php }?><?php if ($field->type->identifier == 'multiselect') { ?> multiple="multiple" <?php }?>>
        <?php if ($field->required != ListField::TEXT_YES) {?><option value=""><?php echo Yii::t('app', 'Please choose');?></option><?php } ?>
        <?php if (!empty($field->options)) { foreach ($field->options as $option) { ?>
        <option value="<?php echo $option->value;?>"<?php echo $option->value == $field->default_value ? ' selected="selected"' : '';?>><?php echo CHtml::encode($option->value);?></option>
        <?php }} ?>
        </select>
    <?php } elseif ($field->type->identifier == 'textarea') { ?>
        <label><?php echo CHtml::encode($field->label);?><?php if ($field->required == ListField::TEXT_YES){?> <span class="required">*</span><?php }?></label>
        <?php echo CHtml::encode('<');?>textarea class="form-control" name="<?php echo CHtml::encode($field->tag);?>" placeholder="<?php echo CHtml::encode($field->help_text);?>"<?php if ($field->required == ListField::TEXT_YES) {?> required <?php }?>><?php echo CHtml::encode(ListField::parseDefaultValueTags($field->default_value));?><?php echo CHtml::encode('<');?>/textarea<?php echo CHtml::encode('>');?>
    <?php } elseif ($field->type->identifier == 'consentcheckbox') { ?>
        <?php echo CHtml::encode('<');?>input type="checkbox" class="form-control" name="<?php echo CHtml::encode($field->tag);?>" value="<?php echo CHtml::encode($field->getModelMetaData()->itemAt('consent_text'));?>"<?php if ($field->required == ListField::TEXT_YES) {?> required <?php }?>/>
        <label><?php echo CHtml::encode($field->label);?><?php if ($field->required == ListField::TEXT_YES){?> <span class="required">*</span><?php }?></label>
        <div><?php echo CHtml::encode($field->getModelMetaData()->itemAt('consent_text'));?></div>
    <?php } elseif ($field->type->identifier == 'checkbox') { ?>
        <?php echo CHtml::encode('<');?>input type="checkbox" class="form-control" name="<?php echo CHtml::encode($field->tag);?>" value="1"<?php if ($field->required == ListField::TEXT_YES) {?> required <?php }?>/>
        <label><?php echo CHtml::encode($field->label);?><?php if ($field->required == ListField::TEXT_YES){?> <span class="required">*</span><?php }?></label>
    <?php } else { ?>
        <label><?php echo CHtml::encode($field->label);?><?php if ($field->required == ListField::TEXT_YES){?> <span class="required">*</span><?php }?></label>
        <input type="text" class="form-control" name="<?php echo CHtml::encode($field->tag);?>" placeholder="<?php echo CHtml::encode($field->help_text);?>" value="<?php echo CHtml::encode(ListField::parseDefaultValueTags($field->default_value));?>"<?php if ($field->required == ListField::TEXT_YES) {?> required <?php }?>/>
    <?php }?>
    </div>
    <?php } ?>
    <div class="clearfix"><!-- --></div>
    <div class="actions">
        <button type="submit" class="btn btn-primary btn-flat"><?php echo Yii::t('list_forms', 'Subscribe');?></button>
    </div>
    <div class="clearfix"><!-- --></div>
</form>
</textarea>
                    <hr />
                    <h5><?php echo Yii::t('list_forms', 'Iframe version');?></h5>
                    <textarea class="form-control" rows="3">
<iframe src="<?php echo Yii::app()->apps->getAppUrl('frontend', 'lists/'.$list->list_uid.'/subscribe', true);?>?output=embed&width=400&height=400" width="400" height="400" frameborder="0" scrolling="no"></iframe>
                    </textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="form-container" id="unsubscribe-form" style="display: none;">
        <div class="box box-primary borderless">
            <div class="box-header">
                <div class="pull-left">
                    <h3 class="box-title"><?php echo Yii::t('list_forms', 'Unsubscribe form');?></h3>
                </div>
                <div class="pull-right"></div>
                <div class="clearfix"><!-- --></div>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <textarea class="form-control" rows="20">
<form action="<?php echo Yii::app()->apps->getAppUrl('frontend', 'lists/'.$list->list_uid.'/unsubscribe', true);?>" method="post" accept-charset="utf-8" target="_blank">

    <div class="form-group">
        <label>Email <span class="required">*</span></label>
        <input type="text" class="form-control" name="EMAIL" placeholder="<?php echo Yii::t('list_forms', 'Please type your email address');?>" value="" required />
    </div>

    <div class="clearfix"><!-- --></div>
    <div class="actions pull-right">
        <button type="submit" class="btn btn-primary btn-flat"><?php echo Yii::t('list_forms', 'Unsubscribe');?></button>
    </div>
    <div class="clearfix"><!-- --></div>

</form>
                    </textarea>
                    <hr />
                    <h5><?php echo Yii::t('list_forms', 'Iframe version');?></h5>
                    <textarea class="form-control" rows="3">
<iframe src="<?php echo Yii::app()->apps->getAppUrl('frontend', 'lists/'.$list->list_uid.'/unsubscribe', true);?>?output=embed&width=400&height=200" width="400" height="200" frameborder="0" scrolling="no"></iframe>
                    </textarea>
                </div>
            </div>
        </div>
    </div>
    <hr />
    <div class="callout callout-info">
        <?php
        $text = 'Please note, you will have to style the forms below to match the place where you embed them.<br />
        You can create better forms by using the <a href="{sdkHref}" target="_blank">PHP-SDK</a> and connect to the provided api.';
        echo Yii::t('list_forms', StringHelper::normalizeTranslationString($text), array(
            '{sdkHref}' => Yii::app()->hooks->applyFilters('sdk_download_url', 'https://github.com/twisted1919/mailwizz-php-sdk'),
        ));
        ?>
    </div>
<?php
}
/**
 * This hook gives a chance to append content after the view file default content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->data}
 * @since 1.3.3.1
 */
$hooks->doAction('after_view_file_content', new CAttributeCollection(array(
    'controller'        => $this,
    'renderedContent'   => $viewCollection->renderContent,
)));
