<?php

namespace oom\api\admin\apis;

use oom\api\models\APIAuthUser;
use luya\admin\ngrest\base\Api;
use luya\helpers\Url;
use Yii;
use yii\web\ServerErrorHttpException;

/**
 * Apiauth User Controller.
 *
 * File has been created with `crud/create` command.
 */
class ApiAuthUserController extends Api
{
    /**
     * @var string The path to the model which is the provider for the rules and fields.
     */
    public $modelClass = 'oom\api\models\APIAuthUser';


    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        return $actions;
    }

    /**
     * @return \yii\db\ActiveRecord
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {

        $model = new APIAuthUser();

        $model->generateAppKey();
        $model->generateAppSecret();
        $model->generateApiToken();

        $model->created_at = time();
        $model->updated_at = time();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute([Url::current(), 'id' => $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }
}