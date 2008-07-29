<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=plugins/textboxer2/inc/textboxer2.inc.php
Version=120
Updated=2007-mar-03
Type=Core
Author=Arkkimaagi
Description=Functions
[END_SED]
==================== */

if (!defined('SED_CODE')) { die('Wrong URL.'); }

/* ========= Textboxer (code by Mikko "Arkkimaagi" T.) ========== */

/* Default settings for all pages */
function tb2_getDefault(){
//-----------------------------------------------------------------------
// You can configure these settings to modify textboxer2 default settings.
//-----------------------------------------------------------------------

/*
This formats the textboxer buttons and their order.
There is no group 0, and 1 is reserved for smilies.

These two are ment for limiting copy/cut/paste functions for ie only, as other browsers do not support them.
'tb_ieOnlyStart',
'tb_ieOnlyEnd',

By stating only a number, system starts a new dropdown. Dropdowns can be only in one level, no inside dropdowns allowed.
The given dropdown number is used when selecting an icon for the dropdown.

By stating '}' the dropdown list is closed. Each dropdown should be closed with this.
*/
	$tb2Buttons = array(
		'tb_ieOnlyStart',
			2,
				'copy',
				'cut',
				'paste',
			'}',
		'tb_ieOnlyEnd',

		'bold',
		'underline',
		'italic',

		3,
			'left',
			'center',
			'right',
		'}',

		4,
			'quote',
			'spoiler',
			'code',
			'list',
			'hr',
			'spacer',
			'ac',
			'p',
		'}',

		5,
			'image',
			'thumb',
			'colleft',
			'colright',
		'}',

		6,
			'url',
//			'urlp',
			'email',
//			'emailp',
		'}',

		7,
			'black',
			'grey',
			'sea',
			'blue',
			'sky',
			'green',
			'yellow',
			'orange',
			'red',
			'white',
			'pink',
			'purple',
		'}',

		8,
			'page',
//			'pagep',
			'user',
//			'link',
//			'linkp',
			'flag',
			'pfs',
			'topic',
			'post',
			'pm',
		'}',

		1,
			'smilies',
		'}',
		//'more',
		//'title',
		//'xtra_wordcount', //this is an sample how to create your own buttons
		'preview'
	);

	return $tb2Buttons;
}

