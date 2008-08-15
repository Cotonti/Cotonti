<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=plug.php
Version=125
Updated=2008-mar-20
Type=Core
Author=Neocrome
Description=Plugin loader
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

$p = sed_import('p','G','ALP');
$e = sed_import('e','G','ALP');
$o = sed_import('o','G','ALP');
$s = sed_import('s','G','ALP');
$r = sed_import('r','G','ALP');
$h = sed_import('h','G','ALP');
$c1= sed_import('c1','G','ALP');
$c2 = sed_import('c2','G','ALP');

unset ($plugin_title, $plugin_body);

if (!empty($p))
	{

	die('Seditio do NOT supports the LDU standard plugins.');

	}
elseif (!empty($e))
	{
	$path_lang_def	= "plugins/$e/lang/$e.en.lang.php";
	$path_lang_alt	= "plugins/$e/lang/$e.$lang.lang.php";
	$path_skin_ntg	= "skins/$skin/plugin.tpl";
	$path_skin_def	= "plugins/$e/$e.tpl";
	$path_skin_alt	= "skins/$skin/plugin.standalone.$e.tpl";

	if (file_exists($path_lang_alt))
		{ require_once($path_lang_alt); }
	elseif (file_exists($path_lang_def))
		{ require_once($path_lang_def); }

	if (file_exists($path_skin_alt))
		{
		$path_skin= $path_skin_alt;
		$autoassigntags = FALSE;
		}
	elseif (file_exists($path_skin_def))
		{
		$path_skin = $path_skin_def;
		$autoassigntags = FALSE;
		}
	elseif (file_exists($path_skin_ntg))
		{
		$path_skin = $path_skin_ntg;
		$autoassigntags = TRUE;
		}
	else
		{
		header("Location: message.php?msg=907");
		exit;
		}

	list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = sed_auth('plug', $e);
	sed_block($usr['auth_read']);

	if (is_array($sed_plugins))
		{
		foreach($sed_plugins as $i => $k)
			{
			if ($k['pl_hook']=='standalone' && $k['pl_code']==$e)
				{ $out['subtitle'] = $k['pl_title']; }
			}
		}

	$out['subtitle'] = (empty($L['plu_title'])) ? $out['subtitle'] : $L['plu_title'];
	$sys['sublocation'] = $out['subtitle'];

	/* ============= */

	require_once("system/header.php");

	$t = new XTemplate($path_skin);

	$extp = array();

	if (is_array($sed_plugins))
		{
		foreach($sed_plugins as $i => $k)
			{
			if ($k['pl_hook']=='standalone' && $k['pl_code']==$e)
				{ $extp[$i] = $k; }
			}
		}

	if (count($extp)==0)
		{
		header("Location: message.php?msg=907");
		exit;
		}

	if (is_array($extp))
		{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }

	if ($autoassigntags)
		{
		$plugin_title = (empty($plugin_title)) ? $L['plu_title'] : $plugin_title;

		$t-> assign(array(
			"PLUGIN_TITLE" => "<a href=\"plug.php?e=$e\">".$plugin_title."</a>",
			"PLUGIN_SUBTITLE" => $plugin_subtitle,
			"PLUGIN_BODY" => $plugin_body
			));
		}

	$t->parse("MAIN");
	$t->out("MAIN");

	require_once("system/footer.php");
	}

