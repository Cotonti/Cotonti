<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

<!-- BEGIN: TABLELIST -->
<div class="block">
	<h2>{PHP.L.adm_extrafields}</h2>
	<div class="wrapper">
		<table class="cells">
			<!-- BEGIN: ROW -->
			<tr>
				<td>
					<a href="{ADMIN_EXTRAFIELDS_ROW_TABLEURL}">{ADMIN_EXTRAFIELDS_ROW_TABLENAME}</a>
				</td>
			</tr>
			<!-- END: ROW -->
		</table>
		<a href="{ADMIN_EXTRAFIELDS_ALLTABLES}">{PHP.L.adm_extrafields_all}</a>
	</div>
</div>
<!-- END: TABLELIST -->

<!-- BEGIN: TABLE -->
<div class="block">
	<form action="{ADMIN_EXTRAFIELDS_URL_FORM_EDIT}" method="post">
		<div class="wrapper">
			<table class="cells">
				<thead>
					<tr>
						<th></th>
						<th class="w-40">{PHP.L.extf_Name}</th>
						<th>{PHP.L.extf_Type}</th>
						<th class="w-40">{PHP.L.adm_extrafield_params}</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<!-- BEGIN: EXTRAFIELDS_ROW -->
					<tr id="ex{ADMIN_EXTRAFIELDS_ROW_ID}">
						<td class="{ADMIN_EXTRAFIELDS_ROW_ODDEVEN}">
							{ADMIN_EXTRAFIELDS_ROW_ENABLED}
						</td>
						<td class="{ADMIN_EXTRAFIELDS_ROW_ODDEVEN}">
							{ADMIN_EXTRAFIELDS_ROW_NAME}
							<label class="small margintop10">{PHP.L.extf_Description}</label>
							{ADMIN_EXTRAFIELDS_ROW_DESCRIPTION}
							<label class="small margintop10">{PHP.L.extf_Base_HTML}</label>
							{ADMIN_EXTRAFIELDS_ROW_HTML}
						</td>
						<td class="{ADMIN_EXTRAFIELDS_ROW_ODDEVEN}">
							{ADMIN_EXTRAFIELDS_ROW_SELECT}
							<p class="small">{PHP.L.adm_extrafield_parse}</p>
							{ADMIN_EXTRAFIELDS_ROW_PARSE}
							<p class="small">{ADMIN_EXTRAFIELDS_ROW_REQUIRED}</p>
						</td>
						<td class="{ADMIN_EXTRAFIELDS_ROW_ODDEVEN}">
							{ADMIN_EXTRAFIELDS_ROW_PARAMS}
							<label class="small margintop10">{PHP.L.adm_extrafield_selectable_values}</label>
							{ADMIN_EXTRAFIELDS_ROW_VARIANTS}
							<label class="small margintop10">{PHP.L.adm_extrafield_default}</label>
							{ADMIN_EXTRAFIELDS_ROW_DEFAULT}
						</td>
						<td class="centerall {ADMIN_EXTRAFIELDS_ROW_ODDEVEN}">
							<a href="{ADMIN_EXTRAFIELDS_ROW_DELETE_CONFIRM_URL}" class="confirmLink button">{PHP.L.Delete}</a>
						</td>
					</tr>
					<!-- END: EXTRAFIELDS_ROW -->
					<!-- IF {ADMIN_EXTRAFIELDS_ON_PAGE_ITEMS} > 0 -->
					<tr>
						<td class="valid" colspan="5">
							<input type="submit" value="{PHP.L.Update}" onclick="location.href='{ADMIN_EXTRAFIELDS_ROW_FORM_URL}'" class="confirm" />
						</td>
					</tr>
					<!-- ENDIF -->
				</tbody>
			</table>
		</div>
	</form>
	<p class="paging">
		{PHP.L.Total}: {ADMIN_EXTRAFIELDS_TOTALITEMS}, {PHP.L.Onpage}: {ADMIN_EXTRAFIELDS_ON_PAGE_ITEMS}
	</p>
	<!-- IF {ADMIN_EXTRAFIELDS_PAGNAV} -->
	<nav class="pagination">
		<ul>
			{ADMIN_EXTRAFIELDS_PAGINATION_PREV}{ADMIN_EXTRAFIELDS_PAGNAV}{ADMIN_EXTRAFIELDS_PAGINATION_NEXT}
		</ul>
	</nav>
	<!-- ENDIF -->
</div>

