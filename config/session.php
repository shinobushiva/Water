<?php

/**
 * Class SessionSetting
 */
 abstract class SessionSetting
 {
    protected $settings;

    public function __construct(array $settings) {
        $this->settings = $settings;
    }

    abstract public function initialize();
 }


class MemcachedSessionSetting extends SessionSetting
{
    public function initialize()
    {
        ini_set('session.save_handler', 'memcached');
        ini_set('session.save_path', $this->settings['save_path']);
        ini_set('session.gc_maxlifetime', $this->settings['maxlifetime']);
    }
}

 /**
  * Class SessionManager
  */
class SessionManager
{
    private static $setting;

    public static function configure(SessionSetting $setting)
    {
        self::$setting = $setting;
    }

    public static function start()
    {
        self::$setting->initialize();
        session_start();
    }

    public static function destroy()
    {
        session_destroy();
    }

    public static function regenerate($delete_old_session = false)
    {
        session_regenerate_id($delete_old_session);
    }
}


 /**
  * Class Session
  */
class Session
{
    public static function get($key)
    {
        return $_SESSION[$key];
    }

    public static function set($key, $val)
    {
        return $_SESSION[$key] = $val;
    }
}
