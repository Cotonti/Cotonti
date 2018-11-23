<?php
/**
 * Static and dynamic resource (e.g. HTML) strings. Can be overriden by skin files and other code.
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

/**
 * Form generation
 */
$R['code_option_empty'] = '---';
$R['code_time_separator'] = ':';
$R['input_checkbox'] = '<input type="hidden" name="{$name}" value="{$value_off}" /><label><input type="checkbox" name="{$name}" value="{$value}"{$checked}{$attrs} /> {$title}</label>';
$R['input_check'] = '<label><input type="checkbox" name="{$name}" value="{$value}"{$checked}{$attrs} /> {$title}</label>';
$R['input_default'] = '<input type="{$type}" name="{$name}" value="{$value}"{$attrs} />{$error}';
$R['input_option'] = '<option value="{$value}"{$selected}>{$title}</option>';
$R['input_radio'] = '<label><input type="radio" name="{$name}" value="{$value}"{$checked}{$attrs} /> {$title}</label>';
$R['input_radio_separator'] = ' ';
$R['input_select'] = '<select name="{$name}"{$attrs}>{$options}</select>{$error}';
$R['input_submit'] = '<button type="submit" name="{$name}" {$attrs}>{$value}</button>';
$R['input_text'] = '<input type="text" name="{$name}" value="{$value}" {$attrs} />{$error}';
$R['input_textarea'] = '<textarea name="{$name}" rows="{$rows}" cols="{$cols}"{$attrs}>{$value}</textarea>{$error}';
$R['input_textarea_editor'] =  '<textarea class="editor" name="{$name}" rows="{$rows}" cols="{$cols}"{$attrs}>{$value}</textarea>{$error}';
$R['input_textarea_medieditor'] =  '<textarea class="medieditor" name="{$name}" rows="{$rows}" cols="{$cols}"{$attrs}>{$value}</textarea>{$error}';
$R['input_textarea_minieditor'] =  '<textarea class="minieditor" name="{$name}" rows="{$rows}" cols="{$cols}"{$attrs}>{$value}</textarea>{$error}';
$R['input_filebox'] = '<a href="{$filepath}">{$value}</a><br /><input type="file" name="{$name}" {$attrs} /><br /><label><input type="checkbox" name="{$delname}" value="1" /> '.cot::$L['Delete'].'</label>{$error}';
$R['input_filebox_empty'] = '<input type="file" name="{$name}" {$attrs} />{$error}';

$R['input_date'] =  '{$day} {$month} {$year} {$hour}: {$minute}';
$R['input_date_short'] =  '{$day} {$month} {$year}';

/**
 * Stars / Votes Icons
 */

$R['icon_rating_stars'] = '<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/vote{$val}.png" alt="{$val}" />';
$R['icon_stars'] = '<img class="icon" src="images/icons/'.$cfg['defaulticons'].'/stars{$val}.png" alt="{$val}" />';

/**
 * Pagination
 */

$R['code_title_page_num'] = ' (' . $L['Page'] . ' {$num})';
$R['link_pagenav_current'] = '<span class="pagenav pagenav_current"><a href="{$url}"{$event}{$rel}>{$num}</a></span>';
$R['link_pagenav_first'] = '<span class="pagenav pagenav_first"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_first'].'</a></span>';
$R['link_pagenav_gap'] = '<span class="pagenav pagenav_gap">...</span>';
$R['link_pagenav_last'] = '<span class="pagenav pagenav pagenav_last"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_last'].'</a></span>';
$R['link_pagenav_main'] = '<span class="pagenav pagenav_pages"><a href="{$url}"{$event}{$rel}>{$num}</a></span>';
$R['link_pagenav_next'] = '<span class="pagenav pagenav_next"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_next'].'</a></span>';
$R['link_pagenav_prev'] = '<span class="pagenav pagenav_prev"><a href="{$url}"{$event}{$rel}>'.$L['pagenav_prev'].'</a></span>';

/**
 * Header
 */

$R['code_basehref'] = '<base href="'.$cfg['mainurl'].'/" />';
$R['code_noindex'] = '<meta name="robots" content="noindex" />';

