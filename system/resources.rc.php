<?php
/**
 * Static and dynamic resource (e.g. HTML) strings. Can be overriden by theme files and other code.
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');

// Todo may be use Cot::$L, Cot::$cfg instead?
global $L, $cfg;

if (!isset($L['Delete']))  {
    include cot_langfile('main', 'core');
}

/**
 * Form generation
 */
$R['code_option_empty'] = '---';
$R['code_time_separator'] = ':';
$R['input_checkbox'] = '<input type="hidden" name="{$name}" value="{$value_off}" /><label><input type="checkbox" name="{$name}" value="{$value}"{$checked}{$attrs} /> {$title}</label>';
$R['input_check'] = '<label><input type="checkbox" name="{$name}" value="{$value}"{$checked}{$attrs} /> {$title}</label>';
$R['input_default'] = '<input type="{$type}" name="{$name}" value="{$value}"{$attrs} />{$error}';
$R['input_option'] = '<option value="{$value}"{$selected}>{$title}</option>';
$R['input_radio'] = '<label class="radio-label"><input type="radio" name="{$name}" value="{$value}"{$checked}{$attrs} /> {$title}</label>';
$R['input_radio_separator'] = ' ';
$R['input_select'] = '<select name="{$name}"{$attrs}>{$options}</select>{$error}';
$R['input_submit'] = '<button type="submit" name="{$name}" {$attrs}>{$value}</button>';
$R['input_text'] = '<input type="text" name="{$name}" value="{$value}" {$attrs} />{$error}';
$R['input_textarea'] = '<textarea name="{$name}"' . (!empty($rows) ? ' rows="{$rows}"' : '') . (!empty($cols) ? ' cols="{$cols}"' : '') . ' {$attrs}>{$value}</textarea>{$error}';
$R['input_textarea_editor'] =  '<textarea class="editor" name="{$name}"' . (!empty($rows) ? ' rows="{$rows}"' : '') . (!empty($cols) ? ' cols="{$cols}"' : '') . '{$attrs}>{$value}</textarea>{$error}';
$R['input_textarea_medieditor'] =  '<textarea class="medieditor" name="{$name}"' . (!empty($rows) ? ' rows="{$rows}"' : '') . (!empty($cols) ? ' cols="{$cols}"' : '') . '{$attrs}>{$value}</textarea>{$error}';
$R['input_textarea_minieditor'] =  '<textarea class="minieditor" name="{$name}"' . (!empty($rows) ? ' rows="{$rows}"' : '') . (!empty($cols) ? ' cols="{$cols}"' : '') . '{$attrs}>{$value}</textarea>{$error}';
$R['input_filebox'] = '<a href="{$filepath}">{$value}</a><br /><input type="file" name="{$name}" {$attrs} /><br /><label><input type="checkbox" name="{$delname}" value="1" /> '.$L['Delete'].'</label>{$error}';
$R['input_filebox_empty'] = '<input type="file" name="{$name}" {$attrs} />{$error}';

$R['input_date'] =  '{$day} {$month} {$year} {$hour}: {$minute}';
$R['input_date_short'] =  '{$day} {$month} {$year}';

/**
 * Pagination
 */
$R['code_title_page_num'] = ' (' . $L['Page'] . ' {$num})';
$R['link_pagenav_current'] = '<span class="pagenav pagenav_current"><a href="{$url}" class="{$class}" {$rel}>{$num}</a></span>';
$R['link_pagenav_first'] = '<span class="pagenav pagenav_first"><a href="{$url}" class="{$class}" {$rel}>'.$L['pagenav_first'].'</a></span>';
$R['link_pagenav_gap'] = '<span class="pagenav pagenav_gap">...</span>';
$R['link_pagenav_last'] = '<span class="pagenav pagenav pagenav_last"><a href="{$url}" class="{$class}" {$rel}>'.$L['pagenav_last'].'</a></span>';
$R['link_pagenav_main'] = '<span class="pagenav pagenav_pages"><a href="{$url}" class="{$class}" {$rel}>{$num}</a></span>';
$R['link_pagenav_next'] = '<span class="pagenav pagenav_next"><a href="{$url}" class="{$class}" {$rel}>'.$L['pagenav_next'].'</a></span>';
$R['link_pagenav_prev'] = '<span class="pagenav pagenav_prev"><a href="{$url}" class="{$class}" {$rel}>'.$L['pagenav_prev'].'</a></span>';

