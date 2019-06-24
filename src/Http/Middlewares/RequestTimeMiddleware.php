<?php

 namespace WecarSwoole\Http\Middlewares;

 use EasySwoole\Http\Request;
 use EasySwoole\Http\Response;
 use EasySwoole\Http\Message\Uri;
 use Psr\Log\LoggerInterface;
 use WecarSwoole\Http\Controller;

 /**
  * 接口请求超时预警
  * Class RequestTimeMiddleware
  * @package WecarSwoole\Http\Middlewares
  */
class RequestTimeMiddleware implements IControllerMiddleware
{
    protected $startTime;
    protected $requestId;
    protected $threshold;
    protected $redis;
    protected $logger;
    protected $percent;

    /**
     * RequestTimeMiddleware constructor.
     * @param \Redis $redis
     * @param int $threshold 超时阀值，单位秒
     * @param float $percent 5分钟内超过阀值的请求数占比
     */
    public function __construct(\Redis $redis, LoggerInterface $logger, int $threshold = 3, float $percent = 0.2)
    {
        $this->threshold = $threshold;
        $this->redis = $redis;
        $this->percent = $percent;
        $this->logger = $logger;
    }

    public function before(Controller $controller, Request $request, Response $response)
    {
        $this->startTime = time();
        $this->requestId = $this->requestKey($request);
        return true;
    }

    public function after(Controller $controller, Request $request, Response $response)
    {
        $stats = $this->redis->get($this->requestId);

        if (!$stats) {
            $stats = $this->initStatsInfo();
        } else {
            // 如果超过5分钟，则清零，并检查是否需要发送告警日志
            $stats = json_decode($stats, true);
            if ($stats['time'] < time() - 60 * 5) {
                $this->log($stats, $request);
                $stats = $this->initStatsInfo();
            }
        }

        $duration = time() - $this->startTime;
        if ($duration >= $this->threshold) {
            $stats['cnt']++;
        }
        $stats['total']++;

        $this->redis->set($this->requestId, json_encode($stats), 60 * 10);
    }

    public function gc()
    {
        $this->startTime = null;
        $this->requestId = null;
    }

    private function requestKey(Request $request)
    {
        $info = [
            $this->urlStr($request->getUri()),
            $request->getMethod(),
        ];
        return 'request-' . md5(implode('-', $info));
    }

    private function log(array $stats, Request $request)
    {
        $per = $stats['cnt'] / $stats['total'];
        if ($per < $this->percent) {
            return;
        }

        $this->logger->critical(
            "请求超时告警。url：" . $this->urlStr($request->getUri()) .
            "5分钟内超过{$this->threshold}s的请求有{$stats['cnt']}条，占总请求" . (number_format($per * 100)) . "%"
        );
    }

    private function initStatsInfo(): array
    {
        return [
            'cnt' => 0,
            'total' => 0,
            'time' => time(),
        ];
    }

    private function urlStr(Uri $uri): string
    {
        return $uri->getScheme() . '://' . $uri->getHost() . ':' . $uri->getPort() . $uri->getPath();
    }
}
