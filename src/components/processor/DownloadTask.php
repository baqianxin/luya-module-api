<?php

namespace oom\api\components\processor;

use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Created by PhpStorm.
 * User: OOM-Administrator
 * Date: 2018/4/19
 * Time: 17:28
 * 下载文件的任务处理器
 */
class DownloadTask extends BaseObject implements JobInterface
{
    public $url;
    public $file;

    /**
     * @param \yii\queue\Queue $queue
     */
    public function execute($queue)
    {
        file_put_contents($this->file, file_get_contents($this->url));
    }
}