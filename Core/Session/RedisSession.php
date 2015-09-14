<?php
use Predis\Client;

class RedisSession
{
    private $_config;
    const REDIS_FILE = '/config/redis.php';
    const SESSION_FILE = '/config/session.php';
    private $_redis;
    private static $_instance = null;

    public static function init()
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self ();
        } else {
            throw new Exception ('RedisSession already initialized');
        }
        return self::$_instance;
    }

    private function __construct()
    {

        $this->_config = require BASE_PATH . self::SESSION_FILE;


        if ($this->_init()) {

            ini_set('session.auto_start', 0);
            ini_set('session.gc_probability', 0);
            ini_set('session.gc_divisor', 0);


//            session_name($this->_config ['session_name']);
//            session_cache_limiter ( 'nocache' );
//            session_set_cookie_params ( $this->_config ['lifetime'], $this->_config ['cookie_path'], $this->_config ['cookie_domain'] );

        } else {
            throw new Exception ('Cannot initiliaze Redis Session');
        }
    }

    private function _init()
    {
        $this->_redis = new Client(require BASE_PATH . self::REDIS_FILE);
        return $this->_redis;
    }

    public function open($path, $sessionName)
    {
        $this->_config ['keyprefix'] = $sessionName . '::';
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($sessionId)
    {
        $_SESSION = json_decode($this->_redis->get($this->_buildKey($sessionId)), true);
        if (isset($_SESSION) && $_SESSION != NULL) {
            return session_encode();
        }
        return FALSE;
    }


    public function write($sessionId)
    {
//        $this->_redis->setnx($this->_buildKey($sessionId), json_encode($_SESSION));
//        $this->_redis->expire($this->_buildKey($sessionId), $this->_config ['lifetime']);
        $this->_redis->setex($this->_buildKey($sessionId), $this->_config ['lifetime'], json_encode($_SESSION));
        return true;
    }

    public function destroy($sessionId)
    {
        $this->_redis->delete($this->_buildKey($sessionId));
        session_destroy();
        return true;
    }

    public function gc()
    {
        return true;
    }

    private function _buildKey($id)
    {
        return $this->_config ['keyprefix'] . $id;
    }
}