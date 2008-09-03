<?php
/**
 * Plugin install script
 *
 * @package Seditio-N
 * @version 0.0.1
 * @author Trustmaster
 * @copyright (c) 2008 Cotonti Team
 * @license BSD License
 */

// Installing new bbcodes
sed_bbcode_remove(0, 'chili');
sed_bbcode_add('highlight', 'callback', '\[highlight=([\w\-]+)\](.*?)\[/highlight\]', 'return \'<div class="highlight"><pre class="\'.$input[1].\'">\'.sed_bbcode_cdata($input[2]).\'</pre></div>\';', true, 3, 'chili');
?>