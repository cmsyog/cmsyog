<?php
use Predis\Client;
class RedisSession {
    private $_config;
    const REDIS_FILE = '/config/redis.php';
    const SESSION_FILE = '/config/session.php';
    private $_redis;
    private $_id = '';
    private static $_instance = null;
    public static function init ( $config = array() ) {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self ( $config );
        } else {
            throw new Exception ( 'RedisSession already initialized' );
        }
    }
    private function __construct ( $config = array() ) {
        if (!empty ( $config )) {
            if (!( isset ( $config ['cookie_path'] ) && isset ( $config ['cookie_domain'] )
                && isset ( $config ['session_name'] )
                && isset ( $config ['lifetime'] ) && is_int ( $config ['lifetime'] )
                && isset ( $config ['server'] ) && is_array ( $config ['server'] ) && isset ( $config ['server'] ['host'] ) && isset ( $config ['server'] ['port'] ) && is_int ( $config ['server'] ['port'] )
            )) {
                throw new Exception ( 'Bad configuration, see documentation' );
            }
            $this->_config =  require BASE_PATH.self::SESSION_FILE;
        }

        if ($this->_init ()) {
            session_set_save_handler ( array (
                &$this,
                'open' ), array (
                &$this,
                'close' ), array (
                &$this,
                'read' ), array (
                &$this,
                'write' ), array (
                &$this,
                'destroy' ), array (
                &$this,
                'gc' ) );

            ini_set ( 'session.auto_start', 0 );
            ini_set ( 'session.gc_probability', 0 );
            ini_set ( 'session.gc_divisor', 0 );

            session_cache_limiter ( 'nocache' );
            session_set_cookie_params ( $this->_config ['lifetime'], $this->_config ['cookie_path'], $this->_config ['cookie_domain'] );

            session_name ( $this->_config ['session_name'] );
            session_start ();
        } else {
            throw new Exception ( 'Cannot initiliaze Redis Session' );
        }
    }
    private function _init () {
        $this->_redis = new Client(require BASE_PATH.self::REDIS_FILE);
        return $this->_redis;
    }
    public function open ( $savePath, $sessionName ) {
        $this->_config ['keyprefix'] = $sessionName . '::';
        return true;
    }
    public function close () {
        return true;
    }
    public function read ( $id ) {
        return $this->_redis->get ( $this->_buildKey ( $id ) );
    }
    public function write ( $id, $data ) {
        if ($this->_id !== $id) {
            if ($this->_redis->getSet ( $this->_buildKey ( $id ), $data ) === false) {
                $this->_redis->expire ( $this->_buildKey ( $id ), $this->_config ['lifetime'] );
            }
            $this->_id = $id;
        } else {
            $this->_redis->set ( $this->_buildKey ( $id ), $data );
        }
        return true;
    }
    public function destroy ( $id ) {
        $this->_redis->delete ( $this->_buildKey ( $id ) );
        session_destroy ();
        return true;
    }
    public function gc () {
        return true;
    }
    private function _buildKey ( $id ) {
        return $this->_config ['keyprefix'] . $id;
    }
}