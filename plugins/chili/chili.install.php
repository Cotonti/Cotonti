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
sed_bbcode_add('highlight', 'pcre', '\[highlight=([\w\-]+)\](.*?)\[/highlight\]', '<pre><code class="$1">$2</code></pre>', true, 128, 'chili');
?>