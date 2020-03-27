<?php
namespace console\controllers;

use console\components\BaseController;
use Yii;

class DelayController extends BaseController
{
    /**
     * consumer
     * @var string
     */
    private $consumer;

    /**
     * rabbitMQ连接参数
     * @var array
     */
    private $setting = [];

    /**
     * worker对应queue,exchange,routing绑定配置
     * @var array
     */
    private $bindSetting = [];

    /**
     * dead-letter 交换机
     * @var string
     */
    private $x_exchange;

    /**
     * dead-letter routing-key
     * @var string
     */
    private $x_routing;

    /**
     * consumer初始化
     * @param $consumer
     */
    private function initConsumer($consumer)
    {
        $this->consumer = ucfirst($consumer);

        $this->setting = Yii::$app->queue->credentials;

        $this->bindSetting = Yii::$app->params['D' .$this->consumer];
    }

    /**
     * 启动consumer-worker
     * @param $consumer
     */
    public function actionStart($consumer)
    {
        $this->initConsumer($consumer);

        $this->msgQueue();
    }

    /**
     * 连接MQ并消费
     */
    private function msgQueue()
    {
        $conn = new \AMQPConnection($this->setting);

        if (!$conn->connect()) {
            echo "\n" ."Connect Failed" ."\n";
            exit();
        }
        echo "\n" ."Connect Success" ."\n";

        $channel = new \AMQPChannel($conn);

//        $ex = new \AMQPExchange($channel);
//        $ex->setName($this->bindSetting['exchange']);
//        $ex->setType(AMQP_EX_TYPE_DIRECT);
//        $ex->setFlags(AMQP_DURABLE);
//        $ex->declareExchange();

        $q = new \AMQPQueue($channel);
        $q->setName($this->bindSetting['queue']);
        $q->setFlags(AMQP_DURABLE);
        $q->setArguments([
            'x-dead-letter-exchange' => $this->bindSetting['x_exchange'],
            'x-dead-letter-routing-key' => $this->bindSetting['x_routing']
        ]);
        $q->declareQueue();

        $q->bind(
            $this->bindSetting['exchange'],
            $this->bindSetting['routing']
        );

        $conn->disconnect();
    }
}