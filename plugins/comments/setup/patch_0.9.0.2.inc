<?php
/**
 * Reinstalls module/structure config implantations if necessary
 *
 * @package Comments
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

global $cfg;

include $cfg['plugins_dir'] . '/comments/setup/comments.install.php';

?>