<div class="block">
	<h2>{PHP.L.adm_extrafield_new}:</h2>
	<form action="{ADMIN_EXTRAFIELDS_URL_FORM_ADD}" method="post">
		<table class="cells info">
			<thead>
				<tr>
					<th class="w-45">{PHP.L.extf_Name}</th>
					<th>{PHP.L.extf_Type}</th>
					<th class="w-45">{PHP.L.adm_extrafield_params}</th>
				</tr>
			</thead>
			<tbody>
				<tr id="exnew">
					<td>
						{ADMIN_EXTRAFIELDS_NAME}
						<label class="small margintop10">{PHP.L.extf_Description}</label>
						{ADMIN_EXTRAFIELDS_DESCRIPTION}
						<label class="small margintop10">{PHP.L.extf_Base_HTML}</label>
						{ADMIN_EXTRAFIELDS_HTML}
					</td>
					<td>
						{ADMIN_EXTRAFIELDS_SELECT}
						<p class="small">{PHP.L.adm_extrafield_parse}</p>
						{ADMIN_EXTRAFIELDS_PARSE}
						<p class="small">{ADMIN_EXTRAFIELDS_REQUIRED}</p>
					</td>
					<td>
						{ADMIN_EXTRAFIELDS_PARAMS}
						<label class="small margintop10">{PHP.L.adm_extrafield_selectable_values}</label>
						{ADMIN_EXTRAFIELDS_VARIANTS}
						<label class="small margintop10">{PHP.L.adm_extrafield_default}</label>
						{ADMIN_EXTRAFIELDS_DEFAULT}
					</td>
				</tr>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="3">
					<input type="checkbox" name="field_noalter" /> {PHP.L.adm_extrafield_noalter}
					<input type="submit" class="confirm" value="{PHP.L.Add}" />
				</td>
			</tr>
			</tfoot>
		</table>
	</form>
</div>
<!-- END: TABLE -->

<script type="text/javascript">
//<![CDATA[
var exFLDHELPERS = "{ADMIN_EXTRAFIELDS_TAGS}";
var exnovars = "{PHP.L.adm_extrafields_help_notused}";
var exvariants = "{PHP.L.adm_extrafields_help_variants}";
var exrange = "{PHP.L.adm_extrafields_help_range}";
var exdata = "{PHP.L.adm_extrafields_help_data}";
var exregex = "{PHP.L.adm_extrafields_help_regex}";
var exfile = "{PHP.L.adm_extrafields_help_file}";
var exseparator = "{PHP.L.adm_extrafields_help_separator}";

$(document).ready(function(){
	$('body').on("change", '.exfldtype', function(){
		var exParent = $(this).closest('tr');
		var exvalid =  $(this).attr('value');
		if(exvalid == 'select' || exvalid == 'radio' || exvalid == 'checklistbox' || exvalid == 'file')
		{
			if (exvalid == 'file') {
				$(exParent).find('.exfldvariants').attr('title', 'jpg, png, pdf, zip,..');
			} else {
				$(exParent).find('.exfldvariants').attr('title',exvariants);
			}
			$(exParent).find('.exfldvariants').removeAttr("disabled");
		}
		else
		{
			$(exParent).find('.exfldvariants').attr('title', exnovars);
			$(exParent).find('.exfldvariants').attr('disabled', 'disabled');

		}
		switch(exvalid)
		{
			case 'input':
			$(exParent).find('.exfldparams').attr('title',exregex);
			$(exParent).find('.exfldparams').removeAttr("disabled");
			break;
			case 'inputint':
			$(exParent).find('.exfldparams').attr('title',exrange);
			$(exParent).find('.exfldparams').removeAttr("disabled");
			break;
			case 'currency':
			$(exParent).find('.exfldparams').attr('title',exrange);
			$(exParent).find('.exfldparams').removeAttr("disabled");
			break;
			case 'double':
			$(exParent).find('.exfldparams').attr('title',exrange);
			$(exParent).find('.exfldparams').removeAttr("disabled");
			break;
			case 'textarea':
			$(exParent).find('.exfldparams').attr('title',exnovars);
			$(exParent).find('.exfldparams').attr('disabled', 'disabled');
			break;
			case 'select':
			$(exParent).find('.exfldparams').attr('title',exnovars);
			$(exParent).find('.exfldparams').attr('disabled', 'disabled');
			break;
			case 'checkbox':
			$(exParent).find('.exfldparams').attr('title',exnovars);
			$(exParent).find('.exfldparams').attr('disabled', 'disabled');
			break;
			case 'radio':
			$(exParent).find('.exfldparams').attr('title',exnovars);
			$(exParent).find('.exfldparams').attr('disabled', 'disabled');
			break;
			case 'datetime':
			$(exParent).find('.exfldparams').attr('title',exdata);
			$(exParent).find('.exfldparams').removeAttr("disabled");
			break;
			case 'file':
			$(exParent).find('.exfldparams').attr('title',exfile);
			$(exParent).find('.exfldparams').removeAttr("disabled");
			break;
			case 'country':
			$(exParent).find('.exfldparams').attr('title',exnovars);
			$(exParent).find('.exfldparams').attr('disabled', 'disabled');
			break;
			case 'range':
			$(exParent).find('.exfldparams').attr('title',exrange);
			$(exParent).find('.exfldparams').removeAttr("disabled");
			break;
			case 'checklistbox':
			$(exParent).find('.exfldparams').attr('title',exseparator);
			$(exParent).find('.exfldparams').removeAttr("disabled");
			break;
		}
		if($(exParent).find('.exfldname').attr('value') != '')
		{
			var exhelper = $(exParent).find('.exfldname').attr('value').toUpperCase();
			exhelper = exFLDHELPERS.replace(/XXXXX/g, exhelper);
			$(exParent).find('.exfldname').attr('title',exhelper);
			$(exParent).find('.exflddesc').attr('title',exhelper);
		}
		else
		{
			$(exParent).find('.exfldname').removeAttr("title");
			$(exParent).find('.exflddesc').removeAttr("title");
		}
	});
	$(".exfldtype").change();
});

;
//]]>
</script>
<!-- END: MAIN -->
