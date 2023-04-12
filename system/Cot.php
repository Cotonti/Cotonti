<?php

/**
 * Main application class and facade to access key Cotonti globals regardless of scope
 */
class Cot
{
    /**
     * Cotonti cache
     * @var Cache
     */
    public static $cache;
    /**
     * Cotonti configuration
     * @var array
     */
    public static $cfg;
    /**
     * Database connection
     * @var CotDB
     */
    public static $db;
    /**
     * Database table name prefix
     */
    public static $db_x;
    /**
     * Environment settings
     * @var array
     */
    public static $env;
    /**
     * Extra fields
     * @var array
     */
    public static $extrafields;
    /**
     * Language strings
     * @var array
     */
    public static $L;
    /**
     * Pre-rendered output strings
     * @var array
     */
    public static $out;
    /**
     * Resource strings
     * @var array
     */
    public static $R;
    /**
     * Structure tree and properties array
     * @var array
     */
    public static $structure;
    /**
     * Temporary system variables
     * @var array
     */
    public static $sys;
    /**
     * Current user object
     * @var array
     */
    public static $usr;

    /**
     * Initializes static members. Call this function once all globals are defined.
     */
    public static function init()
    {
        global $cache, $cfg, $cot_extrafields, $db, $db_x, $env, $L, $out, $R, $structure, $sys, $usr;

        // Todo fill some variables with default values
        self::$cache       =& $cache;
        self::$cfg         =& $cfg;
        self::$db          =& $db;
        self::$db_x        =& $db_x;
        self::$env         =& $env;
        self::$extrafields =& $cot_extrafields;
        self::$L           =& $L;
        self::$out         =& $out;
        self::$R           =& $R;
        self::$structure   =& $structure;
        self::$sys         =& $sys;
        self::$usr         =& $usr;

        // Register core DB tables
        // On the first step of installer it is not initialized yet
        if (!(empty($db) && isset($env['location']) && $env['location'] == 'install')) {
            $db->registerTable('auth');
            $db->registerTable('cache');
            $db->registerTable('cache_bindings');
            $db->registerTable('core');
            $db->registerTable('config');
            $db->registerTable('groups');
            $db->registerTable('groups_users');
            $db->registerTable('logger');
            $db->registerTable('online');
            $db->registerTable('extra_fields');
            $db->registerTable('plugins');
            $db->registerTable('structure');
            $db->registerTable('updates');
            $db->registerTable('users');
        }
        // Fill some variables with default values
        // May be isset() is not needed
        if (!isset(self::$out['head'])) self::$out['head'] = '';
        if (!isset(self::$out['subtitle'])) self::$out['subtitle'] = '';
        if (!isset(self::$env['ext'])) self::$env['ext'] = null;
    }
}
