<?php

namespace oom\api\models;

use luya\admin\models\ApiUser;
use oom\api\admin\aws\ChangeSecretActiveWindow;
use luya\admin\ngrest\base\NgRestModel;
use Yii;
use yii\base\Exception;
use yii\filters\RateLimitInterface;
use yii\web\IdentityInterface;

/**
 * Apiauth User.
 *
 * File has been created with `crud/create` command.
 *
 * @property integer $id
 * @property integer $admin_id
 * @property string $email
 * @property string $username
 * @property string $app_key
 * @property string $api_token
 * @property string $app_secret
 * @property string $app_secret_reset_token
 * @property integer $allowance
 * @property integer $allowance_updated_at
 * @property int $status
 * @property integer $created_at
 * @property integer $updated_at
 */
class APIAuthUser extends NgRestModel implements IdentityInterface, RateLimitInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * @inheritdoc
     */
//    public $i18n = ['email', 'username', 'app_key', 'api_token', 'app_secret', 'app_secret_reset_token'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_user_api_auth';
    }

    /**
     * @inheritdoc
     */
    public static function ngRestApiEndpoint()
    {
        return 'api-auth-user';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('apiadmin', 'ID'),
            'admin_id' => Yii::t('apiadmin', 'Admin User'),
            'email' => Yii::t('apiadmin', 'Email'),
            'username' => Yii::t('apiadmin', 'Username'),
            'app_key' => Yii::t('apiadmin', 'App Key'),
            'api_token' => Yii::t('apiadmin', 'Api Token'),
            'app_secret' => Yii::t('app', 'App Secret'),
            'app_secret_reset_token' => Yii::t('app', 'App Secret Reset Token'),
            'allowance' => Yii::t('app', 'Allowance'),
            'allowance_updated_at' => Yii::t('app', 'Allowance Updated At'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'admin_id','username', 'app_key', 'api_token', 'app_secret', 'created_at', 'updated_at'], 'required'],
            [['allowance', 'allowance_updated_at', 'status', 'created_at', 'updated_at'], 'integer'],
            [['email', 'api_token', 'app_secret', 'app_secret_reset_token'], 'string', 'max' => 255],
            [['username', 'app_key'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function genericSearchFields()
    {
        return ['email', 'username', 'app_key', 'api_token', 'app_secret', 'app_secret_reset_token'];
    }

    /**
     * @inheritdoc
     */
    public function ngRestAttributeTypes()
    {
        return [
            'admin_id' =>[
                'selectModel',
                'model'=>ApiUser::className(),
                'valueField'=>'id',
                'labelField'=>'email'
            ],
            'email' => 'text',
            'username' => 'text',
            'app_key' => 'text',
            'api_token' => 'text',
            'app_secret' => 'text',
            'app_secret_reset_token' => 'text',
            'allowance' => 'number',
            'allowance_updated_at' => 'number',
            'status' => ['selectArray', 'data' => [self::STATUS_DELETED => '禁用', self::STATUS_ACTIVE => '启用']],
            'created_at' => 'number',
            'updated_at' => 'number',
        ];
    }

    /**
     * @inheritdoc
     */
    public function ngRestScopes()
    {
        return [
            [
                'list',
                [
                    'admin_id',
                    'app_key',
                    'allowance',
                    'updated_at'
                ]
            ],
            [
                ['create', 'update'],
                [
                    'admin_id',
                    'allowance',
                    'allowance_updated_at',
                    'status',
                ]
            ],
            ['delete', true],
        ];
    }

    public function ngRestActiveWindows()
    {
        return [
            ['class' => ChangeSecretActiveWindow::className(), 'label' => Yii::t('apiadmin','Change Secret'), 'icon' => 'poll'],
        ];
    }

    /**
     * @param $app_secret
     * @return bool
     * 验证app_secret
     */
    public function validateAppSecret($app_secret)
    {
        return $this->app_secret === $app_secret;
    }

    /**
     * 通过token找到指定授权用户.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['api_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * 通过ID找到对应授权用户
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAppKey()
    {
        return $this->app_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $AppKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAppKey()
     */
    public function validateAppKey($AppKey)
    {
        return $this->app_key === $AppKey;
    }

    /**
     * 返回某一时间允许请求的最大数量，比如设置10秒内最多5次请求
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @return array an array of two elements. The first element is the maximum number of allowed requests,
     * and the second element is the size of the window in seconds.
     */
    public function getRateLimit($request, $action)
    {
        return [5, 10];
    }

    /**
     * 返回剩余的允许的请求和相应的UNIX时间戳数
     * Loads the number of allowed requests and the corresponding timestamp from a persistent storage.
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @return array an array of two elements. The first element is the number of allowed requests,
     * and the second element is the corresponding UNIX timestamp.
     */
    public function loadAllowance($request, $action)
    {
        return [$this->allowance, $this->allowance_updated_at];
    }

    /**
     * 保存允许剩余的请求数和当前的UNIX时间戳
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @param int $allowance the number of allowed requests remaining.
     * @param int $timestamp the current timestamp.
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $this->allowance = $allowance;
        $this->allowance_updated_at = $timestamp;
        $this->save();
    }


    /**
     * @param $app_key
     * @return null|static
     */
    public static function findIdentityByAppKey($app_key)
    {
        return static::findOne(['app_key' => $app_key, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * 生成密码hash（APP_SECRET）
     * @throws Exception
     */
    public function generateAppSecret()
    {
        $app_secret = Yii::$app->security->generateRandomString();
        $this->app_secret = strtolower(str_replace(['-', '_'], 'a', $app_secret));
    }

    /**
     * 生成APP_KEY
     * @throws Exception
     */
    public function generateAppKey()
    {
        $this->app_key = Yii::$app->security->generateRandomString(16);
    }

    /**
     * 生成 api_token
     * @throws Exception
     */
    public function generateApiToken()
    {
        $this->api_token = Yii::$app->security->hashData(Yii::$app->security->generateRandomString(16), $this->app_secret). '_' . time();
    }

    /**
     * 验证 Api_token 是否过期
     * @param $api_token
     * @return bool
     */
    public static function validateApiToken($api_token)
    {
        if (empty($api_token)) {
            return false;
        }
        $timestamp = (int)substr($api_token, strrpos($api_token, '_') + 1);
        $expire = Yii::$app->params['user.apiTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->app_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->app_key === $authKey;
    }

    /**
     * @return bool 是否开启Token有效期验证
     */
    public static function validateTokenExpire()
    {
        $expire = Yii::$app->params['user.apiTokenExpire'];
        return $expire > 0;
    }
}