<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net

[BEGIN_SED]
File=tb2preview.php
Version=101
Updated=2006-mar-15
Type=Standalone
Author=Arkkimaagi
Description=Bbcode preview tool
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

$tbL['ReadMoreCut'] = "Rest of the news post will be hidden behind a \"Read more...\"-link";
$tbL['SubpageCut'] = "Cut into sub-pages, page:";
$tbL['NotEnabled'] = "Textboxer2 is not enabled on this website, this feature is disabled.";

function textboxer_checkIfEnabled(){
	global $sed_plugins;

	$found_textboxer=0;
	foreach($sed_plugins as $value) if(in_array('textboxer2', $value)) $found_textboxer+=$value['pl_active'];
	if($found_textboxer>0){
		return TRUE;
	}else{
		return FALSE;
		//$out = $tbL['NotEnabled'];
	}
}

function textboxer_preview($text, $bbcode, $smiley, $linebreak, $location){
	global $cfg, $sed_plugins,$L, $tbL;

	$out = sed_parse($text, $bbcode, $smiley, $linebreak);

	switch($location){
		case "pageadd":
		case "pageedit":
			$out=$out;
			$pag['page_tabs'] = explode('[newpage]', $out, 99);
			$pag['page_totaltabs'] = count($pag['page_tabs']);

			if ($pag['page_totaltabs']>1){
				$output="";
				if (empty($pag['page_tabs'][0])){
					$remove = array_shift($pag['page_tabs']);
					$pag['page_totaltabs']--;
				}

				$pag['page_tab'] = ($pag['page_tab']>$pag['page_totaltabs']) ? 1 : $pag['page_tab'];

				for ($i = 0; $i < $pag['page_totaltabs']; $i++){
					$p1 = mb_strpos($pag['page_tabs'][$i], '[title]');
					$p2 = mb_strpos($pag['page_tabs'][$i], '[/title]');

					if ($p2>$p1){
						$pag['page_tabtitle'][$i] = substr ($pag['page_tabs'][$i], $p1+7, ($p2-$p1)-7);
						$pag['page_tabs'][$i] = trim(str_replace('[title]'.$pag['page_tabtitle'][$i].'[/title]', '', $pag['page_tabs'][$i]));
					}else { $pag['page_tabtitle'][$i] = "?"; }

					$output.='<div class="cut">'.$tbL['SubpageCut'].'<br /><span>'.($i+1).'. '.$pag['page_tabtitle'][$i].'</span></div>'.$pag['page_tabs'][$i];
				}
				$out=$output;
			}
			break;
		case "newsadd":
		case "newsedit":
			$out = str_replace('<more>', '[more]', $out);
			$out = implode('<div class="cut">'.$tbL['ReadMoreCut'].'</div>',explode('[more]', $out,2));
			$out = str_replace('[more]', '', $out);
			break;
		default:
			break;
	}
	return $out;
}

function textboxer_smilies(){
	global $sed_smilies;
	$result="errr";
	if (is_array($sed_smilies)){
		$result="p.innerHTML ='";

		reset ($sed_smilies);
		$count=0;
		$result2="";

		while (list($i,$dat) = each($sed_smilies)){
			$smilie_image = str_replace("'","\\'",str_replace("\\","\\\\",$dat['smilie_image']));
			$smilie_code = str_replace("'","\\'",str_replace("\\","\\\\",$dat['smilie_code']));
			$smilie_text = str_replace("'","\\'",str_replace("\\","\\\\",$dat['smilie_text']));
           	$result .= '<li><a href="javascript:none();" id="tb_smi_'.$count.'"><img src="'.$smilie_image.'" alt="'.$smilie_text.'"></a></li>';
			$result2.= "bbcodes[u++]=[-1,'smi_".$count."','','".$smilie_code."',\"".$smilie_text."\",1];";
			$count++;
		}
		$result.="';u=bbcodes.length;";
		$result.=$result2;
		$result.="tb2.start();";
	}
  	return $result;
}


header('Content-type: text/html');
@ob_start("ob_gzhandler");

if(textboxer_checkIfEnabled()){
	if(sed_import('s','P','BOL')){
		echo(textboxer_smilies());
	}else if(sed_import('p','P','BOL')){
	echo(textboxer_preview(sed_import('t','P','HTM'), sed_import('bb','P','BOL'), sed_import('smi','P','BOL'), sed_import('br','P','BOL'), sed_import('loc','P','ALP')));
	}else{
		echo("Error");
	}
}else{
	echo $tbL['NotEnabled'];
}
$size = ob_get_length();
@ob_end_flush();

sed_stat_inc('textboxerprev');

//storeDataTransfer($size);

?>
