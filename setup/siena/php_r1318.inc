<?php
/**
 * r1318: datas/cache/templates cleanup for CoTemplate 2.5
 */

defined('COT_CODE') && defined('COT_UPDATE') or die('Wrong URL');

$dp = opendir('./datas/cache/templates');
while ($f = readdir($dp))
{
	$path = './datas/cache/templates/' . $f;
	if (is_file($path))
	{
		unlink($path);
	}
}
closedir($dp);

?>
