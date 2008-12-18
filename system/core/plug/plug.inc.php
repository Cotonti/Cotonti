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
	$path_lang_def	= $cfg['plugins_dir']."/$e/lang/$e.en.lang.php";
	$path_lang_alt	= $cfg['plugins_dir']."/$e/lang/$e.$lang.lang.php";
	$path_skin_ntg	= sed_skinfile('plugin');
	$path_skin_def	= $cfg['plugins_dir']."/$e/$e.tpl";
	$path_skin_alt	= sed_skinfile($e, true);

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
		header("Location: " . SED_ABSOLUTE_URL . sed_url('message', "msg=907", '', true));
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

	require_once $cfg['system_dir'] . '/header.php';

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
		header("Location: " . SED_ABSOLUTE_URL . sed_url('message', "msg=907", '', true));
		exit;
	}

	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }

	if ($autoassigntags)
	{
		$plugin_title = (empty($plugin_title)) ? $L['plu_title'] : $plugin_title;

		$t-> assign(array(
			"PLUGIN_TITLE" => '<a href="'.sed_url('plug', "e=$e").'">'.$plugin_title."</a>",
			"PLUGIN_SUBTITLE" => $plugin_subtitle,
			"PLUGIN_BODY" => $plugin_body
		));
	}

	$t->parse("MAIN");
	$t->out("MAIN");

	require_once $cfg['system_dir'] . '/footer.php';
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
		header("Location: " . SED_ABSOLUTE_URL . sed_url('message', "msg=907", '', true));
		exit;
	}

	$popup_header1 = $cfg['doctype']."<html><head>".sed_htmlmetas()."\n\n<script type=\"text/javascript\">\n<!--\nfunction add(text)\n	{\nopener.document.".$c1.".".$c2.".value += text; }\n//-->\n</script>\n";
	$popup_header2 = "</head><body>";
	$popup_footer = "</body></html>";

	/* ============= */

	sed_sendheaders();

	$mskin = sed_skinfile(array('popup', $o));
	$t = new XTemplate($mskin);

	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }

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
	else
	{
		$incl = $cfg['system_dir']."/help/$h.txt";
		$fd = @fopen($incl, "r") or die("Couldn't find a file : ".$incl);
		$popup_body = fread($fd, filesize($incl));
		fclose($fd);
	}

	$popup_header1 = $cfg['doctype']."<html><head>".sed_htmlmetas()."\n\n<script type=\"text/javascript\">\n<!--\nfunction add(text)\n	{\nopener.document.".$c1.".".$c2.".value += text; }\n//-->\n</script>\n";
	$popup_header2 = "</head><body>";
	$popup_footer = "</body></html>";

	/* ============= */

	sed_sendheaders();

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

elseif (!empty($r) && defined('SED_AJAX'))
{
	$extp = array();
	if (is_array($sed_plugins))
	{
		foreach($sed_plugins as $i => $k)
		{
			if ($k['pl_hook']=='ajax' && $k['pl_code']==$r)
			{ $extp[$i] = $k; }
		}
	}

	if (count($extp)==0)
	{
		header("Location: " . SED_ABSOLUTE_URL . sed_url('message', "msg=907", '', true));
		exit;
	}

	if (is_array($extp))
	{ foreach($extp as $k => $pl) { include_once($cfg['plugins_dir'].'/'.$pl['pl_code'].'/'.$pl['pl_file'].'.php'); } }
}
else
{
	sed_die();
}
?>
