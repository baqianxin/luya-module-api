<?php

namespace oom\api\components\request;

use oom\api\components\exception\ApiParamsException;
use oom\api\components\response\JSONResult;
use oom\api\components\exception\ApiNotFoundException;
use yii\base\Component;
use yii\base\Exception;

/**
 * Created by PhpStorm.
 * User: OOM-Administrator
 * Date: 2018/4/23
 * Time: 14:13
 */
class ApiService extends Component
{

    public static $jsonResult;

    /**
     * 定义接口名，指定回调方法
     * @var array $apiMethod
     */
    public $apiMethod = [];

    /**
     * 定义接口名，指定回调方法
     * @var array $apiMethod
     */
    public function setApiMethod($items = [])
    {
        $this->apiMethod = array_merge($this->apiMethod, $items);
    }

    public function getApiMethod()
    {
        return $this->apiMethod;
    }

    /**
     * 调用对应方法处理接口请求
     * @param $postData
     * @return mixed
     */
    public function handleRequest($postData)
    {
        try {

            if (!$postData || !isset($postData['method'])) {
                throw new ApiParamsException('api接口参数缺失');
            }
            $method = $postData['method'];
            if (isset($this->apiMethod[$method]) && method_exists($this, $this->apiMethod[$method])) {
                $method = $this->apiMethod[$method];
                return $this->$method($postData);
            }
            throw new ApiNotFoundException('请求接口不存在');
        } catch (Exception $e) {
            return new JSONResult([
                'status' => $e->getCode(),
                'error_code' => $e->getName(),
                'error_message' => $e->getMessage()
            ]);
        }
    }

}