$R['form_guest_remember'] = '<input type="checkbox" name="rremember" />';
$R['form_guest_remember_forced'] = '<input type="checkbox" name="rremember" checked="checked" disabled="disabled" />';
$R['form_guest_password'] = '<input type="password" name="rpassword" size="12" maxlength="32" />';
$R['form_guest_username'] = '<input type="text" name="rusername" size="12" maxlength="100" />';

/**
 * Messages
 */
$R['msg_code_153_date'] = '<br />(-&gt; {$date}GMT)';
$R['msg_code_redir_head'] = '<meta http-equiv="refresh" content="{$delay};url={$url}" />';

/**
 * Error handling
 */

$R['code_error_separator'] = '<br />';
$R['code_msg_begin'] = '<ul class="{$class}">';
$R['code_msg_end'] = '</ul>';
$R['code_msg_line'] = '<li><span class="{$class}">{$text}</span></li>';
$R['code_msg_inline'] = '<span class="{$class}">{$text}</span>';

/**
 * Header/footer resources
 */
$R['notices_container'] = '{$notices}';
$R['notices_separator'] = '';
$R['notices_link'] = '<a href="{$url}" title="{$title}">{$title}</a>';
$R['notices_plain'] = '{$title}';
$R['notices_notice'] = '{$notice}';
$R['code_rc_css_embed'] = '<style type="text/css">
/*<![CDATA[*/
{$code}
/*]]>*/
</style>';
$R['code_rc_css_file'] = '<link href="{$url}" type="text/css" rel="stylesheet" />';
$R['code_rc_js_embed'] = '<script>
//<![CDATA[
{$code}
//]]>
</script>';
$R['code_rc_js_file'] = '<script src="{$url}"></script>';

/**
 * Misc
 */

$R['icon_flag'] = '<img class="flag" src="images/flags/{$code}.png" alt="{$alt}" />';
$R['icon_group'] = '<img src="{$src}" alt="'.$L['Group'].'" />';
$R['img_none'] = '<img src="{$src}" alt="'.$L['Image'].'" />';
$R['img_pixel'] = '<img src="images/pixel.gif" width="{$x}" height="{$y}" alt="" />';
$R['img_smilie'] = '<img src="{$src}" alt="{$name}" class="icon" />';
$R['link_catpath'] = '<a href="{$url}" title="{$title}">{$title}</a>';
$R['string_catpath'] = '<span>{$title}</span>';
$R['link_email'] = '<a href="mailto:{$email}">{$email}</a>';


/**
 * Structure
 */
$R['img_structure_cat'] = '<img src="{$icon}" alt="{$title}" title="{$desc}" />';

/**
 * Timezones (countrycode, GMT offset, GMT offset with DST).
 * Used for finding timezones based on countrycode or offset.
 * Necessary because the $country param in DateTimeZone::listIdentifiers is not
 * supported in PHP 5.2.
 *
 * @deprecated PHP 5.2 don't uses any more
 */
