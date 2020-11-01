<?php
/**
 * Redis日志记录类
 * User: Frank
 * Date: 18/10/11
 * Time: 下午2:48
 */

namespace Utils\Log;

use Cache\RedisSock;

class RedisLog extends \Utils\Log implements \Utils\IFace\Log
{
    protected $ip = null;
    protected $port = 0;
    protected $timeout = 0;
    protected $db = 0;
    protected $redis = null;
    protected $hm_table = null;

    const CACHE_LOG_KEY = 'cache_log_queue';
    static $date_format = 'Y-m-d H:i:s';

    function __construct($config = array())
    {
        /*if (isset($config['ip']))
        {
            $this->ip = $config['ip'];
        }
        if (isset($config['port']))
        {
            $this->port = $config['port'];
        }
        if (isset($config['timeout']))
        {
            $this->timeout = $config['timeout'];
        }
        if (isset($config['pawd']))
        {
            $this->pawd = $config['pawd'];
        }
        if (isset($config['db']))
        {
            $this->db = $config['db'];
        }
        parent::__construct($config);*/
    }

    public function format($msg, $level)
    {
        $level = self::convert($level);
        if ($level < $this->level_line)
        {
            return false;
        }
        //$level_str = self::$level_str[$level];

        $data = array(
            'date' => time(),  //date(self::$date_format),
            'level' => $level, //$level_str,
            'msg' => $msg
        );
        return json_encode($data);
    }

    /**
     * 写日志方法
     * @param string $msg
     * @param int $level
     * @desc RedisHash表格式说明： hmset(<设备唯一标识>, <日志内容>)
     * @example
     * LPUSH key value [value ...]  从队列的左边入队一个或多个元素
     * lpush queue a
     * lpush queue e f g
     *
     * LRANGE key start stop 从列表中获取指定返回的元素，负数表示从右向左数
     * lrange queue 0 -1
     *
     * RPOP key 从队列的右边出队一个元素并在队列中删除
     * rpop queue
     *
     * LLEN key 查询key的数量
     *
     * DEL key 删除队列
     * del queue
     */
    public function put($msg, $level = self::INFO)
    {
        $msg = $this->format($msg, $level);
        return RedisSock::lpush(self::CACHE_LOG_KEY, $msg);
    }

    public function create()
    {
        $sql = <<<EOT
            CREATE TABLE IF NOT EXISTS `table_log` (
            `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `logtype` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '日志类型 TRACE:0,INFO:1,NOTICE:2,WARN:3,ERROR:4',
            `addtime` int(10) NOT NULL,
            `msg` varchar(255) NOT NULL COMMENT '内容',
            `save_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='跟踪日志表';
EOT;
    }
}