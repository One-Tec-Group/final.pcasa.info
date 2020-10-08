<?php defined('MW_PATH') || exit('No direct script access allowed');

class CustomerLogin extends Customer {
    public $remember_me = true;

    public function rules() {
        $hooks  = Yii::app()->hooks;
        $apps   = Yii::app()->apps;
        $filter = $apps->getCurrentAppName() . '_model_'.strtolower(get_class($this)).'_'.strtolower(__FUNCTION__);

        $rules = array(
            array('email, password', 'required'),
            array('email', 'length', 'min' => 7, 'max' => 100),
            array('email', 'email', 'validateIDN' => true),
            array('password', 'length', 'min' => 6, 'max' => 100),
            array('password', 'authenticate'),

            array('remember_me', 'safe'),
        );

        $rules = $hooks->applyFilters($filter, new CList($rules));
        $this->onRules(new CModelEvent($this, array(
            'rules' => $rules,
        )));

        return $rules->toArray();
    }

    public function attributeLabels() {
        $labels = array(
            'remember_me' => Yii::t('customers', 'Remember me'),
        );

        return CMap::mergeArray($labels, parent::attributeLabels());
    }

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function authenticate($attribute, $params) {
        if ($this->hasErrors()) {
            return;
        }

        $identity = new CustomerIdentity($this->email, $this->password);
        if (!$identity->authenticate()) {
            $this->addError($attribute, $identity->errorCode);
            return;
        }

        if (!Yii::app()->customer->login($identity, $this->remember_me ? 3600 * 24 * 30 : 0)) {
            $this->addError($attribute, Yii::t('customers', 'Unable to login with the given identity!'));
            return;
        }
    }
}
