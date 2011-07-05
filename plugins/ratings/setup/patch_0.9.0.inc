<?php
/**
 * Reinstalls module/structure config implantations if necessary
 *
 * @package ratings
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2011
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

global $cfg;

include $cfg['plugins_dir'] . '/ratings/setup/ratings.install.php';

?>