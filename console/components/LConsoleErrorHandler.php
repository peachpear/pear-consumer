<?php
namespace console\components;

use common\components\LException;
use common\components\LRabbitQueue;
use Yii;
use yii\console\ErrorHandler;

/**
 * ErrorHandler处理类
 * Class LConsoleErrorHandler
 * @package console\components
 */
class LConsoleErrorHandler extends ErrorHandler
{
    public $sendTo;  // component初始化时会根据config自动赋值，无需init重复赋值
    public $sendCC;

    /**
     * 渲染异常
     * @param \Exception $exception
     */
    public function renderException($exception)
    {
        $this->handleException($exception);
    }

    /**
     * 错误处理
     */
    public function handleError($code, $message, $file, $line)
    {
        $exception =  new \ErrorException($message, $code, 1, $file, $line);

        $this->logException($exception);

        $this->sendErrorMsg($this->formatException($exception));
    }

    /**
     * 致命错误处理
     */
    public function handleFatalError()
    {
        $error = error_get_last();
        if (LException::isFatalError($error)) {
            $exception = new \ErrorException($error['message'], 500, $error['type'], $error['file'], $error['line']);
            $this->exception = $exception;
//            $this->logException($exception);

            if ($this->discardExistingOutput) {
                $this->clearOutput();
            }
            // need to explicitly flush logs because exit() next will terminate the app immediately
            Yii::getLogger()->flush(true);

            $this->handleException($exception);
        }
    }

    /**
     * 异常处理
     * @param $exception
     */
    public function handleException( $exception )
    {
        $data = $this->formatException( $exception );

        $this->logException( $exception );

        if ( YII_DEBUG ) {
            throw $exception;
        } else {
            // 发邮件
            $this->sendErrorMsg( $data );
        }
    }

    /**
     * 格式化异常
     * @param $exception
     * @return array
     */
    protected function formatException($exception)
    {
        $fileName = $exception->getFile();
        $errorLine = $exception->getLine();

        $trace = $exception->getTrace();

        foreach ($trace as $i => $t)
        {
            if (!isset($t['file'])) {
                $trace[$i]['file'] = 'unknown';
            }

            if (!isset($t['line'])) {
                $trace[$i]['line'] = 0;
            }

            if (!isset($t['function'])) {
                $trace[$i]['function'] = 'unknown';
            }

            unset($trace[$i]['object']);
        }

        return array(
            'type' => get_class($exception),
            'errorCode' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file' => $fileName,
            'line' => $errorLine,
            'trace' => $exception->getTraceAsString(),
//            'traces' => $trace,
        );
    }

    /**
     * 发邮件（通过推入队列实现）
     * @param $data
     * @internal param $exception
     */
    public function sendErrorMsg( $data )
    {
        /** @var LRabbitQueue $queue */
        $queue = Yii::$app->get("queue");
        $params = [
            'send_to' => $this->sendTo,
            'cc_to' => $this->sendCC,
            'text' => json_encode( $data ),
            'title' => "[".ENV.']cli-exception-error',
            'file' => []
        ];

        $queue->produce($params, 'async', 'mail');
    }
}