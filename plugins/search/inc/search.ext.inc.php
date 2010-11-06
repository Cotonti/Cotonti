<?PHP
/**
 * Search include file
 *
 * @package Cotonti
 * @version 0.0.6
 * @author Boss
 * @copyright Copyright (c) 2008-2009 Cotonti Team
 * @license BSD License
 */

defined('SED_CODE') || die('Wrong URL.');

// Reading POST params
$within = sed_import('within','P','INT');
$from_day = sed_import('from_day','P','INT');
$from_month = sed_import('from_month','P','INT');
$from_year = sed_import('from_year','P','INT');
$to_day = sed_import('to_day','P','INT');
$to_month = sed_import('to_month','P','INT');
$to_year = sed_import('to_year','P','INT');

// If need to include the date, continue
if($within > 0)
{
	// Set the default date if missing
	if($from_day == 0 || !isset($from_day))
	{ $from_day = 1; $within = 777; }
	if($from_month == 0 || !isset($from_month))
	{ $from_month = 1; $within = 777; }
	if($from_year == 0 || !isset($from_year))
	{ $from_year = 1990; $within = 777; }

	if($to_day == 0 || !isset($to_day))
	{ $to_day = date('j'); $within = 777; }
	if($to_month == 0 || !isset($to_month))
	{ $to_month = date('m'); $within = 777; }
	if($to_year == 0 || !isset($to_year))
	{ $to_year = date('Y'); $within = 777; }

	// Remove extra zeroes
	settype($from_day, 'double');
	settype($from_month, 'double');
	settype($from_year, 'double');
	settype($to_day, 'double');
	settype($to_month, 'double');
	settype($to_year, 'double');
}


$html_code_java = <<<HTM
<script type="text/javascript">

function evaluateDates() {
	var form = document.forms.search;
	var now = new Date();
	var from = new Date();
	var date_to = now.getDate();
	var month_to = now.getMonth()+1;
	var year_to = now.getFullYear();
	var date_from = '';
	var month_from = '';
	var year_from = '';
	switch(form.within[form.within.selectedIndex].value) {
		// last 2 weeks
		case '1' :
		from.setTime(now.valueOf()-1000*60*60*24*14);
		date_from = from.getDate();
		month_from = from.getMonth()+1;
		year_from = from.getFullYear();
		break;
		// lats month
		case '2' :
		from.setTime(now.valueOf()-1000*60*60*24*30);
		date_from = from.getDate();
		month_from = from.getMonth()+1;
		year_from = from.getFullYear();
		break;
		// last 3 months
		case '3' :
		from.setTime(now.valueOf()-1000*60*60*24*90);
		date_from = from.getDate();
		month_from = from.getMonth()+1;
		year_from = from.getFullYear();
		break;
		// last year
		case '4' :
		date_from = from.getDate();
		month_from = from.getMonth()+1;
		year_from = from.getFullYear()-1;
		break;
		// custom range
		case '777' :
		break;
	}
	form.to_day.value = date_to;
	form.to_month.value = month_to;
	form.to_year.value = year_to;
	form.from_day.value = date_from;
	form.from_month.value = month_from;
	form.from_year.value = year_from;
}

function validate_day(day) {
	if(!is_empty(day)) {
		if(day.value < 1) { day.value = 1; }
		if(day.value > 31) { day.value = 31; }
	}
}

function validate_month(month) {
	if(!is_empty(month)) {
		if(month.value < 1) { month.value = 1; }
		if(month.value > 12) { month.value = 12; }
	}
}

function validate_year(year) {
	var now = new Date();
	if(!is_empty(year)) {
		if(year.value < 1990) { year.value = 1990; }
		if(year.value > now.getFullYear()) { year.value = now.getFullYear(); }
	}
}

function getdate() {
	var form = document.forms.search;
	var now = new Date();
	if(form.to_day.value=='') { form.to_day.value = now.getDate(); }
	if(form.to_month.value=='') { form.to_month.value = now.getMonth()+1; }
	if(form.to_year.value=='') { form.to_year.value = now.getFullYear(); }
}

function custom_range(field) {
	is_empty(field) ? true : document.forms.search.within[5].selected = true;
}

function is_empty( fld ) {
	myRe=/^\s+|\s+$/g;
	if(fld.value.replace(myRe,'')=='') { return true; }
	return false;
}

function numeralsOnly(evt) {
	evt = (evt) ? evt : event;
	var charCode = (evt.charCode) ? evt.charCode : ((evt.keyCode) ? evt.keyCode : ((evt.which) ? evt.which : 0));
	if(charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 37 && charCode !== 39 && charCode !== 46) { return false; }
	return true;
}

</script>
HTM;


$html_code_date = "
<div style='margin:0 0 10px;'>
	<select class='search' size='4' onchange='evaluateDates()' name='within'>
	<option value='0'".(($within==0 || !isset($within))?" selected":"").">".$L['plu_any_date']."</option>
	<option value='1'".(($within==1)?" selected":"").">".$L['plu_last_2_weeks']."</option>
	<option value='2'".(($within==2)?" selected":"").">".$L['plu_last_1_month']."</option>
	<option value='3'".(($within==3)?" selected":"").">".$L['plu_last_3_month']."</option>
	<option value='4'".(($within==4)?" selected":"").">".$L['plu_last_1_year']."</option>
	<option value='777'".(($within==777)?" selected":"").">".$L['plu_need_datas']."</option>
	</select>
</div>
<table class='srch'>
	<tr>
		<td>
			<input maxlength='2' size='2' type='text' name='from_day' value='".$from_day."' onkeypress='return numeralsOnly(event)' onChange='custom_range(this)' onBlur='validate_day(this)' />
			<div>".$L['plu_need_dd']."</div>
		</td>
		<td>
			<input maxlength='2' size='2' type='text' name='from_month' value='".$from_month."' onkeypress='return numeralsOnly(event)' onChange='custom_range(this)' onBlur='validate_month(this)' />
			<div>".$L['plu_need_mm']."</div>
		</td>
		<td>
			<input maxlength='4' size='4' type='text' name='from_year' value='".$from_year."' onkeypress='return numeralsOnly(event)' onChange='custom_range(this)' onBlur='validate_year(this)' />
			<div>".$L['plu_need_yy']."</div>
		</td>
		<td>
			<input maxlength='2' size='2' type='text' name='to_day' value='".$to_day."' onkeypress='return numeralsOnly(event)' onChange='custom_range(this)' onBlur='validate_day(this)' />
			<div>".$L['plu_need_dd']."</div>
		</td>
		<td>
			<input maxlength='2' size='2' type='text' name='to_month' value='".$to_month."' onkeypress='return numeralsOnly(event)' onChange='custom_range(this)' onBlur='validate_month(this)' />
			<div>".$L['plu_need_mm']."</div>
		</td>
		<td>
			<input maxlength='4' size='4' type='text' name='to_year' value='".$to_year."' onkeypress='return numeralsOnly(event)' onChange='custom_range(this)' onBlur='validate_year(this)' />
			<div>".$L['plu_need_yy']."</div>
		</td>
	</tr>
</table>
<script type=\"text/javascript\">getdate();</script>

";
?>