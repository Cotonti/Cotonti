<?php
/**
 * Installation handler
 *
 * @package SiteMap
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 *
 * @todo notifications about robots.txt (if need to add o delete some strings)
 */

defined('COT_CODE') or die('Wrong URL');

$robotsTxtFilePath = './robots.txt';
$robotsTxtFile = '';
$robotsTxtChanged = false;
if(file_exists($robotsTxtFilePath) && is_writable($robotsTxtFilePath)) {

	$siteMapLinks = array(
		'index' => cot_url('plug', array('r'=>'sitemap', 'a'=>'index'), '', true),
		'handy' => 'sitemap.xml',
		//'nohandy' => cot_url('plug', array('r'=>'sitemap'))
	);

	foreach($siteMapLinks as $key => $val) {
		if(!cot_url_check($val)){
			$siteMapLinks[$key] = cot::$cfg['mainurl'].'/'.$siteMapLinks[$key];
		}
	}

	$robotsTxtFile = file($robotsTxtFilePath);
	$to_delete = array();
	$to_add = $siteMapLinks;

	foreach ($robotsTxtFile as $line) {
		// Sitemap line. Find it all
		if ( mb_strpos(mb_strtolower($line), 'sitemap:') !== false){
			// find all Sitemap links in robots.txt and remove duplicates
			$found = false;
			foreach($siteMapLinks as $key => $siteMapLink){
				if(mb_stripos($line, $siteMapLink) !== false) {
					$found = true;
					unset($to_add[$key]);	// it is in robots.txt already
				}
			}
			if(!$found) $to_delete[$i] = $line;
		}
		$i++;
	}

	if(!empty($to_add)){
		$robotsTxtFile[] = "\n";
		foreach($to_add as $key => $val){
			$robotsTxtFile[] = 'Sitemap: '.$val."\n";
		}
		$robotsTxtChanged = true;
	}
}

if($robotsTxtChanged) file_put_contents($robotsTxtFilePath, $robotsTxtFile);
