<?php
/**
 * GZIP-compressed resource output and cache control utility
 * Used by static resource consolidation and cache
 *
 * @package Cotonti
 * @version 0.9.4
 * @author Julien Lecomte, massively modified by Cotonti Team
 * @link http://www.julienlecomte.net/blog/2007/08/13/
 * @license BSD
 */

define('COT_CODE', true);

// Required for PHP 5.3
require_once './datas/config.php';
date_default_timezone_set('GMT');

/*
 * Get the path of the target file.
 */
if (isset($_GET['rc']) && preg_match('#^[\w\.\-]+\.(js|css)$#', $_GET['rc'], $mt))
{
	$src_uri = $cfg['cache_dir'] . '/static/' . $_GET['rc'];
	$content_type = $mt[1] == 'js' ? 'text/javascript' : 'text/css';
}
else
{
	$protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
	header($protocol . ' 400 Bad Request');
	echo '<html><body><h1>HTTP 400 - Bad Request</h1></body></html>';
	exit;
}

/*
 * Verify the existence of the target file.
 * Return HTTP 404 if needed.
 */

if (!file_exists($src_uri))
{
	$protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
	header($protocol . ' 404 Not Found');
	echo '<html><body><h1>HTTP 404 - Not Found</h1></body></html>';
	exit;
}

/*
 * Set the HTTP response headers that will
 * tell the client to cache the resource.
 */

$file_last_modified = filemtime($src_uri);
header('Last-Modified: '.date('r', $file_last_modified));

$max_age = 5 * 365 * 24 * 60 * 60; // ~5 years

$expires = $file_last_modified + $max_age;
header('Expires: '.date('r', $expires));

$etag = md5(realpath($src_uri) . filesize($src_uri) . filemtime($src_uri));
header('ETag: ' . $etag);

$cache_control = 'must-revalidate, proxy-revalidate, max-age='.$max_age.', s-maxage='.$max_age;
header('Cache-Control: '.$cache_control);
header('Vary: Accept-Encoding');

/*
 * Check if the client should use the cached version.
 * Return HTTP 304 if needed.
 */

if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
{
	// convert to unix timestamp
	$if_modified_since = strtotime(preg_replace('#;.*$#', '', stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE'])));
}
else
{
	$if_modified_since = false;
}

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) == $etag
	&& $if_modified_since >= $file_last_modified)
{
	$protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
	header($protocol . ' 304 Not Modified');
	exit;
}

/*
 *  Cotonti Static Resources Cache
 */
header('Content-Type: '.$content_type);
readfile($src_uri);

// Gzip compression of CSS and JS files is usually enabled in webserver configuration.

// if (@strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') === FALSE)
// {
// 	readfile($src_uri);
// }
// else
// {
// 	header('Content-Encoding: gzip');
// 	if (!file_exists($src_uri . '.gz'))
// 	{
// 		file_put_contents($src_uri . '.gz', gzencode(file_get_contents($src_uri)));
// 	}
// 	readfile($src_uri . '.gz');
// }

?>