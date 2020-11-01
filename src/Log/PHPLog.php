<?php
namespace Utils\Log;
/**
 * 使用PHP的error_log记录日志
 * @author Tianfeng.Han
 *
 */
class PHPLog extends \Utils\Log implements \Utils\IFace\Log
{
    protected $logput;
    protected $type;
    protected $put_type = array('file' => 3, 'sys' => 0, 'email' => 1);

    function __construct($config)
    {
        if (isset($config['logput']))
        {
            $this->logput = $config['logput'];
        }
        if (isset($config['type']))
        {
            $this->type = $this->put_type[$config['type']];
        }
        parent::__construct($config);
    }

    function put($msg, $level = self::INFO)
    {
//        if (\Server_Tcp::TCP_DEBUG === false) return;

        $msg = $this->format($msg, $level);
        if ($msg) error_log($msg, $this->type, $this->logput);
    }
}