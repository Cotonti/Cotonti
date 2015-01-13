<?php
/**
 * JavaScript and CSS loader for Nemesis theme
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

Resources::addFile($cfg['themes_dir'].'/'.$usr['theme'].'/css/reset.css');
Resources::addFile($cfg['themes_dir'].'/'.$usr['theme'].'/css/extras.css');
Resources::addFile($cfg['themes_dir'].'/'.$usr['theme'].'/css/default.css');
Resources::addFile($cfg['themes_dir'].'/'.$usr['theme'].'/css/modalbox.css');
Resources::addFile($cfg['themes_dir'].'/'.$usr['theme'].'/js/js.js');
