<?php
/**
 * Reinstalls module/structure config implantations if necessary
 *
 * @package comments
 * @version 0.9.0.2
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

global $cfg;

include $cfg['plugins_dir'] . '/comments/setup/comments.install.php';

?>