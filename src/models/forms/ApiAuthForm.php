<?php

namespace luya\apiauth\models\forms;


use yii\base\Model;
use luya\apiauth\models\APIAuthUser;

/**
 * Login form
 */
class ApiAuthForm extends Model
{
    public $app_key;
    public $app_secret;

    /**
     * @var $_user APIAuthUser
     */
    private $_user;

    const GET_API_TOKEN = 'generate_api_token';

    public function init()
    {
        parent::init();
        $this->on(self::GET_API_TOKEN, [$this, 'onGenerateApiToken']);
    }


    /**
     * @inheritdoc
     * 对客户端表单数据进行验证的rule
     */
    public function rules()
    {
        return [
            [['app_key', 'app_secret'], 'required'],
            ['app_secret', 'validateAppSecret'],
        ];
    }

    /**
     * 自定义的密码认证方法
     * @param $attribute
     */
    public function validateAppSecret($attribute)
    {
        if (!$this->hasErrors()) {
            $this->_user = $this->getUser();
            if (!$this->_user || !$this->_user->validateAppSecret($this->app_secret)) {
                $this->addError($attribute, 'APPKEY或APPSECRET错误.');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'app_key' => 'APP_KEY',
            'app_secret' => 'APP_SECRET',
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return ApiAuthUser|null whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $this->trigger(self::GET_API_TOKEN);
            return $this->_user;
        } else {
            return null;
        }
    }

    /**
     * 根据用户Key获取用户的认证信息
     *
     * @return ApiAuthUser|\yii\web\IdentityInterface
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = ApiAuthUser::findIdentityByAppKey($this->app_key);
        }

        return $this->_user;
    }

    /**
     * 登录校验成功后，为用户生成新的token
     * 如果token时间失效，则重新生成token
     * @throws \yii\base\Exception
     */
    public function onGenerateApiToken()
    {
        if (!ApiAuthUser::validateApiToken($this->_user->api_token)) {
            $this->_user->generateApiToken();
            $this->_user->save(false);
        }
    }
}