<?php
/**
 * Created by PhpStorm.
 * User: OOM-Administrator
 * Date: 2018/4/16
 * Time: 10:44
 */

namespace oom\api\utils;

use yii\filters\auth\AuthMethod;


class ApiParamAuth extends AuthMethod
{
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'token';

    /**
     * {@inheritdoc}
     */
    public function handleFailure($response)
    {
        $response->data =  ['error'=>'错误的访问令牌，请重新获取ACCESS_TOKEN'];
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate($user, $request, $response)
    {
        $accessToken = $request->get($this->tokenParam);
        if (is_string($accessToken)) {
            $identity = $user->loginByAccessToken($accessToken, get_class($this));
            if ($identity !== null) {
                return $identity;
            }
        }
        if ($accessToken !== null) {
            $this->handleFailure($response);
        }

        return null;
    }
}
