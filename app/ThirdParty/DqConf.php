<?php
include_once 'DqLoader.php';
class DqConf{
    /**
     * 数据库配置
     */
    static $db=array(
        'host'=>'127.0.0.1',
        'port'=>'3306',
        'user'=>'kirineko',
        'password'=>'as798360687',   
        'database'=>'dq',
    );

    /**
     * redis key前缀
     */
    static $prefix='dq_';
    static $bucket=2;


    //监听端口
    static $port=6879;
    static $queue_len=5000;
    static $socket_select_timeout=1;
    /*消息的最大长度*/
    static $msg_max_size=4096;
    static $http_port=8088;

    static $max_connection=2000;
    static $redis_ping_interval=100;
    static $flush_incr_interval=5;  /*缓存计数时间*/


    /**
     * 日志路径
     */
    static $logPath='/data1/www/logs/dq/';

    /**
     *进程名称不能包含空格
     */
    const DQ_MASTER        = 'dq-master';
    const DQ_SERVER        = 'dq-server';
    const DQ_TIMER         = 'dq-timer';
    const DQ_CONSUME       = 'dq-consume';
    const DQ_REDIS_CHECKER = 'dq-redis-checker';
    const DQ_HTTP_SERVER   = 'dq-http-server';

    /**
     * 消费进程数
     */
    static $consume_nums=10;

    //异常重复通知次数
    static $notify_exp_nums=10;

    static $config_refresh=300;  //添加redis组，和topic之后，生效时长

    /**
     * php bin文件路径
     */
    static $phpBin='/usr/bin/php';


    //队列优先级设置
    const TASK_PRIORITY_HIGH=1;
    const TASK_PRIORITY_NORMAL=2;
    const TASK_PRIORITY_LOW=3;
    static $priorityConfig=array(
        self::TASK_PRIORITY_HIGH=>5,
        self::TASK_PRIORITY_NORMAL=>3,
        self::TASK_PRIORITY_LOW=>2,
    );

    static $priorityName=array(
        self::TASK_PRIORITY_HIGH=>'高',
        self::TASK_PRIORITY_NORMAL=>'中',
        self::TASK_PRIORITY_LOW=>'低',
    );


    /**
     * 从数据库中读取redis的配置信息
     */
    static $redis=array();
    public static function getRedisServer(){
        static $time=0;
        if(empty($time) || time()-$time>self::$config_refresh || empty(self::$redis)) {
            $arr = DqMysql::select('dq_redis');
            $ret = array();
            foreach ($arr as $v) {
                $tmp = array();
                $parts = explode(',', $v['t_content']);
                list($host, $port, $auth) = self::parse_config($parts[0]);
                $tmp['master'] = array('host' => $host, 'port' => $port, 'auth' => $auth,'id'=>$v['id']);
                if (isset($parts[1])) {
                    list($host, $port, $auth) = self::parse_config($parts[1]);
                    $tmp['slave'] = array('host' => $host, 'port' => $port, 'auth' => $auth,'id'=>$v['id']);
                }
                $ret[] = $tmp;
            }
            $time = time();
            self::$redis = $ret;
        }
        return self::$redis;
    }

    /**
     * 解析redis配置
     */
    public static function parse_config($part){
        $segInfo = explode(':',$part);
        return array(trim($segInfo[0]),trim($segInfo[1]),trim(isset($segInfo[2])?$segInfo[2]:''));
    }

    public static function getListenPort(){
        return self::$port;
    }

    public static function getListenQueueLen(){
        return self::$queue_len;
    }

    public static function getLogDir(){
        return self::$logPath;
    }

    public static function getRedisMaster(){
        return self::$redis['master'];
    }

    public static function getRedisSlave(){
        return self::$redis['slave'];
    }

    //获取本机ip
    public static function getLocalHost(){
        return '0.0.0.0';
    }

    public static function  get_socket_select_timeout(){
        return self::$socket_select_timeout;
    }

    public static function get_msg_max_size(){
        return self::$msg_max_size;
    }

}
