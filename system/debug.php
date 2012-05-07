<?php
/**
 * Debugging Facilities
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

/**
 * Accepts several variables and prints their values in debug mode (var dump).
 *
 * @example cot_assert($foo, $bar);
 * @see cot_watch(), cot_backtrace(), cot_vardump()
 */
function cot_print()
{
	ob_end_clean();
	$vars = func_get_args();
	foreach ($vars as $name => $var)
	{
		var_dump($var);
	}
	die();
}

/**
 * Dumps current state of its arguments to debug log file and continues normal script execution.
 *
 * @global string $cfg['debug_logpath'] Path to debug log file
 * @example cot_watch($foo, $bar);
 * @see cot_assert(), cot_checkpoint()
 */
function cot_watch()
{
	global $cfg;
	$fp = fopen($cfg['debug_logpath'] . '/cot_debug_' . date('Ymd_His') . '.log', 'a');
	$btrace = debug_backtrace();
	fputs($fp, $btrace[0]['file'].', '.$btrace[0]['line'].":\n");
	$vars = func_get_args();
	foreach ($vars as $name => $var)
	{
		fputs($fp, "arg #$name = ".print_r($var, TRUE)."\n");
	}
	fputs($fp, "----------------\n");
	fclose($fp);
}

/**
 * Prints program execution backtrace.
 *
 * @param bool $clear_screen If TRUE displays backtrace only. Otherwise it will be printed in normal flow.
 * @see cot_assert(), cot_vardump()
 */
function cot_backtrace($clear_screen = TRUE)
{
	if ($clear_screen)
	{
		ob_end_clean();
		cot_sendheaders('text/plain');
	}
	debug_print_backtrace();
	if ($clear_screen)
	{
		die();
	}
}

/**
 * Prints structure and contents of all global variables currently assigned.
 *
 * @param bool $clear_screen If TRUE displays vardump only. Otherwise it will be printed in normal flow.
 * @see COT_VARDUMP_LOCALS, cot_assert(), cot_backtrace()
 */
function cot_vardump($clear_screen = TRUE)
{
	if ($clear_screen)
	{
		ob_end_clean();
	}
	foreach ($GLOBALS as $key => $val)
	{
		if ($key != 'GLOBALS')
		{
			echo "<br /><em>$key</em><br />";
			var_dump($val);
		}
	}
	if ($clear_screen)
	{
		die();
	}
}

/**
 * Local vardump macro. Prints structure and contents of all variables in the local scope.
 *
 * @example eval(COT_VARDUMP_LOCALS);
 * @see cot_vardump(), cot_watch()
 */
define('COT_VARDUMP_LOCALS', 'ob_end_clean();
$debug_vars = get_defined_vars();
foreach ($debug_vars as $debug_key => $debug_val)
{
	if ($debug_key != "GLOBALS" && $debug_key != "debug_vars")
	{
		echo "<br /><em>$debug_key</em><br />";
		var_dump($debug_val);
	}
}
die();');

/**
 * Dumps current state of global variables into debug log file and continues normal script execution.
 *
 * @global string $cfg['debug_logpath'] Path to debug log file
 * @see COT_CHECKPOINT_LOCALS, cot_watch(), cot_vardump()
 */
function cot_checkpoint()
{
	global $cfg;
	$fp = fopen($cfg['debug_logpath'] . '/cot_debug_' . date('Ymd_His') . '.log', 'a');
	$btrace = debug_backtrace();
	fputs($fp, $btrace[1]['file'] . ', ' . $btrace[1]['line'] . ":\n");
	foreach ($GLOBALS as $key => $val)
	{
		if ($key != 'GLOBALS')
		{
			fputs($fp, "$key = " .print_r($val, TRUE) ."\n");
		}
	}
	fputs($fp, "----------------\n");
	fclose($fp);
}

/**
 * Dumps variables in local scope into debug log file and continues normal script execution.
 *
 * @global string $cfg['debug_logpath'] Path to debug log file
 * @example eval(COT_CHECKPOINT_LOCALS);
 * @see cot_checkpoint(), cot_watch(), COT_VARDUMP_LOCALS
 */
define('COT_CHECKPOINT_LOCALS', 'global $cfg;
	$debug_fp = fopen($cfg[\'debug_logpath\'] . \'/cot_debug_\' . date(\'Ymd_His\') . \'.log\', "a");
	$debug_btrace = debug_backtrace();
	fputs($debug_fp, $debug_btrace[0]["file"] . ", " . $debug_btrace[1]["line"] . ":\n");
	$debug_vars = get_defined_vars();
	foreach ($debug_vars as $debug_key => $debug_val)
	{
		if ($debug_key != "GLOBALS" && $debug_key != "debug_vars" && $debug_key != "debug_btrace" && $debug_key != "debug_fp")
		{
			fputs($debug_fp, "$debug_key = " .print_r($debug_val, TRUE) ."\n");
		}
	}
	fputs($debug_fp, "----------------\n");
	fclose($debug_fp);'
);

?>