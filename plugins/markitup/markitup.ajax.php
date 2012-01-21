<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]
==================== */

/**
 * Simple AJAX previewer for MarkItUp!
 *
 * @package markitup
 * @version 0.7.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Preview contents
$text = cot_import('text', 'P', 'HTM');
$style = '<link rel="stylesheet" type="text/css" href="'.$cfg['themes_dir'].'/'.$theme.'/'.$theme.'.css" />'."\n";
cot_sendheaders();
echo $style . '<body class="preview">' . cot_parse($text) . '</body>';

?>