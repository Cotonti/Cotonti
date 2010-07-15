<?php
/**
 * GZIP-compressed resource output and cache control utility
 * Usage: rc.php?uri=js/filename.js
 *		rc.php?uri=skins/myskin/style.css
 *
 * Or set automatic compression at the top of .htaccess rewrite rules:
 * RewriteRule \.(js|css)$ rc.php?uri=%{REQUEST_FILENAME} [NC,L]
 *
 * @package Cotonti
 * @version 0.7.0
 * @author Julien Lecomte, Cotonti Team
 * @link http://www.julienlecomte.net/blog/2007/08/13/
 * @license BSD
 */

// Required for PHP 5.3
require_once './datas/config.php';

/*
 * List of known content types based on file extension.
 * Note: These must be built-in somewhere...
 */

$known_content_types = array(
	'js' => 'text/javascript',
	'css' => 'text/css',
	'gif' => 'image/gif',
	'jpg' => 'image/jpeg',
	'jpeg' => 'image/jpeg',
	'png' => 'image/png'
);

/*
 * Get the path of the target file.
 */

if (!isset($_GET['uri']))
{
	header('HTTP/1.1 400 Bad Request');
	echo '<html><body><h1>HTTP 400 - Bad Request</h1></body></html>'; // TODO: Need translate
	exit;
}

/*
 * Verify the existence of the target file.
 * Return HTTP 404 if needed.
 */

$src_uri = preg_replace('#[^\x20-\x7e]+#', '', $_GET['uri']); // ASCII printable chars only
if (strlen($src_uri) > 256)
{
	// No super long paths supported
	$src_uri = substr($src_uri, 0, 256);
}

if (!file_exists($src_uri))
{
	/* The file does not exist */
	header('HTTP/1.1 404 Not Found');
	echo '<html><body><h1>HTTP 404 - Not Found</h1></body></html>'; // TODO: Need translate
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

$etag = dechex($file_last_modified);
header('ETag: '.$etag);

$cache_control = 'must-revalidate, proxy-revalidate, max-age='.$max_age.', s-maxage='.$max_age;
header('Cache-Control: '.$cache_control);

/*
 * Check if the client should use the cached version.
 * Return HTTP 304 if needed.
 */

if (function_exists('http_match_etag') && function_exists('http_match_modified'))
{
	if (http_match_etag($etag) || http_match_modified($file_last_modified))
	{
		header('HTTP/1.1 304 Not Modified');
		exit;
	}
}
else
{
	error_log('The HTTP extensions to PHP does not seem to be installed...'); // TODO: Need translate
}

/*
 * Extract the directory, file name and file
 * extension from the 'uri' parameter.
 */

$uri_dir = '';
$file_name = '';
$content_type = '';

$uri_parts = explode('/', $src_uri);

for ($i = 0;$i < count($uri_parts) - 1;$i++)
{
	$uri_dir .= $uri_parts[$i].'/';
}

$file_name = end($uri_parts);

$file_parts = explode('.', $file_name);
if (count($file_parts) > 1)
{
	$file_extension = end($file_parts);
	$content_type = $known_content_types[$file_extension];
}

/*
 * Verify the requested file has allowed extension for security reasons.
 */

$doc_root = realpath( '.' );
$file_dir = realpath($uri_dir);

if (!isset($known_content_types[$file_extension]) || strpos($file_dir, $doc_root) !== 0)
{
	header('HTTP/1.1 403 Forbidden');
	echo '<html><body><h1>HTTP 403 - Forbidden</h1></body></html>'; // TODO: Need translate
	exit;
}

/*
 * Get the target file.
 * If the browser accepts gzip encoding, the target file
 * will be the gzipped version of the requested file.
 */

$dst_uri = $src_uri;

$compress = true;

/*
 * Let's compress only text files...
 */

$compress = $compress && (strpos($content_type, 'text') !== false);

/*
 * Finally, see if the client sent us the correct Accept-encoding: header value...
 */

$compress = $compress && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false);

if ($compress)
{
	ob_start('ob_gzhandler');
}

/*
 * Output the target file and set the appropriate HTTP headers.
 */

if ($content_type)
{
	header('Content-Type: '.$content_type);
}

//header('Content-Length: ' . filesize($dst_uri));
readfile($dst_uri);

if ($compress)
{
	ob_end_flush();
}

?>