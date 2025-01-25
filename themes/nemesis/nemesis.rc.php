<?php
/**
 * JavaScript and CSS loader for Nemesis theme
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

// Bootstrap is needed to use the Modal, Toast, etc. components.
Resources::addFile('lib/bootstrap/css/bootstrap.min.css');
if (Cot::$cfg['headrc_consolidate']) {
    Resources::addFile('lib/bootstrap/js/bootstrap.bundle.min.js');
} else {
    Resources::linkFileFooter('lib/bootstrap/js/bootstrap.bundle.min.js');
}

Resources::addFile(Cot::$cfg['themes_dir'] . '/' . Cot::$usr['theme'] . '/css/reset.css');
Resources::addFile(Cot::$cfg['themes_dir'] . '/' . Cot::$usr['theme'] . '/css/extras.css');
Resources::addFile(Cot::$cfg['themes_dir'] . '/' . Cot::$usr['theme'] . '/css/default.css');
Resources::addFile(Cot::$cfg['themes_dir'] . '/' . Cot::$usr['theme'] . '/css/modalbox.css');
Resources::addFile(Cot::$cfg['themes_dir'] . '/' . Cot::$usr['theme'] . '/js/js.js');