/* settings for buttons */
function tb2_getSettings(){
	global $L;

	$res['copy'] = array('CTRL + C', '', 1);
	$res['cut'] = array('CTRL + X', '', 2);
	$res['paste'] = array('CTRL + V', '', 3);
	$res['bold'] = array('[b]', '[/b]', 4);
	$res['underline'] = array('[u]', '[/u]', 5);
	$res['italic'] = array('[i]', '[/i]', 6);
	$res['left'] = array('[left]', '[/left]', 7);
	$res['center'] = array('[center]', '[/center]', 8);
	$res['right'] = array('[right]', '[/right]', 9);
	$res['quote'] = array('[quote]', '[/quote]', 10);
	$res['spoiler'] = array('[spoiler]', '[/spoiler]', 10);
	$res['code'] = array('[code]', '[/code]', 11);
	$res['list'] = array('\n[list]\n1\n2\n3\n[/list]\n', '', 12);
	$res['hr'] = array('[hr]', '', 13);
	$res['spacer'] = array('[_]', '', 14);
	$res['image'] = array('[img]', '[/img]', 15);
	$res['thumb'] = array('[t=thumbnail]', '[/t]', 16);
	$res['colleft'] = array('[colleft]', '[/colleft]', 17);
	$res['colright'] = array('[colright]', '[/colright]', 18);
	$res['url'] = array('[url]', '[/url]', 19);
	$res['urlp'] = array('[url=]', '[/url]', 20);
	$res['email'] = array('[email]', '[/email]', 21);
	$res['emailp'] = array('[email=]', '[/email]', 22);
	$res['black'] = array('[black]', '[/black]', 23);
	$res['grey'] = array('[grey]', '[/grey]', 24);
	$res['sea'] = array('[sea]', '[/sea]', 25);
	$res['blue'] = array('[blue]', '[/blue]', 26);
	$res['sky'] = array('[sky]', '[/sky]', 27);
	$res['green'] = array('[green]', '[/green]', 28);
	$res['yellow'] = array('[yellow]', '[/yellow]', 29);
	$res['orange'] = array('[orange]', '[/orange]', 30);
	$res['red'] = array('[red]', '[/red]', 31);
	$res['white'] = array('[white]', '[/white]', 32);
	$res['pink'] = array('[pink]', '[/pink]', 33);
	$res['purple'] = array('[purple]', '[/purple]', 34);
	$res['page'] = array('[page]', '[/page]', 35);
	$res['user'] = array('[user=]', '[/user]', 36);
//	$res['link'] = array('[link]', '[/link]', 37);
	$res['flag'] = array('[f]', '[/f]', 38);
	$res['pagep'] = array('[page=ID]', '[/page]', 35);
//	$res['linkp'] = array('[link=ID]', '[/link]', 37);
	$res['ac'] = array('[ac=explanation]', '[/ac]', 39);
	$res['p'] = array('[p]', '[/p]', 40);
	$res['pfs'] = array('[pfs]', '[/pfs]', 42);
	$res['topic'] = array('[topic]', '[/topic]', 44);
	$res['post'] = array('[post]', '[/post]', 45);
	$res['pm'] = array('[pm]', '[/pm]', 46);
	//spesific for pages
	$res['title'] = array('[newpage]\n[title]', '[/title]', 48);
	//spesific for news
	$res['more'] = array('', '[more]', 41);
	//Only in textboxer2
	$res['preview'] = array('', '', 43);

	//Adding custom buttons
	//$L['bbcodes_ex_xtra_wordcount'] = "Calculate words, characters, etc.";
	//$L['bbcodes_xtra_wordcount'] = "Calculate stats";
	//$res['xtra_wordcount'] = array('', '', 0);

	return $res;
}

/* ---------------------------------------------------------------------------------------------------------------------- */

