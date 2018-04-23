<?php
/**
 * Created by PhpStorm.
 * User: OOM-Administrator
 * Date: 2018/4/16
 * Time: 10:37
 */

namespace oom\api\frontend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use oom\components\utils\ApiParamAuth;

class BaseApiController extends Controller
{

    public $response;

    /**
     * @return array
     */
    public function behaviors()
    {
        $this->response = Yii::$app->response;
        $this->response->format = Response::FORMAT_JSON;

        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => ApiParamAuth::className(),
                'tokenParam' => 'token',
                'optional' => [
                    'token',
                    'signup-test'
                ],
            ]
        ]);

    }

}