$cot_timezones = array(

	"Africa/Abidjan" => array("ci", 0, 0),
	"Africa/Accra" => array("gh", 0, 0),
	"Africa/Addis_Ababa" => array("et", 10800, 10800),
	"Africa/Algiers" => array("dz", 3600, 3600),
	"Africa/Asmara" => array("er", 10800, 10800),
	"Africa/Bamako" => array("ml", 0, 0),
	"Africa/Bangui" => array("cf", 3600, 3600),
	"Africa/Banjul" => array("gm", 0, 0),
	"Africa/Bissau" => array("gw", 0, 0),
	"Africa/Blantyre" => array("mw", 7200, 7200),
	"Africa/Brazzaville" => array("cg", 3600, 3600),
	"Africa/Bujumbura" => array("bi", 7200, 7200),
	"Africa/Cairo" => array("eg", 7200, 7200),
	"Africa/Casablanca" => array("ma", 0, 3600),
	"Africa/Ceuta" => array("es", 3600, 7200),
	"Africa/Conakry" => array("gn", 0, 0),
	"Africa/Dakar" => array("sn", 0, 0),
	"Africa/Dar_es_Salaam" => array("tz", 10800, 10800),
	"Africa/Djibouti" => array("dj", 10800, 10800),
	"Africa/Douala" => array("cm", 3600, 3600),
	"Africa/El_Aaiun" => array("eh", 0, 0),
	"Africa/Freetown" => array("sl", 0, 0),
	"Africa/Gaborone" => array("bw", 7200, 7200),
	"Africa/Harare" => array("zw", 7200, 7200),
	"Africa/Johannesburg" => array("za", 7200, 7200),
	"Africa/Juba" => array("ss", 10800, 10800),
	"Africa/Kampala" => array("ug", 10800, 10800),
	"Africa/Khartoum" => array("sd", 10800, 10800),
	"Africa/Kigali" => array("rw", 7200, 7200),
	"Africa/Kinshasa" => array("cd", 3600, 3600),
	"Africa/Lagos" => array("ng", 3600, 3600),
	"Africa/Libreville" => array("ga", 3600, 3600),
	"Africa/Lome" => array("tg", 0, 0),
	"Africa/Luanda" => array("ao", 3600, 3600),
	"Africa/Lubumbashi" => array("cd", 7200, 7200),
	"Africa/Lusaka" => array("zm", 7200, 7200),
	"Africa/Malabo" => array("gq", 3600, 3600),
	"Africa/Maputo" => array("mz", 7200, 7200),
	"Africa/Maseru" => array("ls", 7200, 7200),
	"Africa/Mbabane" => array("sz", 7200, 7200),
	"Africa/Mogadishu" => array("so", 10800, 10800),
	"Africa/Monrovia" => array("lr", 0, 0),
	"Africa/Nairobi" => array("ke", 10800, 10800),
	"Africa/Ndjamena" => array("td", 3600, 3600),
	"Africa/Niamey" => array("ne", 3600, 3600),
	"Africa/Nouakchott" => array("mr", 0, 0),
	"Africa/Ouagadougou" => array("bf", 0, 0),
	"Africa/Porto-Novo" => array("bj", 3600, 3600),
	"Africa/Sao_Tome" => array("st", 0, 0),
	"Africa/Tripoli" => array("ly", 7200, 7200),
	"Africa/Tunis" => array("tn", 3600, 3600),
	"Africa/Windhoek" => array("na", 3600, 7200),
	"America/Adak" => array("us", -36000, -32400),
	"America/Anchorage" => array("us", -32400, -28800),
	"America/Anguilla" => array("ai", -14400, -14400),
	"America/Antigua" => array("ag", -14400, -14400),
	"America/Araguaina" => array("br", -10800, -10800),
	"America/Argentina/Buenos_Aires" => array("ar", -10800, -10800),
	"America/Argentina/Catamarca" => array("ar", -10800, -10800),
	"America/Argentina/Cordoba" => array("ar", -10800, -10800),
	"America/Argentina/Jujuy" => array("ar", -10800, -10800),
	"America/Argentina/La_Rioja" => array("ar", -10800, -10800),
	"America/Argentina/Mendoza" => array("ar", -10800, -10800),
	"America/Argentina/Rio_Gallegos" => array("ar", -10800, -10800),
	"America/Argentina/Salta" => array("ar", -10800, -10800),
	"America/Argentina/San_Juan" => array("ar", -10800, -10800),
	"America/Argentina/San_Luis" => array("ar", -10800, -10800),
	"America/Argentina/Tucuman" => array("ar", -10800, -10800),
	"America/Argentina/Ushuaia" => array("ar", -10800, -10800),
	"America/Aruba" => array("aw", -14400, -14400),
	"America/Asuncion" => array("py", -14400, -10800),
	"America/Atikokan" => array("ca", -18000, -18000),
	"America/Bahia" => array("br", -10800, -7200),
	"America/Bahia_Banderas" => array("mx", -21600, -18000),
	"America/Barbados" => array("bb", -14400, -14400),
	"America/Belem" => array("br", -10800, -10800),
	"America/Belize" => array("bz", -21600, -21600),
	"America/Blanc-Sablon" => array("ca", -14400, -14400),
	"America/Boa_Vista" => array("br", -14400, -14400),
	"America/Bogota" => array("co", -18000, -18000),
	"America/Boise" => array("us", -25200, -21600),
	"America/Cambridge_Bay" => array("ca", -25200, -21600),
	"America/Campo_Grande" => array("br", -14400, -10800),
	"America/Cancun" => array("mx", -21600, -18000),
	"America/Caracas" => array("ve", -16200, -16200),
	"America/Cayenne" => array("gf", -10800, -10800),
	"America/Cayman" => array("ky", -18000, -18000),
	"America/Chicago" => array("us", -21600, -18000),
	"America/Chihuahua" => array("mx", -25200, -21600),
	"America/Costa_Rica" => array("cr", -21600, -21600),
	"America/Creston" => array("ca", -25200, -25200),
	"America/Cuiaba" => array("br", -14400, -10800),
	"America/Curacao" => array("cw", -14400, -14400),
	"America/Danmarkshavn" => array("gl", 0, 0),
	"America/Dawson" => array("ca", -28800, -25200),
	"America/Dawson_Creek" => array("ca", -25200, -25200),
	"America/Denver" => array("us", -25200, -21600),
	"America/Detroit" => array("us", -18000, -14400),
	"America/Dominica" => array("dm", -14400, -14400),
	"America/Edmonton" => array("ca", -25200, -21600),
	"America/Eirunepe" => array("br", -14400, -14400),
	"America/El_Salvador" => array("sv", -21600, -21600),
	"America/Fortaleza" => array("br", -10800, -10800),
	"America/Glace_Bay" => array("ca", -14400, -10800),
	"America/Godthab" => array("gl", -10800, -7200),
	"America/Goose_Bay" => array("ca", -14400, -10800),
	"America/Grand_Turk" => array("tc", -18000, -14400),
	"America/Grenada" => array("gd", -14400, -14400),
	"America/Guadeloupe" => array("gp", -14400, -14400),
	"America/Guatemala" => array("gt", -21600, -21600),
	"America/Guayaquil" => array("ec", -18000, -18000),
	"America/Guyana" => array("gy", -14400, -14400),
	"America/Halifax" => array("ca", -14400, -10800),
	"America/Havana" => array("cu", -18000, -14400),
	"America/Hermosillo" => array("mx", -25200, -25200),
	"America/Indiana/Indianapolis" => array("us", -18000, -14400),
	"America/Indiana/Knox" => array("us", -21600, -18000),
	"America/Indiana/Marengo" => array("us", -18000, -14400),
	"America/Indiana/Petersburg" => array("us", -18000, -14400),
	"America/Indiana/Tell_City" => array("us", -21600, -18000),
	"America/Indiana/Vevay" => array("us", -18000, -14400),
	"America/Indiana/Vincennes" => array("us", -18000, -14400),
	"America/Indiana/Winamac" => array("us", -18000, -14400),
	"America/Inuvik" => array("ca", -25200, -21600),
	"America/Iqaluit" => array("ca", -18000, -14400),
	"America/Jamaica" => array("jm", -18000, -18000),
	"America/Juneau" => array("us", -32400, -28800),
	"America/Kentucky/Louisville" => array("us", -18000, -14400),
	"America/Kentucky/Monticello" => array("us", -18000, -14400),
	"America/Kralendijk" => array("bq", -14400, -14400),
	"America/La_Paz" => array("bo", -14400, -14400),
	"America/Lima" => array("pe", -18000, -18000),
	"America/Los_Angeles" => array("us", -28800, -25200),
	"America/Lower_Princes" => array("sx", -14400, -14400),
	"America/Maceio" => array("br", -10800, -10800),
	"America/Managua" => array("ni", -21600, -21600),
	"America/Manaus" => array("br", -14400, -14400),
	"America/Marigot" => array("mf", -14400, -14400),
	"America/Martinique" => array("mq", -14400, -14400),
	"America/Matamoros" => array("mx", -21600, -18000),
	"America/Mazatlan" => array("mx", -25200, -21600),
	"America/Menominee" => array("us", -21600, -18000),
	"America/Merida" => array("mx", -21600, -18000),
	"America/Metlakatla" => array("us", -28800, -28800),
	"America/Mexico_City" => array("mx", -21600, -18000),
	"America/Miquelon" => array("pm", -10800, -7200),
	"America/Moncton" => array("ca", -14400, -10800),
	"America/Monterrey" => array("mx", -21600, -18000),
	"America/Montevideo" => array("uy", -10800, -7200),
	"America/Montreal" => array("ca", -18000, -14400),
	"America/Montserrat" => array("ms", -14400, -14400),
	"America/Nassau" => array("bs", -18000, -14400),
	"America/New_York" => array("us", -18000, -14400),
	"America/Nipigon" => array("ca", -18000, -14400),
	"America/Nome" => array("us", -32400, -28800),
	"America/Noronha" => array("br", -7200, -7200),
	"America/North_Dakota/Beulah" => array("us", -21600, -18000),
	"America/North_Dakota/Center" => array("us", -21600, -18000),
	"America/North_Dakota/New_Salem" => array("us", -21600, -18000),
	"America/Ojinaga" => array("mx", -25200, -21600),
	"America/Panama" => array("pa", -18000, -18000),
	"America/Pangnirtung" => array("ca", -18000, -14400),
	"America/Paramaribo" => array("sr", -10800, -10800),
	"America/Phoenix" => array("us", -25200, -25200),
	"America/Port-au-Prince" => array("ht", -18000, -14400),
	"America/Port_of_Spain" => array("tt", -14400, -14400),
	"America/Porto_Velho" => array("br", -14400, -14400),
	"America/Puerto_Rico" => array("pr", -14400, -14400),
	"America/Rainy_River" => array("ca", -21600, -18000),
	"America/Rankin_Inlet" => array("ca", -21600, -18000),
	"America/Recife" => array("br", -10800, -10800),
	"America/Regina" => array("ca", -21600, -21600),
	"America/Resolute" => array("ca", -21600, -18000),
	"America/Rio_Branco" => array("br", -14400, -14400),
	"America/Santa_Isabel" => array("mx", -28800, -25200),
	"America/Santarem" => array("br", -10800, -10800),
	"America/Santiago" => array("cl", -14400, -10800),
	"America/Santo_Domingo" => array("do", -14400, -14400),
	"America/Sao_Paulo" => array("br", -10800, -7200),
	"America/Scoresbysund" => array("gl", -3600, 0),
	"America/Sitka" => array("us", -32400, -28800),
	"America/St_Barthelemy" => array("bl", -14400, -14400),
	"America/St_Johns" => array("ca", -12600, -9000),
	"America/St_Kitts" => array("kn", -14400, -14400),
	"America/St_Lucia" => array("lc", -14400, -14400),
	"America/St_Thomas" => array("vi", -14400, -14400),
	"America/St_Vincent" => array("vc", -14400, -14400),
	"America/Swift_Current" => array("ca", -21600, -21600),
	"America/Tegucigalpa" => array("hn", -21600, -21600),
	"America/Thule" => array("gl", -14400, -10800),
	"America/Thunder_Bay" => array("ca", -18000, -14400),
	"America/Tijuana" => array("mx", -28800, -25200),
	"America/Toronto" => array("ca", -18000, -14400),
	"America/Tortola" => array("vg", -14400, -14400),
	"America/Vancouver" => array("ca", -28800, -25200),
	"America/Whitehorse" => array("ca", -28800, -25200),
	"America/Winnipeg" => array("ca", -21600, -18000),
	"America/Yakutat" => array("us", -32400, -28800),
	"America/Yellowknife" => array("ca", -25200, -21600),
	"Antarctica/Casey" => array("aq", 39600, 28800),
	"Antarctica/Davis" => array("aq", 18000, 25200),
	"Antarctica/DumontDUrville" => array("aq", 36000, 36000),
	"Antarctica/Macquarie" => array("aq", 39600, 39600),
	"Antarctica/Mawson" => array("aq", 18000, 18000),
	"Antarctica/McMurdo" => array("aq", 43200, 46800),
	"Antarctica/Palmer" => array("aq", -14400, -10800),
	"Antarctica/Rothera" => array("aq", -10800, -10800),
	"Antarctica/Syowa" => array("aq", 10800, 10800),
	"Antarctica/Vostok" => array("aq", 21600, 21600),
	"Arctic/Longyearbyen" => array("sj", 3600, 7200),
	"Asia/Aden" => array("ye", 10800, 10800),
	"Asia/Almaty" => array("kz", 21600, 21600),
	"Asia/Amman" => array("jo", 7200, 10800),
	"Asia/Anadyr" => array("ru", 43200, 43200),
	"Asia/Aqtau" => array("kz", 18000, 18000),
	"Asia/Aqtobe" => array("kz", 18000, 18000),
	"Asia/Ashgabat" => array("tm", 18000, 18000),
	"Asia/Baghdad" => array("iq", 10800, 10800),
	"Asia/Bahrain" => array("bh", 10800, 10800),
	"Asia/Baku" => array("az", 14400, 18000),
	"Asia/Bangkok" => array("th", 25200, 25200),
	"Asia/Beirut" => array("lb", 7200, 10800),
	"Asia/Bishkek" => array("kg", 21600, 21600),
	"Asia/Brunei" => array("bn", 28800, 28800),
	"Asia/Choibalsan" => array("mn", 28800, 28800),
	"Asia/Chongqing" => array("cn", 28800, 28800),
	"Asia/Colombo" => array("lk", 19800, 19800),
	"Asia/Damascus" => array("sy", 7200, 10800),
	"Asia/Dhaka" => array("bd", 21600, 21600),
	"Asia/Dili" => array("tl", 32400, 32400),
	"Asia/Dubai" => array("ae", 14400, 14400),
	"Asia/Dushanbe" => array("tj", 18000, 18000),
	"Asia/Gaza" => array("ps", 7200, 10800),
	"Asia/Harbin" => array("cn", 28800, 28800),
	"Asia/Hebron" => array("ps", 7200, 10800),
	"Asia/Ho_Chi_Minh" => array("vn", 25200, 25200),
	"Asia/Hong_Kong" => array("hk", 28800, 28800),
	"Asia/Hovd" => array("mn", 25200, 25200),
	"Asia/Irkutsk" => array("ru", 32400, 32400),
	"Asia/Jakarta" => array("id", 25200, 25200),
	"Asia/Jayapura" => array("id", 32400, 32400),
	"Asia/Jerusalem" => array("il", 7200, 10800),
	"Asia/Kabul" => array("af", 16200, 16200),
	"Asia/Kamchatka" => array("ru", 43200, 43200),
	"Asia/Karachi" => array("pk", 18000, 18000),
	"Asia/Kashgar" => array("cn", 28800, 28800),
	"Asia/Kathmandu" => array("np", 20700, 20700),
	"Asia/Kolkata" => array("in", 19800, 19800),
	"Asia/Krasnoyarsk" => array("ru", 28800, 28800),
	"Asia/Kuala_Lumpur" => array("my", 28800, 28800),
	"Asia/Kuching" => array("my", 28800, 28800),
	"Asia/Kuwait" => array("kw", 10800, 10800),
	"Asia/Macau" => array("mo", 28800, 28800),
	"Asia/Magadan" => array("ru", 43200, 43200),
	"Asia/Makassar" => array("id", 28800, 28800),
	"Asia/Manila" => array("ph", 28800, 28800),
	"Asia/Muscat" => array("om", 14400, 14400),
	"Asia/Nicosia" => array("cy", 7200, 10800),
	"Asia/Novokuznetsk" => array("ru", 25200, 25200),
	"Asia/Novosibirsk" => array("ru", 25200, 25200),
	"Asia/Omsk" => array("ru", 25200, 25200),
	"Asia/Oral" => array("kz", 18000, 18000),
	"Asia/Phnom_Penh" => array("kh", 25200, 25200),
	"Asia/Pontianak" => array("id", 25200, 25200),
	"Asia/Pyongyang" => array("kp", 32400, 32400),
	"Asia/Qatar" => array("qa", 10800, 10800),
	"Asia/Qyzylorda" => array("kz", 21600, 21600),
	"Asia/Rangoon" => array("mm", 23400, 23400),
	"Asia/Riyadh" => array("sa", 10800, 10800),
	"Asia/Sakhalin" => array("ru", 39600, 39600),
	"Asia/Samarkand" => array("uz", 18000, 18000),
	"Asia/Seoul" => array("kr", 32400, 32400),
	"Asia/Shanghai" => array("cn", 28800, 28800),
	"Asia/Singapore" => array("sg", 28800, 28800),
	"Asia/Taipei" => array("tw", 28800, 28800),
	"Asia/Tashkent" => array("uz", 18000, 18000),
	"Asia/Tbilisi" => array("ge", 14400, 14400),
	"Asia/Tehran" => array("ir", 12600, 16200),
	"Asia/Thimphu" => array("bt", 21600, 21600),
	"Asia/Tokyo" => array("jp", 32400, 32400),
	"Asia/Ulaanbaatar" => array("mn", 28800, 28800),
	"Asia/Urumqi" => array("cn", 28800, 28800),
	"Asia/Vientiane" => array("la", 25200, 25200),
	"Asia/Vladivostok" => array("ru", 39600, 39600),
	"Asia/Yakutsk" => array("ru", 36000, 36000),
	"Asia/Yekaterinburg" => array("ru", 21600, 21600),
	"Asia/Yerevan" => array("am", 14400, 14400),
	"Atlantic/Azores" => array("pt", -3600, 0),
	"Atlantic/Bermuda" => array("bm", -14400, -10800),
	"Atlantic/Canary" => array("es", 0, 3600),
	"Atlantic/Cape_Verde" => array("cv", -3600, -3600),
	"Atlantic/Faroe" => array("fo", 0, 3600),
	"Atlantic/Madeira" => array("pt", 0, 3600),
	"Atlantic/Reykjavik" => array("is", 0, 0),
	"Atlantic/South_Georgia" => array("gs", -7200, -7200),
	"Atlantic/St_Helena" => array("sh", 0, 0),
	"Atlantic/Stanley" => array("fk", -10800, -10800),
	"Australia/Adelaide" => array("au", 34200, 37800),
	"Australia/Brisbane" => array("au", 36000, 36000),
	"Australia/Broken_Hill" => array("au", 34200, 37800),
	"Australia/Currie" => array("au", 36000, 39600),
	"Australia/Darwin" => array("au", 34200, 34200),
	"Australia/Eucla" => array("au", 31500, 31500),
	"Australia/Hobart" => array("au", 36000, 39600),
	"Australia/Lindeman" => array("au", 36000, 36000),
	"Australia/Lord_Howe" => array("au", 37800, 39600),
	"Australia/Melbourne" => array("au", 36000, 39600),
	"Australia/Perth" => array("au", 28800, 28800),
	"Australia/Sydney" => array("au", 36000, 39600),
	"Europe/Amsterdam" => array("nl", 3600, 7200),
	"Europe/Andorra" => array("ad", 3600, 7200),
	"Europe/Athens" => array("gr", 7200, 10800),
	"Europe/Belgrade" => array("rs", 3600, 7200),
	"Europe/Berlin" => array("de", 3600, 7200),
	"Europe/Bratislava" => array("sk", 3600, 7200),
	"Europe/Brussels" => array("be", 3600, 7200),
	"Europe/Bucharest" => array("ro", 7200, 10800),
	"Europe/Budapest" => array("hu", 3600, 7200),
	"Europe/Chisinau" => array("md", 7200, 10800),
	"Europe/Copenhagen" => array("dk", 3600, 7200),
	"Europe/Dublin" => array("ie", 0, 3600),
	"Europe/Gibraltar" => array("gi", 3600, 7200),
	"Europe/Guernsey" => array("gg", 0, 3600),
	"Europe/Helsinki" => array("fi", 7200, 10800),
	"Europe/Isle_of_Man" => array("im", 0, 3600),
	"Europe/Istanbul" => array("tr", 7200, 10800),
	"Europe/Jersey" => array("je", 0, 3600),
	"Europe/Kaliningrad" => array("ru", 10800, 10800),
	"Europe/Kiev" => array("ua", 7200, 10800),
	"Europe/Lisbon" => array("pt", 0, 3600),
	"Europe/Ljubljana" => array("si", 3600, 7200),
	"Europe/London" => array("uk", 0, 3600),
	"Europe/Luxembourg" => array("lu", 3600, 7200),
	"Europe/Madrid" => array("es", 3600, 7200),
	"Europe/Malta" => array("mt", 3600, 7200),
	"Europe/Mariehamn" => array("ax", 7200, 10800),
	"Europe/Minsk" => array("by", 10800, 10800),
	"Europe/Monaco" => array("mc", 3600, 7200),
	"Europe/Moscow" => array("ru", 14400, 14400),
	"Europe/Oslo" => array("no", 3600, 7200),
	"Europe/Paris" => array("fr", 3600, 7200),
	"Europe/Podgorica" => array("me", 3600, 7200),
	"Europe/Prague" => array("cz", 3600, 7200),
	"Europe/Riga" => array("lv", 7200, 10800),
	"Europe/Rome" => array("it", 3600, 7200),
	"Europe/Samara" => array("ru", 14400, 14400),
	"Europe/San_Marino" => array("sm", 3600, 7200),
	"Europe/Sarajevo" => array("ba", 3600, 7200),
	"Europe/Simferopol" => array("ua", 7200, 10800),
	"Europe/Skopje" => array("mk", 3600, 7200),
	"Europe/Sofia" => array("bg", 7200, 10800),
	"Europe/Stockholm" => array("se", 3600, 7200),
	"Europe/Tallinn" => array("ee", 7200, 10800),
	"Europe/Tirane" => array("al", 3600, 7200),
	"Europe/Uzhgorod" => array("ua", 7200, 10800),
	"Europe/Vaduz" => array("li", 3600, 7200),
	"Europe/Vatican" => array("va", 3600, 7200),
	"Europe/Vienna" => array("at", 3600, 7200),
	"Europe/Vilnius" => array("lt", 7200, 10800),
	"Europe/Volgograd" => array("ru", 14400, 14400),
	"Europe/Warsaw" => array("pl", 3600, 7200),
	"Europe/Zagreb" => array("hr", 3600, 7200),
	"Europe/Zaporozhye" => array("ua", 7200, 10800),
	"Europe/Zurich" => array("ch", 3600, 7200),
	"Indian/Antananarivo" => array("mg", 10800, 10800),
	"Indian/Chagos" => array("io", 21600, 21600),
	"Indian/Christmas" => array("cx", 25200, 25200),
	"Indian/Cocos" => array("cc", 23400, 23400),
	"Indian/Comoro" => array("km", 10800, 10800),
	"Indian/Kerguelen" => array("tf", 18000, 18000),
	"Indian/Mahe" => array("sc", 14400, 14400),
	"Indian/Maldives" => array("mv", 18000, 18000),
	"Indian/Mauritius" => array("mu", 14400, 14400),
	"Indian/Mayotte" => array("yt", 10800, 10800),
	"Indian/Reunion" => array("re", 14400, 14400),
	"Pacific/Apia" => array("ws", 46800, 50400),
	"Pacific/Auckland" => array("nz", 43200, 46800),
	"Pacific/Chatham" => array("nz", 45900, 49500),
	"Pacific/Chuuk" => array("fm", 36000, 36000),
	"Pacific/Easter" => array("cl", -21600, -18000),
	"Pacific/Efate" => array("vu", 39600, 39600),
	"Pacific/Enderbury" => array("ki", 46800, 46800),
	"Pacific/Fakaofo" => array("tk", 50400, 50400),
	"Pacific/Fiji" => array("fj", 43200, 46800),
	"Pacific/Funafuti" => array("tv", 43200, 43200),
	"Pacific/Galapagos" => array("ec", -21600, -21600),
	"Pacific/Gambier" => array("pf", -32400, -32400),
	"Pacific/Guadalcanal" => array("sb", 39600, 39600),
	"Pacific/Guam" => array("gu", 36000, 36000),
	"Pacific/Honolulu" => array("us", -36000, -36000),
	"Pacific/Johnston" => array("um", -36000, -36000),
	"Pacific/Kiritimati" => array("ki", 50400, 50400),
	"Pacific/Kosrae" => array("fm", 39600, 39600),
	"Pacific/Kwajalein" => array("mh", 43200, 43200),
	"Pacific/Majuro" => array("mh", 43200, 43200),
	"Pacific/Marquesas" => array("pf", -34200, -34200),
	"Pacific/Midway" => array("um", -39600, -39600),
	"Pacific/Nauru" => array("nr", 43200, 43200),
	"Pacific/Niue" => array("nu", -39600, -39600),
	"Pacific/Norfolk" => array("nf", 41400, 41400),
	"Pacific/Noumea" => array("nc", 39600, 39600),
	"Pacific/Pago_Pago" => array("as", -39600, -39600),
	"Pacific/Palau" => array("pw", 32400, 32400),
	"Pacific/Pitcairn" => array("pn", -28800, -28800),
	"Pacific/Pohnpei" => array("fm", 39600, 39600),
	"Pacific/Port_Moresby" => array("pg", 36000, 36000),
	"Pacific/Rarotonga" => array("ck", -36000, -36000),
	"Pacific/Saipan" => array("mp", 36000, 36000),
	"Pacific/Tahiti" => array("pf", -36000, -36000),
	"Pacific/Tarawa" => array("ki", 43200, 43200),
	"Pacific/Tongatapu" => array("to", 46800, 46800),
	"Pacific/Wake" => array("um", 43200, 43200),
	"Pacific/Wallis" => array("wf", 43200, 43200)
);
