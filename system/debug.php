<?php
/*
 * ================================ Debugging Facilities ================================
 */

/**
 * Accepts several variables and prints their values in debug mode (var dump).
 *
 * @example sed_assert($foo, $bar);
 * @see sed_watch(), sed_backtrace(), sed_vardump()
 */
function sed_print()
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
 * @example sed_watch($foo, $bar);
 * @see sed_assert(), sed_checkpoint(), SED_DEBUG_LOGFILE
 */
function sed_watch()
{
	$fp = fopen(SED_DEBUG_LOGFILE, 'a');
	$btrace = debug_backtrace();
	fputs($fp, $btrace[1]['file'].', '.$btrace[1]['line'].":\n");
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
 * @see sed_assert(), sed_vardump()
 */
function sed_backtrace($clear_screen = TRUE)
{
	if ($clear_screen)
	{
		ob_end_clean();
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
 * @see SED_VARDUMP_LOCALS, sed_assert(), sed_backtrace()
 */
function sed_vardump($clear_screen = TRUE)
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
 * @example eval(SED_VARDUMP_LOCALS);
 * @see sed_vardump(), sed_watch()
 */
define('SED_VARDUMP_LOCALS', 'ob_end_clean();
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
 * @see SED_CHECKPOINT_LOCALS, SED_DEBUG_LOGFILE, sed_watch(), sed_vardump()
 */
function sed_checkpoint()
{
	$fp = fopen(SED_DEBUG_LOGFILE, 'a');
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
 * @example eval(SED_CHECKPOINT_LOCALS);
 * @see sed_checkpoint(), SED_DEBUG_LOGFILE, sed_watch(), SED_VARDUMP_LOCALS
 */
define('SED_CHECKPOINT_LOCALS', '$debug_fp = fopen(SED_DEBUG_LOGFILE, "a");
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