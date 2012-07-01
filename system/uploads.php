<?php
/**
 * File Upload Helpers
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Checks a file to be sure it is valid
 *
 * @param string $path File path
 * @param string $name File name
 * @param string $ext File extension
 * @return bool
 */
function cot_file_check($path, $name, $ext)
{
	global $L, $cfg;
	if ($cfg['pfsfilecheck'])
	{
		require './datas/mimetype.php';
		$fcheck = FALSE;
		if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif')))
		{
			switch($ext)
			{
				case 'gif':
					$fcheck = @imagecreatefromgif($path);
				break;

				case 'png':
					$fcheck = @imagecreatefrompng($path);
				break;

				default:
					$fcheck = @imagecreatefromjpeg($path);
				break;
			}
			$fcheck = $fcheck !== FALSE;
		}
		else
		{
			if (!empty($mime_type[$ext]))
			{
				foreach ($mime_type[$ext] as $mime)
				{
					$content = file_get_contents($path, 0, NULL, $mime[3], $mime[4]);
					$content = ($mime[2]) ? bin2hex($content) : $content;
					$mime[1] = ($mime[2]) ? strtolower($mime[1]) : $mime[1];
					$i++;
					if ($content == $mime[1])
					{
						$fcheck = TRUE;
						break;
					}
				}
			}
			else
			{
				$fcheck = ($cfg['pfsnomimepass']) ? 1 : 2;
				cot_log(sprintf($L['pfs_filechecknomime'], $ext, $name), 'sec');
			}
		}
		if (!$fcheck)
		{
			cot_log(sprintf($L['pfs_filecheckfail'], $ext, $name), 'sec');
		}
	}
	else
	{
		$fcheck = true;
	}
	return($fcheck);
}

/**
 * Returns maximum size for uploaded file, in KiB (allowed in php.ini, and may be allowed in .htaccess)
 *
 * @return int
 */
function cot_get_uploadmax()
{
	static $par_a = array('upload_max_filesize', 'post_max_size', 'memory_limit');
	static $opt_a = array('G' => 1073741824, 'M' => 1048576, 'K' => 1024);
	$val_a = array();
	foreach ($par_a as $par)
	{
		$val = ini_get($par);
		$opt = strtoupper($val[strlen($val) - 1]);
		$val = isset($opt_a[$opt]) ? $val * $opt_a[$opt] : (int)$val;
		if ($val > 0)
		{
			$val_a[] = $val;
		}
	}
	return floor(min($val_a) / 1024); // KiB
}

/**
 * Strips all unsafe characters from file base name and converts it to latin
 *
 * @param string $basename File base name
 * @param bool $underscore Convert spaces to underscores
 * @param string $postfix Postfix appended to filename
 * @return string
 */
function cot_safename($basename, $underscore = true, $postfix = '')
{
	global $lang, $cot_translit;
	if(!$cot_translit && $lang != 'en' && file_exists(cot_langfile('translit', 'core')))
	{
		require_once cot_langfile('translit','core');
	}

	$fname = mb_substr($basename, 0, mb_strrpos($basename, '.'));
	$ext = mb_substr($basename, mb_strrpos($basename, '.') + 1);
	if($lang != 'en' && is_array($cot_translit))
	{
		$fname = strtr($fname, $cot_translit);
	}
	if($underscore) $fname = str_replace(' ', '_', $fname);
	$fname = preg_replace('#[^a-zA-Z0-9\-_\.\ \+]#', '', $fname);
	$fname = str_replace('..', '.', $fname);
	if(empty($fname)) $fname = cot_unique();
	return $fname . $postfix . '.' . mb_strtolower($ext);
}
?>