<?php
namespace console\workers;

use Yii;

class Log
{
    /**
     * 一般日志信息由Logstash从mq拿信息写入Elasticsearch
     * @param $envelope
     * @param $queue
     */
    public function run($envelope, $queue)
    {
        $msg = $envelope->getBody();
        $queue->ack($envelope->getDeliveryTag());
    }
}