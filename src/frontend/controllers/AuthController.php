<?php
/**
 * Created by PhpStorm.
 * User: OOM-Administrator
 * Date: 2018/4/13
 * Time: 16:45
 */

namespace oom\api\frontend\controllers;


use Yii;
use yii\helpers\Json;
use yii\web\IdentityInterface;
use oom\api\components\response\JSONResult;
use oom\api\models\APIAuthUser;
use oom\api\models\forms\ApiAuthForm;
use oom\api\frontend\services\AuthService;

class AuthController extends BaseApiController
{

    public $enableCsrfValidation = false;


    /**
     * @return array|string
     */
    public function actionIndex()
    {
        $authService = new AuthService();
        return $authService->handleRequest(Yii::$app->request->post());
    }

    public function actionToken()
    {
        $jsonResult = new JSONResult();
        $model = new ApiAuthForm();
        $model->setAttributes(Yii::$app->request->post());
        if ($user = $model->login()) {
            if ($user instanceof IdentityInterface) {
                $jsonResult->data = ['access-token' => $user->api_token];
            } else {
                $jsonResult->setErrorCode('102');
                $jsonResult->setErrorMessage($user->errors);
            }
        } else {
            $jsonResult->setErrorCode('103');
            $jsonResult->setErrorMessage($model->errors);
        }
        return $jsonResult;
    }

    /**
     * 添加测试用户
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionSignupTest()
    {
        $user = new APIAuthUser();
        $user->generateAppKey();
        $user->generateAppSecret();
        $user->generateApiToken();
        $user->username = 'oom';
        $user->email = 'oom@ghs.com';
        $user->save(false);

        return Json::encode([
            'code' => 200
        ]);
    }

}