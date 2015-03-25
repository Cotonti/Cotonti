<?php
/**
 * Creates HTML Purifier Serializer cache folder
 *
 * @package HTML Purifier
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

if (!file_exists($cfg['cache_dir'] . '/htmlpurifier'))
{
	mkdir($cfg['cache_dir'] . '/htmlpurifier');
}
