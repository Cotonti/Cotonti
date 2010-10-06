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
 * @author Trustmaster
 * @copyright Copyright (c) Cotonti Team 2008-2010
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

// Preview contents
$text = cot_import('text', 'P', 'HTM');
$style = '<link rel="stylesheet" type="text/css" href="themes/'.$theme.'/'.$theme.'.css" />'."\n";
cot_sendheaders();
echo $style . '<body class="preview">' . cot_parse($text) . '</body>';

?>