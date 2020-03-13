<?php
namespace app\models;
use Yii;
use yii\base\Model;


class Keriouser extends Model{

    const SCENARIO_EDIT='edit';

    public $quota_daily;
    public $quota_weekly;
    public $quota_monthly;
    public $credentials_userName;
    public $credentials_password;
    public $credentials_passwordConf;
    public $fullName;
    public $description;
    public $email;
    public $autoLogin_addresses;
    public $autoLogin_macAddresses;
    public $autoLogin_vpnAddress;
    public $groups;
    public $use_template;

    public function scenarios(){
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_EDIT]=[
                'fullName',
                'description',
                'groups',
            ];
        return $scenarios;
    }

    public function rules()
    {
        return [
            // username and password are both required
            [['credentials_userName', 'credentials_password', 'credentials_passwordConf', 'fullName', 'description', 'groups'], 'required'],
            // rememberMe must be a boolean value
            [['quota_daily', 'quota_weekly', 'quota_monthly', 'use_template'], 'integer'],
            [['email', 'autoLogin_addresses', 'autoLogin_macAddresses', 'autoLogin_vpnAddress'], 'string'],
            // password is validated by validatePassword()
            ['credentials_passwordConf', 'validatePassword'],
        ];
    }


    public function validatePassword($attribute, $params){
        if( $this->credentials_password!=$this->credentials_passwordConf)
            $this->addError($attribute,'Ваш текущий пароль неверен');
    }


    public function attributeLabels()
    {
        return [
            'credentials_userName' => Yii::t('app', 'Login'),
            'credentials_password' => Yii::t('app', 'Parol'),
            'credentials_passwordConf' => Yii::t('app', 'Parolni takror kiriting'),
            'fullName' => Yii::t('app', 'F.I.O'),
            'description' => Yii::t('app', 'Qo`shimcha ma`lumot'),
            'email' => Yii::t('app', 'Email'),
            'use_template' => Yii::t('app', 'Joriy andozadan foydalanasizmi'),
            'quota_daily' => Yii::t('app', 'Kunlik kvota'),
            'quota_weekly' => Yii::t('app', 'Haftalik kvota'),
            'quota_monthly' => Yii::t('app', 'Oylik kvota'),
            'groups' => Yii::t('app', 'Guruh'),
            'autoLogin_addresses' => Yii::t('app', 'Avtomatik kirish IP adress bo`yicha'),
            'autoLogin_macAddresses' => Yii::t('app', 'Avtomatik kirish MAC adress bo`yicha'),
            'autoLogin_vpnAddress' => Yii::t('app', 'Avtomatik VPN IP adress bo`yicha'),
        ];
    }
}