/**
 * Sort
 */
$R['link_list_sort'] = '<a href="{$asc_url}" rel="nofollow">{$icon_down}</a> <a href="{$desc_url}" rel="nofollow">{$icon_up}</a> {$text}';

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
$R['code_rc_css_embed'] = '<style type="text/css"{$attr}>
/*<![CDATA[*/
{$code}
/*]]>*/
</style>';
$R['code_rc_css_file'] = '<link href="{$url}" type="text/css" rel="stylesheet" />';
$R['code_rc_js_embed'] = '<script{$attr}>
//<![CDATA[
{$code}
//]]>
</script>';
$R['code_rc_js_file'] = '<script src="{$url}"></script>';

/**
 * Misc
 */
$R['icon_flag'] = '<img class="flag" src="images/flags/{$code}.png" alt="{$alt}" />';
$R['icon_group'] = '<img src="{$src}" alt="' . $L['Group'] . '" />';
$R['icon_group'] = '<img src="{$src}" alt="' . $L['Group'] . '" />';
$R['img_none'] = '<img src="{$src}" alt="' . $L['Image'] . '" />';
$R['img_pixel'] = '<img src="images/pixel.gif" width="{$x}" height="{$y}" alt="" />';
$R['img_smilie'] = '<img src="{$src}" alt="{$name}" class="icon" />';
$R['link_catpath'] = '<a href="{$url}" title="{$title}">{$title}</a>';
$R['link_email'] = '<a href="mailto:{$email}">{$email}</a>';
$R['string_catpath'] = '<span>{$title}</span>';
$R['users_defaultAvatarSrc'] = 'images/blank-avatar.png';

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
 * @deprecated PHP 5.2 don't using anymore
 * @see https://www.php.net/manual/ru/datetimezone.listidentifiers.php
 * @see DateTimeZone::listIdentifiers()
 */