function sed_textboxer2($name, $formname, $content, $rows, $cols, $loc="unknown", $parse_bbcodes, $parse_smilies, $parse_br, $tb2Buttons, $bbDropdownIcons, $smilieDropMaxHeight, $initialSmiliesCount) {
	global $cfg, $skin, $tbL;

	$result="";

	$textboxer_imgurl = "plugins/textboxer2/themes/";
	$textboxer_themeurl = "plugins/textboxer2/themes/";
	$textboxer_jsurl = "plugins/textboxer2/themes/tb2.js";

	$pbb = ($parse_bbcodes)?1:0;
	$psm = ($parse_smilies)?1:0;
	$pbr = ($parse_br)?1:0;

	if (!is_array($tb2Buttons)){
		$tb2Buttons=tb2_getDefault();
	}

	$xtraJS="";

//Here is an sample how to add your own javascript functions to tb2.
/*
	$xtraJS = '
function xtra_wordcount(){
	function delEmpty(arr){
		rarr=new Array();
		for(i in arr){
			var a=wipe(arr[i]," ","");
			if(a!=""){rarr.push(arr[i]);}
		}
		return rarr;
	}
	function wipe(txt,rem,rep){return txt.split(rem).join(rep);}

	if(o=dg(tbName)){
		txt=o.value;
		var charc=wipe(txt,"\r\n"," ").length;
		var words=delEmpty(wipe(wipe(txt,"\r\n"," "),"\n"," ").split(" "));
		var lines=delEmpty(wipe(txt,"\r\n","\n").split("\n"));
		var avrcpw = words.join("").length/words.length;
		var linew=0;
		for(i in lines){linew+=delEmpty(lines[i].split(" ")).length;}
		var avrwpl=linew/lines.length;
		var wordc=words.length;
		var linec=lines.length;
		var bbc=txt.split("[").length-txt.split("[/").length;
		alert("Statistics from textarea:"+
			"\n  Characters: "+charc+
			"\n  Words: "+wordc+
			"\n  Rows: "+linec+
			"\n  bbcodes: "+bbc+
			"\n  Average chars per word: "+Math.round(avrcpw*100)/100+
			"\n  Average words per row: "+Math.round(avrwpl*100)/100);
	}
}';*/


	$result = '
<link href="'.$textboxer_themeurl.'layout.css" type="text/css" rel="stylesheet" />
<link href="skins/'.$skin.'/'.$skin.'.textboxer.css" type="text/css" rel="stylesheet" />';

	$result.='
<script src="'.$textboxer_jsurl.'" type="text/javascript"></script>
<script type="text/javascript"><!--
//Preview function initialization (DO NOT MODIFY THIS PREVIEW BLOCK!)
var xmlhttp=false;
/*@cc_on @*/
/*@if(@_jscript_version>=5)
try{xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");}catch(e){try{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}catch(E){xmlhttp=false;}}
@end @*/
if(!xmlhttp&&typeof XMLHttpRequest!="undefined"){xmlhttp=new XMLHttpRequest();}
//end of preview init (DO NOT MODIFY THIS PREVIEW BLOCK!)
'.

$xtraJS

.'
var tbURL="plug.php?r=tb2preview";
var tbName="'.$name.'";
var formName="'.$formname.'";
var popupSmilies='.$cfg['plugin']['textboxer2']['popup_smilies'].';
var tbLoc="bb='.$pbb.'&smi='.$psm.'&br='.$pbr.'&loc='.$loc.'";
var tbMax='.$smilieDropMaxHeight.';
var tbdrop=['.implode(',',$bbDropdownIcons).'];
var tbL={'.

'warnPM:"'.$tbL['tb2_notInPreview'].
'",loadingPM:"'.$tbL['tb2_loadingPreview'].
'",loadingSMI:"'.$tbL['tb2_loadingSmilies'].
'",noSupport:"'.$tbL['tb2_noBrowserSupport'].
'",PM:"'.$tbL['tb2_previewMode'].
'",bbc:"'.$tbL['tb2_defaultExplain'].

'",quote:"'.$tbL['tb2_quote'].
'",code:"'.$tbL['tb2_code'].
'",urlp:"'.$tbL['tb2_urlp'].
'",url:"'.$tbL['tb2_url'].
'",emailp:"'.$tbL['tb2_emailp'].
'",email:"'.$tbL['tb2_email'].
'",pagep:"'.$tbL['tb2_pagep'].
'",page:"'.$tbL['tb2_page'].
// '",linkp:"'.$tbL['tb2_linkp'].
// '",link:"'.$tbL['tb2_link'].
'",imagep:"'.$tbL['tb2_imgp'].
'",image:"'.$tbL['tb2_img'].
'",acp:"'.$tbL['tb2_acp'].
'",ac:"'.$tbL['tb2_ac'].
'",userp:"'.$tbL['tb2_userp'].
'",user:"'.$tbL['tb2_user'].

'",preview404:"'.$tbL['tb2_preview404'].
'",previewError:"'.$tbL['tb2_previewError'].

'"};
var bbcodes=['.

	/*This generates the javascript table for explanations*/
	tb2_jscode($tb2Buttons,$initialSmiliesCount).

'];
//creates the tb. Takes the name of the variable as an parameter.
var tb2=new TextBoxer2("tb2",tbURL,tbName,tbLoc,tbMax,tbdrop,tbL);
//This starts textboxer on page load.
listen("load",window,tb2.start);
// -->
</script>

<div id="txb"><div id="bb"><div id="explain">bbcode</div><ul id="bbbuttons">'.

/* Here is the order of items */

tb2_htmlcode($tb2Buttons,$initialSmiliesCount).


'<li><div title="Textboxer bbcode editor 2.0 by Arkkimaagi" id="bba"></div></li>
</ul><div class="clearBoth"></div></div><textarea name="'.$name.'" id="'.$name.'" rows="'.$rows.'" cols="'.$cols.'">'.$content.'</textarea><div id="preview"></div></div>';

  return $result;
}

/* ---------------------------------------------------------------------------------------------------------------------- */

/* Creates datatable for javascript */
function tb2_jscode($tb2Buttons,$initialSmiliesCount){
  	global $L, $sed_smilies;

	$res=tb2_getSettings();

	$result=array();
	$dropdownID=0;
	foreach($tb2Buttons as $btn){
		if(is_int($btn)){
			$dropdownID=$btn;
		}else if($btn=="}"){
			$dropdownID=0;
		}else if($btn=='smilies'){
			if (is_array($sed_smilies)){
				$count=0;
				reset ($sed_smilies);
				while (list($i,$dat) = each($sed_smilies)){
					if($count<$initialSmiliesCount){
						$smilie_image = str_replace("'","\\\\'",str_replace("\\","\\\\\\\\",$dat['smilie_image']));
						$smilie_code = str_replace("'","\\\\'",str_replace("\\","\\\\\\\\\\\\\\\\",$dat['smilie_code']));
						$smilie_text = str_replace('"','\\\\"',str_replace("\\","\\\\\\\\",$dat['smilie_text']));
						array_push($result,"[-1,'sm_".$count."','','".$smilie_code."',\"".$smilie_text."\",1]");
					}
					$count++;
				}
			}
		}else if($btn!="tb_ieOnlyStart" && $btn!="tb_ieOnlyEnd"){
			$ex_text = str_replace('"','\\"',str_replace("\\","\\\\\\\\",$L["bbcodes_ex_".$btn]));
			array_push($result,"[".$res[$btn][2].",'".$btn."','".$res[$btn][0]."','".$res[$btn][1]."',\"".$ex_text."\",".$dropdownID."]");
		}
	}
	return implode(",\n",$result);
}

/* ---------------------------------------------------------------------------------------------------------------------- */

/* creates html buttons */
function tb2_htmlcode($tb2Buttons,$initialSmiliesCount){
  	global $L, $sed_smilies;

	$tb2L['moreSmilies']="More&nbsp;smilies..";

	$res=tb2_getSettings();

	$result=array();
	$dropdownID=0;
	foreach($tb2Buttons as $btn){
		if($btn=="tb_ieOnlyStart"){
			array_push($result,"<!--[if gte IE 5]>");
		}else if($btn=="tb_ieOnlyEnd"){
			array_push($result,"<![endif]-->");
		}else if($btn==1){
			$dropdownID=$btn;
			array_push($result,'<li><ul id="smileyDrop" class="dropdown"><li id="bbd_1" class="bbd"><span></span><ul id="smilies">');
		}else if(is_int($btn)){
			$dropdownID=$btn;
			array_push($result,'<li><ul class="dropdown"><li id="bbd_'.$dropdownID.'" class="bbd"><span></span><ul>');
		}else if($btn=="}"){
			$dropdownID=0;
			array_push($result,'</ul></li></ul></li>');
		}else if($btn=='smilies'){
			if (is_array($sed_smilies)){
				$count=0;
				reset ($sed_smilies);
				while (list($i,$dat) = each($sed_smilies)){
					if($count<$initialSmiliesCount){
						$smilie_text = str_replace('"','\\\\"',str_replace("\\","\\\\\\\\",$dat['smilie_text']));
						array_push($result,'<li><a href="javascript:none();" id="tb_sm_'.$count.'"><img src="'.$dat['smilie_image'].'" alt="'.$smilie_text.'" /></a></li>');
					}
					$count++;
				}
				if($count>$initialSmiliesCount){
					array_push($result,'<li><a href="javascript:none();" onclick="javascript:tb2.loadSmilies();">'.$tb2L['moreSmilies'].'</a></li>');
				}
			}
		}else if($dropdownID==0){
			array_push($result,'<li class="bbn"><a href="javascript:none();" id="tb_'.$btn.'"><span></span></a></li>');
		}else{
			array_push($result,'<li><a href="javascript:none();" id="tb_'.$btn.'"><span></span>'.$L["bbcodes_".$btn].' '.$res[$btn][0].''.$res[$btn][1].'</a></li>');
		}
	}
	return implode("\n",$result);
}

?>
