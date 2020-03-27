<?php
namespace console\controllers;

use console\components\BaseController;
use Yii;

class ConsumerController extends BaseController
{
    /**
     * 项目根目录
     * @var string
     */
    private $root;

    /**
     * worker pidfile
     * @var string
     */
    private $pidfile;

    /**
     * worker进程pid
     * @var array
     */
    private $pids = [];

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
     * worker实例
     * @var object
     */
    private $worker;

    /**
     * consumer初始化
     * @param $consumer
     */
    private function initConsumer($consumer)
    {
        $this->consumer = ucfirst($consumer);

        $this->root = Yii::$app->params['root'];

        $this->pidfile = Yii::$app->params['pidfile_root'] .$this->consumer;

        $this->setting = Yii::$app->queue->credentials;

        $this->bindSetting = Yii::$app->params['C' .$this->consumer];

        $this->pids = file_exists($this->pidfile)
            ? explode(',', file_get_contents($this->pidfile))
            : null;
    }

    /**
     * 启动consumer-worker
     * @param $consumer
     */
    public function actionStart($consumer)
    {
        $this->initConsumer($consumer);

        $pid = pcntl_fork();

        if ($pid == -1) {
            die('could not fork');
        } elseif ($pid) {
            exit('parent process');
        } else {
            posix_setsid();

            if (file_exists($this->pidfile)) {
                file_put_contents($this->pidfile, ',' .posix_getpid(), FILE_APPEND);
            } else {
                file_put_contents($this->pidfile, posix_getpid());
            }

            $this->msgQueue();
        }
    }

    /**
     * 停止consumer-worker
     * @param $consumer
     */
    public function actionStop($consumer)
    {
        $this->initConsumer($consumer);

        if (empty($this->pids)) {
            echo "\n" .'worker not start' ."\n";
            exit();
        }

        foreach ($this->pids as $pid)
        {
            posix_kill($pid, SIGTERM);
        }

        unlink($this->pidfile);

        echo "\n" .'Stop Success' ."\n";
    }

    /**
     * 重启consumer-worker
     * @param $consumer
     */
    public function actionRestart($consumer)
    {
        $this->actionStop($consumer);

        sleep(2);

        $this->actionStart($consumer);
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
        $q->declareQueue();

        $q->bind(
            $this->bindSetting['exchange'],
            $this->bindSetting['routing']
        );

        if (!file_exists($this->root . '/workers/' . $this->consumer . '.php')) {
            echo "\n" ."workerService does not exist" ."\n";
            exit();
        }
        $workerName = 'console\\workers\\' . $this->consumer;

        $this->worker = new $workerName();

        $channel->qos(0,1);
        $q->consume(array($this->worker, 'run'));

        $conn->disconnect();
    }
}