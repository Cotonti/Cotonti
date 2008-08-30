<?php
/* ====================
[BEGIN_SED]
File=plugins/chili/chili.header.php
Version=121
Updated=2008-aug-30
Type=Plugin
Author=Trustmaster
Description=
[END_SED]
[BEGIN_SED_EXTPLUGIN]
Code=chili
Part=header
File=chili.header
Hooks=header.main
Tags=header.tpl:{HEADER_COMPOPUP}
Minlevel=0
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * chili connector for Seditio
 *
 * @package Seditio-N
 * @version 0.0.1
 * @author Trustmaster
 * @copyright (c) 2008 Cotonti Team
 * @license BSD license
 */

$out['compopup'] .= <<<HTM
<script type="text/javascript" src="{$cfg['plugins_dir']}/chili/js/jquery.chili-2.2.js"></script>
<script type="text/javascript" >
ChiliBook.recipeFolder = "{$cfg['plugins_dir']}/chili/js/";
</script>

HTM;
?>