elseif (!empty($o))

	{
	$extp = array();
	if (is_array($sed_plugins))
		{
		foreach($sed_plugins as $i => $k)
			{
			if ($k['pl_hook']=='popup' && $k['pl_code']==$o)
				{ $extp[$i] = $k; }
			}
		}

	if (count($extp)==0)
		{
		header("Location: message.php?msg=907");
		exit;
		}

	$popup_header1 = $cfg['doctype']."<html><head>".sed_htmlmetas()."\n\n<script type=\"text/javascript\">\n<!--\nfunction add(text)\n	{\nopener.document.".$c1.".".$c2.".value += text; }\n//-->\n</script>\n";
	$popup_header2 = "</head><body>";
	$popup_footer = "</body></html>";

	/* ============= */

	$mskin = sed_skinfile(array('popup', $o));
	$t = new XTemplate($mskin);

	if (is_array($extp))
		{ foreach($extp as $k => $pl) { include_once('./plugins/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }

	$t->assign(array(
		"POPUP_HEADER1" => $popup_header1,
		"POPUP_HEADER2" => $popup_header2,
		"POPUP_FOOTER" => $popup_footer,
		"POPUP_BODY" => $popup_body,
			));

	$t->parse("MAIN");
	$t->out("MAIN");

	}

elseif (!empty($h))

	{
	if ($h=='smilies')
		{
		if (is_array($sed_smilies))
			{
			$popup_body = $L['Smilies']." (".$L['Smilies_explain'].") :<p>";
			$popup_body .= "<div class=\"smilies\"><table>";
			reset ($sed_smilies);

			while (list($i,$dat) = each($sed_smilies))
				{
				$popup_body .= "<tr><td style=\"text-align:right;\"><a href=\"javascript:add('".$dat['smilie_code']."')\"><img src=\"".$dat['smilie_image']."\"  alt=\"\" /></a></td><td>".$dat['smilie_code']."</td><td> ".sed_cc($dat['smilie_text'])."</td></tr>";
				}
			$popup_body .= "</table></div></p>";
			}
		else
			{ $popup_body = $L['None']; }

		}
	elseif ($h=='bbcodes')
		{
		reset ($sed_bbcodes);
		$ii=0;
		$popup_body = $L['BBcodes']." (".$L['BBcodes_explain'].") :<p>";
		$popup_body .= "<div class=\"bbcodes\"><table><tr>";

		while (list($i,$dat) = each($sed_bbcodes))
			{
			$kk = "bbcodes_".$dat[1];
			if (substr($dat[1], 0, 5)=='style')
			   	{
				$popup_body .= "<td colspan=\"2\"><a href=\"javascript:add('".$dat[0]."')\">";
				$popup_body .= "<span class=\"bb".$dat[1]."\">".$L[$kk]." &nbsp;</span></td>";
			   	}
			else
			   	{
				$popup_body .= "<td><a href=\"javascript:add('".$dat[0]."')\">";
				$popup_body .= "<img src=\"system/img/bbcodes/".$dat[1].".gif\" alt=\"\" /></a></td>";
				$popup_body .= "<td>".$L[$kk]." &nbsp;</td>";
				}

			$ii++;
			if ($ii==3)
				{
				$ii=0;
				$popup_body .= "</tr><tr>";
				}
			}

		$popup_body .= "</table></div></p>";
		}
	else
		{
		$incl = "system/help/$h.txt";
		$fd = @fopen($incl, "r") or die("Couldn't find a file : ".$incl);
		$popup_body = fread($fd, filesize($incl));
		fclose($fd);
		}

	$popup_header1 = $cfg['doctype']."<html><head>".sed_htmlmetas()."\n\n<script type=\"text/javascript\">\n<!--\nfunction add(text)\n	{\nopener.document.".$c1.".".$c2.".value += text; }\n//-->\n</script>\n";
	$popup_header2 = "</head><body>";
	$popup_footer = "</body></html>";

	/* ============= */

	$mskin = sed_skinfile(array('popup', $h));
	$t = new XTemplate($mskin);

	$t->assign(array(
		"POPUP_HEADER1" => $popup_header1,
		"POPUP_HEADER2" => $popup_header2,
		"POPUP_FOOTER" => $popup_footer,
		"POPUP_BODY" => $popup_body,
			));

	$t->parse("MAIN");
	$t->out("MAIN");

	}

elseif (!empty($r))

	{
	if (eregi("\.",$r) || eregi("/",$r))
		{ sed_die(); }

	$incl = 'plugins/code/'.$r.'.php';

	if (@file_exists($incl))
		{ require_once($incl); }
	else
		{ sed_die(); }
	}

elseif ($m=='version')

	{
	$plugin_title = "Seditio FrameWork - Version & copyrights";
	$plugin_body = "<h1>Seditio build 120 / 2007-jun-16</h1><h4>Licence</h4>Important: Seditio is not an open source software product. You must follow the limitations in this software agreement. Technical support is available to those following this agreement with no charge on http://www.neocrome.net<br />Neocrome grants you a non-exclusive license to use Seditio if you follow all restrictions in all sections of this agreement.<h4>Copyrights</h4>Ownership rights and intellectual property rights in the Seditio software shall remain with Neocrome. This software is protected by copyright laws and treaties. Title and related rights in the content accessed through the software is the property of the applicable content owner and may be protected by applicable law. This license gives you no rights to such content.<br />Authorisation to remove copyright notices can be obtained from Neocrome for a one time fee. This fee authorises you to remove the output of copyright notices, it does not give you authorisation to remove any copyright notices in the script source header files nor any other rights.<h4>Scope of grant</h4>You may :<p>- Use the software on one or more computers.<br />- Customise the software's design to suit the needs of your own web site.<br />- Produce and distribute modification instructions, skin packs or language packs provided that they contain notification that it was originally created by Neocrome. The modifications instructions you personally create are not owned by Neocrome so long as they contain no proprietary coding from Seditio.</p>You may not :<p>- Use Seditio for illegal activities.<br />- Modify and/or remove the copyright notice in the footer and in the header of each script source file.<br />- Reverse engineer, disassemble, or create derivative works based on Seditio for distribution or usage outside your website.<br />- Distribute Seditio without written consent from Neocrome.<br />- Permit other individuals to use Seditio except under the terms listed above.</p><h4>Third party modifications</h4>Technical support will not be provided for third-party modifications to the software including modifications to code, Skin packs, and Language packs to any license holder. If Seditio is modified using a third-party modification instruction or otherwise, technical support may be refused to any license holder.<h4>Disclaimer of warranty</h4>The Software is provided on an 'as is' basis, without warranty of any kind, including without limitation the warranties of merchantability, fitness for a particular purpose and non-infringement. The entire risk as to the quality and performance of this software is borne by you.<h4>Contacts</h4>You can contact Neocrome at <a href='http://www.neocrome.net'>http://www.neocrome.net</a> for questions.";

	/* ============= */

	require_once("system/header.php");

	$t = new XTemplate("skins/".$skin."/plugin.tpl");
	$t-> assign(array(
		"PLUGIN_TITLE" => $plugin_title,
		"PLUGIN_BODY" => $plugin_body
		));
	$t->parse("MAIN");
	$t->out("MAIN");

	require_once("system/footer.php");

	}

else

	{ sed_die(); }
?>
