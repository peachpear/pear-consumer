<?php
namespace console\workers;

use Yii;
use common\service\LogService;

/**
 * 该类基本没用到
 * Class BaseWorker
 * @package console\workers
 */
class BaseWorker
{
    /** @var \AMQPqueue $queue */
    protected $queue;      // 队列
    /** @var \AMQPenvelope $envelope */
    protected $envelope;           // 消息对象
    protected $msg;                // 消息内容

    /**
     * 初始化消息
     * @param type $envelope
     * @param type $queue
     * @return type
     */
    public function initMessage($envelope, $queue)
    {
        $this->queue = $queue;
        $this->envelope = $envelope;

        $msg = $this->envelope->getBody();

        $this->msg = json_decode($msg, TRUE);

        return  $this->msg;
    }

    /**
     * 队列结束处理
     * @param bool $result
     * @return bool
     * @throws \Exception
     */
    public function close($result = true)
    {
        $queue_name = $this->queue->getName();

        // 结果为true时不记录队列日志数据
        if ($result !== true) {
            // 日志记录数据
            $log = [
                'queue_name' => $queue_name,
                'msg' => $this->msg,
                'result' => $result,
            ];
            Yii::error($log);
        }

        // code 定义在500~599，队列抛出异常，结束消费
        if( isset($result['code']) && ($result['code'] >= 500 && $result['code'] <= 599) ) {    //异常队列结束
            throw new \Exception($queue_name . $result['msg']);
        }

        Yii::$app->db->close();
        return $this->queue->ack($this->envelope->getDeliveryTag());
    }
}