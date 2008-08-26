<?php
/**
 * Plugin uninstall script
 *
 * @package Seditio-N
 * @version 0.0.1
 * @author Trustmaster
 * @copyright (c) 2008 Cotonti Team
 * @license BSD License
 */

// Remove plugin bbcodes
sed_bbcode_remove(0, 'markitup');
?>