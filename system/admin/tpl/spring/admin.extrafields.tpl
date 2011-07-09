<!-- BEGIN: MAIN -->
<h2>{PHP.L.adm_extrafields}</h2>
		{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
<!-- BEGIN: TABLELIST -->	
<div class="block">
	<table class="cells">
		<!-- BEGIN: ROW -->	
		<tr>
			<td><a href="{ADMIN_EXTRAFIELDS_ROW_TABLEURL}">{ADMIN_EXTRAFIELDS_ROW_TABLENAME}</a></td>
		</tr>
		<!-- END: ROW -->
	</table>
	<a href="{ADMIN_EXTRAFIELDS_ALLTABLES}">{PHP.L.adm_extrafields_all}</a>
</div>
<!-- END: TABLELIST -->
<!-- BEGIN: TABLE -->	
<div class="block">
	<form action="{ADMIN_EXTRAFIELDS_URL_FORM_EDIT}" method="post">
		<table class="cells">
			<tr>
				<td class="coltop"></td>
				<td class="coltop">{PHP.L.extf_Name}</td>
				<td class="coltop">{PHP.L.extf_Type}</td>
				<td class="coltop">{PHP.L.adm_extrafield_params}</td>
				<td class="coltop"></td>
			</tr>
			<!-- BEGIN: EXTRAFIELDS_ROW -->	
			<tr id="ex{ADMIN_EXTRAFIELDS_ROW_ID}">
				<td>
					{ADMIN_EXTRAFIELDS_ROW_ENABLED}
				</td>
				<td class="{ADMIN_EXTRAFIELDS_ODDEVEN}">
						{ADMIN_EXTRAFIELDS_ROW_NAME}
					<p class="small">{PHP.L.extf_Description}</p>
						{ADMIN_EXTRAFIELDS_ROW_DESCRIPTION}
					<p class="small">{PHP.L.extf_Base_HTML}</p>
						{ADMIN_EXTRAFIELDS_ROW_HTML}
				</td>
				<td class="{ADMIN_EXTRAFIELDS_ODDEVEN}">
						{ADMIN_EXTRAFIELDS_ROW_SELECT}
					<p class="small">{PHP.L.adm_extrafield_parse}</p>
						{ADMIN_EXTRAFIELDS_ROW_PARSE}
					<p class="small">{ADMIN_EXTRAFIELDS_ROW_REQUIRED}{PHP.L.adm_extrafield_required}</p>

				</td>
				<td class="{ADMIN_EXTRAFIELDS_ODDEVEN}">
					{ADMIN_EXTRAFIELDS_ROW_PARAMS}
					<p class="small">{PHP.L.adm_extrafield_selectable_values}</p>
						{ADMIN_EXTRAFIELDS_ROW_VARIANTS}						
					<p class="small">{PHP.L.adm_extrafield_default}</p>
						{ADMIN_EXTRAFIELDS_ROW_DEFAULT}
				</td>
				<td class="centerall {ADMIN_EXTRAFIELDS_ODDEVEN}">

					<a title="{PHP.L.Delete}" href="{ADMIN_EXTRAFIELDS_ROW_DEL_URL}" class="ajax">{PHP.R.admin_icon_delete}</a>
				</td>
			</tr>
			<!-- END: EXTRAFIELDS_ROW -->
			<tr>
				<td class="valid" colspan="4">
					<input type="submit" value="{PHP.L.Update}" onclick="location.href='{ADMIN_EXTRAFIELDS_ROW_FORM_URL}'" />
				</td>
			</tr>
		</table>
	</form>
	<p class="paging">{ADMIN_EXTRAFIELDS_PAGINATION_PREV}{ADMIN_EXTRAFIELDS_PAGNAV}{ADMIN_EXTRAFIELDS_PAGINATION_NEXT} <span>{PHP.L.Total}: {ADMIN_EXTRAFIELDS_TOTALITEMS}, {PHP.L.Onpage}: {ADMIN_EXTRAFIELDS_COUNTER_ROW}</span></p>
</div>

<div class="block">
	<h3>{PHP.L.adm_extrafield_new}:</h3>
	<form action="{ADMIN_EXTRAFIELDS_URL_FORM_ADD}" method="post">
		<table class="cells">
			<tr>
				<td class="coltop width30">{PHP.L.extf_Name}</td>
				<td class="coltop width20">{PHP.L.extf_Type}</td>
				<td class="coltop width40">{PHP.L.adm_extrafield_params}</td>
			</tr>
			<tr id="exnew">
				<td>
					{ADMIN_EXTRAFIELDS_NAME}
					<p class="small">{PHP.L.extf_Description}</p>
							{ADMIN_EXTRAFIELDS_DESCRIPTION}
					<p class="small">{PHP.L.extf_Base_HTML}</p>
						{ADMIN_EXTRAFIELDS_HTML}	
				</td>
				<td>
							{ADMIN_EXTRAFIELDS_SELECT}
					<p class="small">{PHP.L.adm_extrafield_parse}</p>
						{ADMIN_EXTRAFIELDS_PARSE}
					<p class="small">{ADMIN_EXTRAFIELDS_REQUIRED}{PHP.L.adm_extrafield_required}</p>
				</td>
				<td>
							{ADMIN_EXTRAFIELDS_PARAMS}
					<p class="small">{PHP.L.adm_extrafield_selectable_values}</p>
							{ADMIN_EXTRAFIELDS_VARIANTS}					
					<p class="small">{PHP.L.adm_extrafield_default}</p>
						{ADMIN_EXTRAFIELDS_DEFAULT}
				</td>
			</tr>
			<tr>
				<td class="valid" colspan="3">
					<p class="small"><input type="checkbox" name="field_noalter" /> {PHP.L.adm_extrafield_noalter}</p>
					<input type="submit" value="{PHP.L.Add}" />
				</td>
			</tr>
		</table>
	</form>
</div>
<!-- END: TABLE -->
<!-- END: MAIN -->