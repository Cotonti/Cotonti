<?php
/* ====================
[BEGIN_SED]
File=plugins/markitup/markitup.header.php
Version=121
Updated=2008-aug-26
Type=Plugin
Author=Trustmaster
Description=
[END_SED]
[BEGIN_SED_EXTPLUGIN]
Code=markitup
Part=header
File=markitup.header
Hooks=header.main
Tags=header.tpl:{HEADER_COMPOPUP}
Minlevel=0
Order=10
[END_SED_EXTPLUGIN]
==================== */

/**
 * MarkItUp! connector for Seditio
 *
 * @package Sedition-N
 * @version 0.0.1
 * @author Trustmaster
 * @copyright (c) 2008 Cotonti Team
 * @license BSD license
 */

$mkup_lang = "plugins/markitup/lang/$lang.lang.js";
if(!file_exists($mkup_lang))
{
	$mkup_lang = 'plugins/markitup/lang/en.lang.js';
}

$xg = sed_sourcekey();

$out['compopup'] .= <<<HTM
<script type="text/javascript" src="plugins/markitup/jquery.markitup.js"></script>
<script type="text/javascript" src="$mkup_lang"></script>
<script type="text/javascript" src="plugins/markitup/set.js"></script>
<link rel="stylesheet" type="text/css" href="plugins/markitup/skins/markitup/style.css" />
<link rel="stylesheet" type="text/css" href="plugins/markitup/style.css" />
<script type="text/javascript" >
   $(document).ready(function() {
      $("textarea.editor").markItUp(mySettings);
   });
</script>

HTM;

?>