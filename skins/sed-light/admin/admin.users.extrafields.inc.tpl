<!-- BEGIN: USER_EXTRAFIELDS -->
	<div id="ajaxBlock">
		<h2>{PHP.L.Users}</h2>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">
				<h4>{PHP.L.Message}</h4>
				<p>{ADMIN_USER_EXTRAFIELDS_ADMINWARNINGS}</p>
			</div>
<!-- ENDIF -->
		<table class="cells">
			<tr>
				<td class="coltop" style="width:20%;">{PHP.L.extf_Name}</td>
				<td class="coltop" style="width:25%;">{PHP.L.extf_Type}</td>
				<td class="coltop" style="width:45%;">{PHP.L.extf_Base_HTML}</td>
				<td class="coltop" style="width:10%;">{PHP.L.Action}</td>
			</tr>
<!-- BEGIN: USER_EXTRAFIELDS_ROW -->
			<form action="{ADMIN_USER_EXTRAFIELDS_ROW_FORM_URL}" method="post">
			<tr>
				<td>
					<input type="text" name="field_name" value="{ADMIN_USER_EXTRAFIELDS_ROW_NAME}" />
					<p class="small">{PHP.L.extf_Description}</p>
					<textarea name="field_description" rows="1" cols="20">{ADMIN_USER_EXTRAFIELDS_ROW_DESCRIPTION}</textarea>
				</td>
				<td>
					<select name="field_type" >
<!-- BEGIN: USER_EXTRAFIELDS_ROW_SELECT -->
						<option{ADMIN_USER_EXTRAFIELDS_ROW_SELECT_SELECTED}>{ADMIN_USER_EXTRAFIELDS_ROW_SELECT_OPTION}</option>
<!-- END: USER_EXTRAFIELDS_ROW_SELECT -->
					</select>
					<!-- <div class="variants_{ADMIN_USER_EXTRAFIELDS_ROW_NAME}" {ADMIN_USER_EXTRAFIELDS_ROW_VARIANTS_STYLE}> -->
					<p class="small">{PHP.L.adm_extrafield_selectable_values}</p>
					<textarea name="field_variants" rows="1" cols="20">{ADMIN_USER_EXTRAFIELDS_ROW_VARIANTS}</textarea>
				</td>
				<td>
					<textarea name="field_html" rows="1" cols="60">{ADMIN_USER_EXTRAFIELDS_ROW_FIELD_HTML_ENCODED}</textarea>
				</td>
				<td>
					<input type="submit" value="{PHP.L.Update}" onclick="location.href='{ADMIN_USER_EXTRAFIELDS_ROW_FORM_URL}'" /><br />
					<input type="button" value="{PHP.L.Delete}" onclick="if(confirm('{PHP.L.adm_extrafield_confirmdel}')) location.href='{ADMIN_USER_EXTRAFIELDS_ROW_DEL_URL}'" />
				</td>
			</tr>
			<!--//<tr>
				<td colspan="4">page.tpl: {PAGE_{ADMIN_USER_EXTRAFIELDS_ROW_BIGNAME}}&nbsp;&nbsp;&nbsp; page.add.tpl: {PAGEADD_FORM_{ADMIN_USER_EXTRAFIELDS_ROW_BIGNAME}}&nbsp;&nbsp;&nbsp; page.edit.tpl: {PAGEEDIT_FORM_{ADMIN_USER_EXTRAFIELDS_ROW_BIGNAME}} &nbsp;&nbsp;&nbsp; list.tpl: {LIST_ROW_{ADMIN_USER_EXTRAFIELDS_ROW_BIGNAME}}, {LIST_TOP_{ADMIN_USER_EXTRAFIELDS_ROW_BIGNAME}}</td>
			</tr>//-->
			</form>
<!-- END: USER_EXTRAFIELDS_ROW -->
		</table>
		<p class="paging">{ADMIN_USER_EXTRAFIELDS_PAGINATION_PREV}{ADMIN_USER_EXTRAFIELDS_PAGNAV}{ADMIN_USER_EXTRAFIELDS_PAGINATION_NEXT}<span class="a1">{PHP.L.Total}: {ADMIN_USER_EXTRAFIELDS_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_USER_EXTRAFIELDS_COUNTER_ROW}</span></p>

		<h3>{PHP.L.adm_extrafield_new}:</h3>
		<form action="{ADMIN_USER_EXTRAFIELDS_URL_FORM_ADD}" method="post">
		<table class="cells">
			<tr>
				<td class="coltop" style="width:20%;">{PHP.L.extf_Name}</td>
				<td class="coltop" style="width:25%;">{PHP.L.extf_Type}</td>
				<td class="coltop" style="width:55%;">{PHP.L.extf_Base_HTML}</td>
			</tr>
			<tr>
				<td>
					<input type="text" name="field_name" value="" />
					<p class="small">{PHP.L.extf_Description}</p>
					<textarea name="field_description" rows="1" cols="20"></textarea>
				</td>
				<td>
					<select name="field_type">
<!-- BEGIN: USER_EXTRAFIELDS_FORM_ADD_SELECT_FIELD_TYPE -->
						<option{ADMIN_USER_EXTRAFIELDS_SELECT_FIELD_TYPE_OPTION_SELECTED}>{ADMIN_USER_EXTRAFIELDS_SELECT_FIELD_TYPE_OPTION}</option>
<!-- END: USER_EXTRAFIELDS_FORM_ADD_SELECT_FIELD_TYPE -->
					</select>
					<p class="small">{PHP.L.adm_extrafield_selectable_values}</p>
					<textarea name="field_variants" rows="1" cols="20"></textarea>
				</td>
				<td>
					<textarea name="field_html" rows="2" cols="40"></textarea>
					<p class="small"><input type="checkbox" name="field_noalter" /> {PHP.L.adm_extrafield_noalter}</p>
				</td>
			</tr>
			<tr>
				<td class="valid" colspan="3">
					<input type="submit" value="{PHP.L.Add}" />
				</td>
			</tr>
		</table>
		</form>
	</div>
<!-- END: USER_EXTRAFIELDS -->