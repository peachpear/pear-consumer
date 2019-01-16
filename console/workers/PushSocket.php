<?php
namespace console\workers;

use common\widgets\WSClient;
use Yii;

class PushSocket extends BaseWorker
{
    private $socket;

    public function __construct()
    {
        // 实例化websocket客户端
        $this->socket = new WSClient(
            Yii::$app->params['chat_socket'],
            '/',
            false,
            Yii::$app->params['chat_port']
        );

        if (!$this->socket->connect(true)) {
            die('connect failed');
        }
    }

    public function run($envelope, $queue)
    {
        $msg = $envelope->getBody();

        // 向websocket服务器发送数据
        if (!$this->socket->send(WS_FRAME_TEXT, $msg, 1)) {
            echo $this->socket->errstr . "\n";
            die;
        }

        $queue->ack($envelope->getDeliveryTag());
    }
}