$cot_timezones = [
	"Africa/Abidjan" => ["ci", 0, 0],
	"Africa/Accra" => ["gh", 0, 0],
	"Africa/Addis_Ababa" => ["et", 10800, 10800],
	"Africa/Algiers" => ["dz", 3600, 3600],
	"Africa/Asmara" => ["er", 10800, 10800],
	"Africa/Bamako" => ["ml", 0, 0],
	"Africa/Bangui" => ["cf", 3600, 3600],
	"Africa/Banjul" => ["gm", 0, 0],
	"Africa/Bissau" => ["gw", 0, 0],
	"Africa/Blantyre" => ["mw", 7200, 7200],
	"Africa/Brazzaville" => ["cg", 3600, 3600],
	"Africa/Bujumbura" => ["bi", 7200, 7200],
	"Africa/Cairo" => ["eg", 7200, 7200],
	"Africa/Casablanca" => ["ma", 0, 3600],
	"Africa/Ceuta" => ["es", 3600, 7200],
	"Africa/Conakry" => ["gn", 0, 0],
	"Africa/Dakar" => ["sn", 0, 0],
	"Africa/Dar_es_Salaam" => ["tz", 10800, 10800],
	"Africa/Djibouti" => ["dj", 10800, 10800],
	"Africa/Douala" => ["cm", 3600, 3600],
	"Africa/El_Aaiun" => ["eh", 0, 0],
	"Africa/Freetown" => ["sl", 0, 0],
	"Africa/Gaborone" => ["bw", 7200, 7200],
	"Africa/Harare" => ["zw", 7200, 7200],
	"Africa/Johannesburg" => ["za", 7200, 7200],
	"Africa/Juba" => ["ss", 10800, 10800],
	"Africa/Kampala" => ["ug", 10800, 10800],
	"Africa/Khartoum" => ["sd", 10800, 10800],
	"Africa/Kigali" => ["rw", 7200, 7200],
	"Africa/Kinshasa" => ["cd", 3600, 3600],
	"Africa/Lagos" => ["ng", 3600, 3600],
	"Africa/Libreville" => ["ga", 3600, 3600],
	"Africa/Lome" => ["tg", 0, 0],
	"Africa/Luanda" => ["ao", 3600, 3600],
	"Africa/Lubumbashi" => ["cd", 7200, 7200],
	"Africa/Lusaka" => ["zm", 7200, 7200],
	"Africa/Malabo" => ["gq", 3600, 3600],
	"Africa/Maputo" => ["mz", 7200, 7200],
	"Africa/Maseru" => ["ls", 7200, 7200],
	"Africa/Mbabane" => ["sz", 7200, 7200],
	"Africa/Mogadishu" => ["so", 10800, 10800],
	"Africa/Monrovia" => ["lr", 0, 0],
	"Africa/Nairobi" => ["ke", 10800, 10800],
	"Africa/Ndjamena" => ["td", 3600, 3600],
	"Africa/Niamey" => ["ne", 3600, 3600],
	"Africa/Nouakchott" => ["mr", 0, 0],
	"Africa/Ouagadougou" => ["bf", 0, 0],
	"Africa/Porto-Novo" => ["bj", 3600, 3600],
	"Africa/Sao_Tome" => ["st", 0, 0],
	"Africa/Tripoli" => ["ly", 7200, 7200],
	"Africa/Tunis" => ["tn", 3600, 3600],
	"Africa/Windhoek" => ["na", 3600, 7200],
	"America/Adak" => ["us", -36000, -32400],
	"America/Anchorage" => ["us", -32400, -28800],
	"America/Anguilla" => ["ai", -14400, -14400],
	"America/Antigua" => ["ag", -14400, -14400],
	"America/Araguaina" => ["br", -10800, -10800],
	"America/Argentina/Buenos_Aires" => ["ar", -10800, -10800],
	"America/Argentina/Catamarca" => ["ar", -10800, -10800],
	"America/Argentina/Cordoba" => ["ar", -10800, -10800],
	"America/Argentina/Jujuy" => ["ar", -10800, -10800],
	"America/Argentina/La_Rioja" => ["ar", -10800, -10800],
	"America/Argentina/Mendoza" => ["ar", -10800, -10800],
	"America/Argentina/Rio_Gallegos" => ["ar", -10800, -10800],
	"America/Argentina/Salta" => ["ar", -10800, -10800],
	"America/Argentina/San_Juan" => ["ar", -10800, -10800],
	"America/Argentina/San_Luis" => ["ar", -10800, -10800],
	"America/Argentina/Tucuman" => ["ar", -10800, -10800],
	"America/Argentina/Ushuaia" => ["ar", -10800, -10800],
	"America/Aruba" => ["aw", -14400, -14400],
	"America/Asuncion" => ["py", -14400, -10800],
	"America/Atikokan" => ["ca", -18000, -18000],
	"America/Bahia" => ["br", -10800, -7200],
	"America/Bahia_Banderas" => ["mx", -21600, -18000],
	"America/Barbados" => ["bb", -14400, -14400],
	"America/Belem" => ["br", -10800, -10800],
	"America/Belize" => ["bz", -21600, -21600],
	"America/Blanc-Sablon" => ["ca", -14400, -14400],
	"America/Boa_Vista" => ["br", -14400, -14400],
	"America/Bogota" => ["co", -18000, -18000],
	"America/Boise" => ["us", -25200, -21600],
	"America/Cambridge_Bay" => ["ca", -25200, -21600],
	"America/Campo_Grande" => ["br", -14400, -10800],
	"America/Cancun" => ["mx", -21600, -18000],
	"America/Caracas" => ["ve", -16200, -16200],
	"America/Cayenne" => ["gf", -10800, -10800],
	"America/Cayman" => ["ky", -18000, -18000],
	"America/Chicago" => ["us", -21600, -18000],
	"America/Chihuahua" => ["mx", -25200, -21600],
	"America/Costa_Rica" => ["cr", -21600, -21600],
	"America/Creston" => ["ca", -25200, -25200],
	"America/Cuiaba" => ["br", -14400, -10800],
	"America/Curacao" => ["cw", -14400, -14400],
	"America/Danmarkshavn" => ["gl", 0, 0],
	"America/Dawson" => ["ca", -28800, -25200],
	"America/Dawson_Creek" => ["ca", -25200, -25200],
	"America/Denver" => ["us", -25200, -21600],
	"America/Detroit" => ["us", -18000, -14400],
	"America/Dominica" => ["dm", -14400, -14400],
	"America/Edmonton" => ["ca", -25200, -21600],
	"America/Eirunepe" => ["br", -14400, -14400],
	"America/El_Salvador" => ["sv", -21600, -21600],
	"America/Fortaleza" => ["br", -10800, -10800],
	"America/Glace_Bay" => ["ca", -14400, -10800],
	"America/Godthab" => ["gl", -10800, -7200],
	"America/Goose_Bay" => ["ca", -14400, -10800],
	"America/Grand_Turk" => ["tc", -18000, -14400],
	"America/Grenada" => ["gd", -14400, -14400],
	"America/Guadeloupe" => ["gp", -14400, -14400],
	"America/Guatemala" => ["gt", -21600, -21600],
	"America/Guayaquil" => ["ec", -18000, -18000],
	"America/Guyana" => ["gy", -14400, -14400],
	"America/Halifax" => ["ca", -14400, -10800],
	"America/Havana" => ["cu", -18000, -14400],
	"America/Hermosillo" => ["mx", -25200, -25200],
	"America/Indiana/Indianapolis" => ["us", -18000, -14400],
	"America/Indiana/Knox" => ["us", -21600, -18000],
	"America/Indiana/Marengo" => ["us", -18000, -14400],
	"America/Indiana/Petersburg" => ["us", -18000, -14400],
	"America/Indiana/Tell_City" => ["us", -21600, -18000],
	"America/Indiana/Vevay" => ["us", -18000, -14400],
	"America/Indiana/Vincennes" => ["us", -18000, -14400],
	"America/Indiana/Winamac" => ["us", -18000, -14400],
	"America/Inuvik" => ["ca", -25200, -21600],
	"America/Iqaluit" => ["ca", -18000, -14400],
	"America/Jamaica" => ["jm", -18000, -18000],
	"America/Juneau" => ["us", -32400, -28800],
	"America/Kentucky/Louisville" => ["us", -18000, -14400],
	"America/Kentucky/Monticello" => ["us", -18000, -14400],
	"America/Kralendijk" => ["bq", -14400, -14400],
	"America/La_Paz" => ["bo", -14400, -14400],
	"America/Lima" => ["pe", -18000, -18000],
	"America/Los_Angeles" => ["us", -28800, -25200],
	"America/Lower_Princes" => ["sx", -14400, -14400],
	"America/Maceio" => ["br", -10800, -10800],
	"America/Managua" => ["ni", -21600, -21600],
	"America/Manaus" => ["br", -14400, -14400],
	"America/Marigot" => ["mf", -14400, -14400],
	"America/Martinique" => ["mq", -14400, -14400],
	"America/Matamoros" => ["mx", -21600, -18000],
	"America/Mazatlan" => ["mx", -25200, -21600],
	"America/Menominee" => ["us", -21600, -18000],
	"America/Merida" => ["mx", -21600, -18000],
	"America/Metlakatla" => ["us", -28800, -28800],
	"America/Mexico_City" => ["mx", -21600, -18000],
	"America/Miquelon" => ["pm", -10800, -7200],
	"America/Moncton" => ["ca", -14400, -10800],
	"America/Monterrey" => ["mx", -21600, -18000],
	"America/Montevideo" => ["uy", -10800, -7200],
	"America/Montreal" => ["ca", -18000, -14400],
	"America/Montserrat" => ["ms", -14400, -14400],
	"America/Nassau" => ["bs", -18000, -14400],
	"America/New_York" => ["us", -18000, -14400],
	"America/Nipigon" => ["ca", -18000, -14400],
	"America/Nome" => ["us", -32400, -28800],
	"America/Noronha" => ["br", -7200, -7200],
	"America/North_Dakota/Beulah" => ["us", -21600, -18000],
	"America/North_Dakota/Center" => ["us", -21600, -18000],
	"America/North_Dakota/New_Salem" => ["us", -21600, -18000],
	"America/Ojinaga" => ["mx", -25200, -21600],
	"America/Panama" => ["pa", -18000, -18000],
	"America/Pangnirtung" => ["ca", -18000, -14400],
	"America/Paramaribo" => ["sr", -10800, -10800],
	"America/Phoenix" => ["us", -25200, -25200],
	"America/Port-au-Prince" => ["ht", -18000, -14400],
	"America/Port_of_Spain" => ["tt", -14400, -14400],
	"America/Porto_Velho" => ["br", -14400, -14400],
	"America/Puerto_Rico" => ["pr", -14400, -14400],
	"America/Rainy_River" => ["ca", -21600, -18000],
	"America/Rankin_Inlet" => ["ca", -21600, -18000],
	"America/Recife" => ["br", -10800, -10800],
	"America/Regina" => ["ca", -21600, -21600],
	"America/Resolute" => ["ca", -21600, -18000],
	"America/Rio_Branco" => ["br", -14400, -14400],
	"America/Santa_Isabel" => ["mx", -28800, -25200],
	"America/Santarem" => ["br", -10800, -10800],
	"America/Santiago" => ["cl", -14400, -10800],
	"America/Santo_Domingo" => ["do", -14400, -14400],
	"America/Sao_Paulo" => ["br", -10800, -7200],
	"America/Scoresbysund" => ["gl", -3600, 0],
	"America/Sitka" => ["us", -32400, -28800],
	"America/St_Barthelemy" => ["bl", -14400, -14400],
	"America/St_Johns" => ["ca", -12600, -9000],
	"America/St_Kitts" => ["kn", -14400, -14400],
	"America/St_Lucia" => ["lc", -14400, -14400],
	"America/St_Thomas" => ["vi", -14400, -14400],
	"America/St_Vincent" => ["vc", -14400, -14400],
	"America/Swift_Current" => ["ca", -21600, -21600],
	"America/Tegucigalpa" => ["hn", -21600, -21600],
	"America/Thule" => ["gl", -14400, -10800],
	"America/Thunder_Bay" => ["ca", -18000, -14400],
	"America/Tijuana" => ["mx", -28800, -25200],
	"America/Toronto" => ["ca", -18000, -14400],
	"America/Tortola" => ["vg", -14400, -14400],
	"America/Vancouver" => ["ca", -28800, -25200],
	"America/Whitehorse" => ["ca", -28800, -25200],
	"America/Winnipeg" => ["ca", -21600, -18000],
	"America/Yakutat" => ["us", -32400, -28800],
	"America/Yellowknife" => ["ca", -25200, -21600],
	"Antarctica/Casey" => ["aq", 39600, 28800],
	"Antarctica/Davis" => ["aq", 18000, 25200],
	"Antarctica/DumontDUrville" => ["aq", 36000, 36000],
	"Antarctica/Macquarie" => ["aq", 39600, 39600],
	"Antarctica/Mawson" => ["aq", 18000, 18000],
	"Antarctica/McMurdo" => ["aq", 43200, 46800],
	"Antarctica/Palmer" => ["aq", -14400, -10800],
	"Antarctica/Rothera" => ["aq", -10800, -10800],
	"Antarctica/Syowa" => ["aq", 10800, 10800],
	"Antarctica/Vostok" => ["aq", 21600, 21600],
	"Arctic/Longyearbyen" => ["sj", 3600, 7200],
	"Asia/Aden" => ["ye", 10800, 10800],
	"Asia/Almaty" => ["kz", 21600, 21600],
	"Asia/Amman" => ["jo", 7200, 10800],
	"Asia/Anadyr" => ["ru", 43200, 43200],
	"Asia/Aqtau" => ["kz", 18000, 18000],
	"Asia/Aqtobe" => ["kz", 18000, 18000],
	"Asia/Ashgabat" => ["tm", 18000, 18000],
	"Asia/Baghdad" => ["iq", 10800, 10800],
	"Asia/Bahrain" => ["bh", 10800, 10800],
	"Asia/Baku" => ["az", 14400, 18000],
	"Asia/Bangkok" => ["th", 25200, 25200],
	"Asia/Beirut" => ["lb", 7200, 10800],
	"Asia/Bishkek" => ["kg", 21600, 21600],
	"Asia/Brunei" => ["bn", 28800, 28800],
	"Asia/Choibalsan" => ["mn", 28800, 28800],
	"Asia/Chongqing" => ["cn", 28800, 28800],
	"Asia/Colombo" => ["lk", 19800, 19800],
	"Asia/Damascus" => ["sy", 7200, 10800],
	"Asia/Dhaka" => ["bd", 21600, 21600],
	"Asia/Dili" => ["tl", 32400, 32400],
	"Asia/Dubai" => ["ae", 14400, 14400],
	"Asia/Dushanbe" => ["tj", 18000, 18000],
	"Asia/Gaza" => ["ps", 7200, 10800],
	"Asia/Harbin" => ["cn", 28800, 28800],
	"Asia/Hebron" => ["ps", 7200, 10800],
	"Asia/Ho_Chi_Minh" => ["vn", 25200, 25200],
	"Asia/Hong_Kong" => ["hk", 28800, 28800],
	"Asia/Hovd" => ["mn", 25200, 25200],
	"Asia/Irkutsk" => ["ru", 32400, 32400],
	"Asia/Jakarta" => ["id", 25200, 25200],
	"Asia/Jayapura" => ["id", 32400, 32400],
	"Asia/Jerusalem" => ["il", 7200, 10800],
	"Asia/Kabul" => ["af", 16200, 16200],
	"Asia/Kamchatka" => ["ru", 43200, 43200],
	"Asia/Karachi" => ["pk", 18000, 18000],
	"Asia/Kashgar" => ["cn", 28800, 28800],
	"Asia/Kathmandu" => ["np", 20700, 20700],
	"Asia/Kolkata" => ["in", 19800, 19800],
	"Asia/Krasnoyarsk" => ["ru", 28800, 28800],
	"Asia/Kuala_Lumpur" => ["my", 28800, 28800],
	"Asia/Kuching" => ["my", 28800, 28800],
	"Asia/Kuwait" => ["kw", 10800, 10800],
	"Asia/Macau" => ["mo", 28800, 28800],
	"Asia/Magadan" => ["ru", 43200, 43200],
	"Asia/Makassar" => ["id", 28800, 28800],
	"Asia/Manila" => ["ph", 28800, 28800],
	"Asia/Muscat" => ["om", 14400, 14400],
	"Asia/Nicosia" => ["cy", 7200, 10800],
	"Asia/Novokuznetsk" => ["ru", 25200, 25200],
	"Asia/Novosibirsk" => ["ru", 25200, 25200],
	"Asia/Omsk" => ["ru", 25200, 25200],
	"Asia/Oral" => ["kz", 18000, 18000],
	"Asia/Phnom_Penh" => ["kh", 25200, 25200],
	"Asia/Pontianak" => ["id", 25200, 25200],
	"Asia/Pyongyang" => ["kp", 32400, 32400],
	"Asia/Qatar" => ["qa", 10800, 10800],
	"Asia/Qyzylorda" => ["kz", 21600, 21600],
	"Asia/Rangoon" => ["mm", 23400, 23400],
	"Asia/Riyadh" => ["sa", 10800, 10800],
	"Asia/Sakhalin" => ["ru", 39600, 39600],
	"Asia/Samarkand" => ["uz", 18000, 18000],
	"Asia/Seoul" => ["kr", 32400, 32400],
	"Asia/Shanghai" => ["cn", 28800, 28800],
	"Asia/Singapore" => ["sg", 28800, 28800],
	"Asia/Taipei" => ["tw", 28800, 28800],
	"Asia/Tashkent" => ["uz", 18000, 18000],
	"Asia/Tbilisi" => ["ge", 14400, 14400],
	"Asia/Tehran" => ["ir", 12600, 16200],
	"Asia/Thimphu" => ["bt", 21600, 21600],
	"Asia/Tokyo" => ["jp", 32400, 32400],
	"Asia/Ulaanbaatar" => ["mn", 28800, 28800],
	"Asia/Urumqi" => ["cn", 28800, 28800],
	"Asia/Vientiane" => ["la", 25200, 25200],
	"Asia/Vladivostok" => ["ru", 39600, 39600],
	"Asia/Yakutsk" => ["ru", 36000, 36000],
	"Asia/Yekaterinburg" => ["ru", 21600, 21600],
	"Asia/Yerevan" => ["am", 14400, 14400],
	"Atlantic/Azores" => ["pt", -3600, 0],
	"Atlantic/Bermuda" => ["bm", -14400, -10800],
	"Atlantic/Canary" => ["es", 0, 3600],
	"Atlantic/Cape_Verde" => ["cv", -3600, -3600],
	"Atlantic/Faroe" => ["fo", 0, 3600],
	"Atlantic/Madeira" => ["pt", 0, 3600],
	"Atlantic/Reykjavik" => ["is", 0, 0],
	"Atlantic/South_Georgia" => ["gs", -7200, -7200],
	"Atlantic/St_Helena" => ["sh", 0, 0],
	"Atlantic/Stanley" => ["fk", -10800, -10800],
	"Australia/Adelaide" => ["au", 34200, 37800],
	"Australia/Brisbane" => ["au", 36000, 36000],
	"Australia/Broken_Hill" => ["au", 34200, 37800],
	"Australia/Currie" => ["au", 36000, 39600],
	"Australia/Darwin" => ["au", 34200, 34200],
	"Australia/Eucla" => ["au", 31500, 31500],
	"Australia/Hobart" => ["au", 36000, 39600],
	"Australia/Lindeman" => ["au", 36000, 36000],
	"Australia/Lord_Howe" => ["au", 37800, 39600],
	"Australia/Melbourne" => ["au", 36000, 39600],
	"Australia/Perth" => ["au", 28800, 28800],
	"Australia/Sydney" => ["au", 36000, 39600],
	"Europe/Amsterdam" => ["nl", 3600, 7200],
	"Europe/Andorra" => ["ad", 3600, 7200],
	"Europe/Athens" => ["gr", 7200, 10800],
	"Europe/Belgrade" => ["rs", 3600, 7200],
	"Europe/Berlin" => ["de", 3600, 7200],
	"Europe/Bratislava" => ["sk", 3600, 7200],
	"Europe/Brussels" => ["be", 3600, 7200],
	"Europe/Bucharest" => ["ro", 7200, 10800],
	"Europe/Budapest" => ["hu", 3600, 7200],
	"Europe/Chisinau" => ["md", 7200, 10800],
	"Europe/Copenhagen" => ["dk", 3600, 7200],
	"Europe/Dublin" => ["ie", 0, 3600],
	"Europe/Gibraltar" => ["gi", 3600, 7200],
	"Europe/Guernsey" => ["gg", 0, 3600],
	"Europe/Helsinki" => ["fi", 7200, 10800],
	"Europe/Isle_of_Man" => ["im", 0, 3600],
	"Europe/Istanbul" => ["tr", 7200, 10800],
	"Europe/Jersey" => ["je", 0, 3600],
	"Europe/Kaliningrad" => ["ru", 10800, 10800],
	"Europe/Kiev" => ["ua", 7200, 10800],
	"Europe/Lisbon" => ["pt", 0, 3600],
	"Europe/Ljubljana" => ["si", 3600, 7200],
	"Europe/London" => ["uk", 0, 3600],
	"Europe/Luxembourg" => ["lu", 3600, 7200],
	"Europe/Madrid" => ["es", 3600, 7200],
	"Europe/Malta" => ["mt", 3600, 7200],
	"Europe/Mariehamn" => ["ax", 7200, 10800],
	"Europe/Minsk" => ["by", 10800, 10800],
	"Europe/Monaco" => ["mc", 3600, 7200],
	"Europe/Moscow" => ["ru", 14400, 14400],
	"Europe/Oslo" => ["no", 3600, 7200],
	"Europe/Paris" => ["fr", 3600, 7200],
	"Europe/Podgorica" => ["me", 3600, 7200],
	"Europe/Prague" => ["cz", 3600, 7200],
	"Europe/Riga" => ["lv", 7200, 10800],
	"Europe/Rome" => ["it", 3600, 7200],
	"Europe/Samara" => ["ru", 14400, 14400],
	"Europe/San_Marino" => ["sm", 3600, 7200],
	"Europe/Sarajevo" => ["ba", 3600, 7200],
	"Europe/Simferopol" => ["ua", 7200, 10800],
	"Europe/Skopje" => ["mk", 3600, 7200],
	"Europe/Sofia" => ["bg", 7200, 10800],
	"Europe/Stockholm" => ["se", 3600, 7200],
	"Europe/Tallinn" => ["ee", 7200, 10800],
	"Europe/Tirane" => ["al", 3600, 7200],
	"Europe/Uzhgorod" => ["ua", 7200, 10800],
	"Europe/Vaduz" => ["li", 3600, 7200],
	"Europe/Vatican" => ["va", 3600, 7200],
	"Europe/Vienna" => ["at", 3600, 7200],
	"Europe/Vilnius" => ["lt", 7200, 10800],
	"Europe/Volgograd" => ["ru", 14400, 14400],
	"Europe/Warsaw" => ["pl", 3600, 7200],
	"Europe/Zagreb" => ["hr", 3600, 7200],
	"Europe/Zaporozhye" => ["ua", 7200, 10800],
	"Europe/Zurich" => ["ch", 3600, 7200],
	"Indian/Antananarivo" => ["mg", 10800, 10800],
	"Indian/Chagos" => ["io", 21600, 21600],
	"Indian/Christmas" => ["cx", 25200, 25200],
	"Indian/Cocos" => ["cc", 23400, 23400],
	"Indian/Comoro" => ["km", 10800, 10800],
	"Indian/Kerguelen" => ["tf", 18000, 18000],
	"Indian/Mahe" => ["sc", 14400, 14400],
	"Indian/Maldives" => ["mv", 18000, 18000],
	"Indian/Mauritius" => ["mu", 14400, 14400],
	"Indian/Mayotte" => ["yt", 10800, 10800],
	"Indian/Reunion" => ["re", 14400, 14400],
	"Pacific/Apia" => ["ws", 46800, 50400],
	"Pacific/Auckland" => ["nz", 43200, 46800],
	"Pacific/Chatham" => ["nz", 45900, 49500],
	"Pacific/Chuuk" => ["fm", 36000, 36000],
	"Pacific/Easter" => ["cl", -21600, -18000],
	"Pacific/Efate" => ["vu", 39600, 39600],
	"Pacific/Enderbury" => ["ki", 46800, 46800],
	"Pacific/Fakaofo" => ["tk", 50400, 50400],
	"Pacific/Fiji" => ["fj", 43200, 46800],
	"Pacific/Funafuti" => ["tv", 43200, 43200],
	"Pacific/Galapagos" => ["ec", -21600, -21600],
	"Pacific/Gambier" => ["pf", -32400, -32400],
	"Pacific/Guadalcanal" => ["sb", 39600, 39600],
	"Pacific/Guam" => ["gu", 36000, 36000],
	"Pacific/Honolulu" => ["us", -36000, -36000],
	"Pacific/Johnston" => ["um", -36000, -36000],
	"Pacific/Kiritimati" => ["ki", 50400, 50400],
	"Pacific/Kosrae" => ["fm", 39600, 39600],
	"Pacific/Kwajalein" => ["mh", 43200, 43200],
	"Pacific/Majuro" => ["mh", 43200, 43200],
	"Pacific/Marquesas" => ["pf", -34200, -34200],
	"Pacific/Midway" => ["um", -39600, -39600],
	"Pacific/Nauru" => ["nr", 43200, 43200],
	"Pacific/Niue" => ["nu", -39600, -39600],
	"Pacific/Norfolk" => ["nf", 41400, 41400],
	"Pacific/Noumea" => ["nc", 39600, 39600],
	"Pacific/Pago_Pago" => ["as", -39600, -39600],
	"Pacific/Palau" => ["pw", 32400, 32400],
	"Pacific/Pitcairn" => ["pn", -28800, -28800],
	"Pacific/Pohnpei" => ["fm", 39600, 39600],
	"Pacific/Port_Moresby" => ["pg", 36000, 36000],
	"Pacific/Rarotonga" => ["ck", -36000, -36000],
	"Pacific/Saipan" => ["mp", 36000, 36000],
	"Pacific/Tahiti" => ["pf", -36000, -36000],
	"Pacific/Tarawa" => ["ki", 43200, 43200],
	"Pacific/Tongatapu" => ["to", 46800, 46800],
	"Pacific/Wake" => ["um", 43200, 43200],
	"Pacific/Wallis" => ["wf", 43200, 43200]
];
