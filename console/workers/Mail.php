<?php
namespace console\workers;

use common\service\MailService;
use Yii;

class Mail
{
    /**
     * @param $envelope
     * @param $queue
     */
    public function run($envelope, $queue)
    {
        $msg = $envelope->getBody();

        // 发送邮件
        MailService::sendMailOfQueue($msg);

        Yii::$app->demoDB->close();
        $queue->ack($envelope->getDeliveryTag());
    }
}