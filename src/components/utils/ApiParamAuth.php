<?php
/**
 * Created by PhpStorm.
 * User: OOM-Administrator
 * Date: 2018/4/16
 * Time: 10:44
 */

namespace oom\api\components\utils;

use oom\api\models\APIAuthUser;
use yii\filters\auth\AuthMethod;
use yii\web\Request;
use yii\web\Response;


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
        $response->data['code'] = '401';
        $response->data['error'] = 'ACCESS_TOKEN已经失效,请重新获取';
    }

    /**
     * Authenticates the current user.
     * @param APIAuthUser $user
     * @param Request $request
     * @param Response $response
     * {@inheritdoc}
     */
    public function authenticate($user, $request, $response)
    {
        $accessToken = $request->get($this->tokenParam);
        if (method_exists($user->identityClass, 'validateTokenExpire')
            && ($user->identityClass)::validateTokenExpire()) {
            if (method_exists($user->identityClass, 'validateApiToken')
                && !($user->identityClass)::validateApiToken($accessToken)) {
                $this->handleFailure($response);
                return null;
            }
        }
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
