<?php
/**
 * JavaScript and CSS loader for Nemesis theme
 *
 * @package Cotonti
 * @version 0.9.18
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2010-2015
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL.');

Resources::addFile($cfg['themes_dir'].'/'.$usr['theme'].'/css/reset.css');
Resources::addFile($cfg['themes_dir'].'/'.$usr['theme'].'/css/extras.css');
Resources::addFile($cfg['themes_dir'].'/'.$usr['theme'].'/css/default.css');
Resources::addFile($cfg['themes_dir'].'/'.$usr['theme'].'/css/modalbox.css');
Resources::addFile($cfg['themes_dir'].'/'.$usr['theme'].'/js/js.js');
