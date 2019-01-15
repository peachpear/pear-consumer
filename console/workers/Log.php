<?php
namespace console\workers;

use Yii;

class Log
{
    /**
     * logstash处理
     * @param $envelope
     * @param $queue
     */
    public function run($envelope, $queue)
    {
        $msg = $envelope->getBody();
        $queue->ack($envelope->getDeliveryTag());
    }
}