<?php
/**
 * Created by PhpStorm.
 * User: OOM-Administrator
 * Date: 2018/4/23
 * Time: 14:22
 */

namespace oom\api\components\exception;


use yii\base\Exception;

class ApiParamsException extends Exception
{

    public function getName()
    {
        return 'ApiParamsException';
    }

}