<?php

namespace luya\apiauth\models;

use Yii;
use yii\base\Exception;
use yii\web\Link;
use yii\helpers\Url;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\filters\RateLimitInterface;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "api_auth_user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property int $allowance 剩余的允许的请求数量
 * @property int $allowance_updated_at 有效期UNIX时间戳数
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string api_token
 */
class ApiAuthUser extends ActiveRecord implements IdentityInterface, RateLimitInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_auth_user';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'app_key', 'api_token', 'app_secret', 'email', 'created_at', 'updated_at'], 'required'],
            [['allowance', 'allowance_updated_at', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'app_key'], 'string', 'max' => 32],
            [['app_secret', 'api_token', 'app_secret_reset_token', 'email'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'username' => 'Username',
            'app_key' => 'Auth Key',
            'api_token' => 'Api Token',
            'app_secret' => 'APP Secret',
            'app_secret_reset_token' => 'APP Secret Reset Token',
            'allowance' => 'Allowance',
            'allowance_updated_at' => 'Allowance Updated At',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return array
     * 可以屏蔽掉一些敏感字段
     */
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['app_key'], $fields['app_secret'], $fields['app_secret_reset_token']);
        return $fields;
    }

    /**
     * {@inheritdoc}
     *
     * The default implementation returns the names of the relations that have been populated into this record.
     * @return array
     */
    public function extraFields()
    {
        return ['status'];
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
    public function generatePasswordHash()
    {
        $this->app_secret = Yii::$app->security->generateRandomString();
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
        $this->api_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * @param $app_secret
     * @return bool
     */
    public function validatePassword($app_secret)
    {
        return $this->app_secret === $app_secret;
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
        // TODO: Implement getAuthKey() method.
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
        // TODO: Implement validateAuthKey() method.
    }
}