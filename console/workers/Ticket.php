<?php
namespace console\workers;

use common\service\TicketService;
use Yii;

class Ticket extends BaseWorker
{
    /**
     * 消费者run
     * @param $envelope
     * @param $queue
     * e.g.队列中一条数据是 {"event":"ticket_callback",
     * "data":{"type":"total_of_ticket",
     * "infos":{"user_id":"1474"}}}
     */
    public function run($envelope, $queue)
    {
//        $msg = parent::initMessage($envelope, $queue);
        $msg = $envelope->getBody();
        $msg = json_decode($msg, true);
        $event = isset($msg['event']) ?  $msg['event'] : '';

        switch ($event) {
            case 'ticket_callback':
                TicketService::callbackRequest( $msg['data'] );
                break;
            default:
                break;
        }

        Yii::$app->demoDB->close();
        $queue->ack($envelope->getDeliveryTag());
    }

}