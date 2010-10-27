<!-- BEGIN: USER_EXTRAFIELDS -->
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">{ADMIN_USER_EXTRAFIELDS_ADMINWARNINGS}</div>
<!-- ENDIF -->
			<table class="cells">
			<tr>
				<td class="coltop" style="width:20%;">{PHP.L.extf_Name}</td>
				<td class="coltop" style="width:25%;">{PHP.L.extf_Type}</td>
				<td class="coltop" style="width:45%;">{PHP.L.extf_Base_HTML}</td>
				<td class="coltop" style="width:10%;">&nbsp;</td>
			</tr>
			</table>
<!-- BEGIN: USER_EXTRAFIELDS_ROW -->
			<form action="{ADMIN_USER_EXTRAFIELDS_ROW_FORM_URL}" method="post">
				<table class="cells">
				<tr>
					<td style="width:20%;">
						<input type="text" name="field_name" value="{ADMIN_USER_EXTRAFIELDS_ROW_NAME}" />
						<br />
						<span style="font-size: 80%;">{PHP.L.extf_Description}</span>
						<br />
						<textarea name="field_description" rows="1" cols="20">{ADMIN_USER_EXTRAFIELDS_ROW_DESCRIPTION}</textarea>
					</td>
					<td style="width:25%;">
						<select name="field_type" >
<!-- BEGIN: USER_EXTRAFIELDS_ROW_SELECT -->
							<option{ADMIN_USER_EXTRAFIELDS_ROW_SELECT_SELECTED}>{ADMIN_USER_EXTRAFIELDS_ROW_SELECT_OPTION}</option>
<!-- END: USER_EXTRAFIELDS_ROW_SELECT -->
						</select>
						<!-- <div class="variants_{ADMIN_USER_EXTRAFIELDS_ROW_NAME}" {ADMIN_USER_EXTRAFIELDS_ROW_VARIANTS_STYLE}> -->
						<br />
						<span style="font-size: 80%;">{PHP.L.adm_extrafield_selectable_values}</span>
						<br />
						<textarea name="field_variants" rows="1" cols="20">{ADMIN_USER_EXTRAFIELDS_ROW_VARIANTS}</textarea>
					</td>
					<td style="width:45%;">
						<textarea name="field_html" rows="1" cols="60">{ADMIN_USER_EXTRAFIELDS_ROW_FIELD_HTML_ENCODED}</textarea>
					</td>
					<td style="width:10%;">
						<input type="submit" value="{PHP.L.Update}" onclick="location.href='{ADMIN_USER_EXTRAFIELDS_ROW_FORM_URL}'" /><br />
						<input type="button" value="{PHP.L.Delete}" onclick="if(confirm('{PHP.L.adm_extrafield_confirmdel}')) location.href='{ADMIN_USER_EXTRAFIELDS_ROW_DEL_URL}'" />
					</td>
				</tr>
				<!--//<tr>
					<td colspan="4">page.tpl: {PAGE_{ADMIN_USER_EXTRAFIELDS_ROW_BIGNAME}}&nbsp;&nbsp;&nbsp; page.add.tpl: {PAGEADD_FORM_{ADMIN_USER_EXTRAFIELDS_ROW_BIGNAME}}&nbsp;&nbsp;&nbsp; page.edit.tpl: {PAGEEDIT_FORM_{ADMIN_USER_EXTRAFIELDS_ROW_BIGNAME}} &nbsp;&nbsp;&nbsp; list.tpl: {LIST_ROW_{ADMIN_USER_EXTRAFIELDS_ROW_BIGNAME}}, {LIST_TOP_{ADMIN_USER_EXTRAFIELDS_ROW_BIGNAME}}</td>
				</tr>//-->
				</table>
			</form>
<!-- END: USER_EXTRAFIELDS_ROW -->
			<table class="cells">
			<tr>
				<td>
					<div class="pagnav">{ADMIN_USER_EXTRAFIELDS_PAGINATION_PREV} {ADMIN_USER_EXTRAFIELDS_PAGNAV} {ADMIN_USER_EXTRAFIELDS_PAGINATION_NEXT}</div>
				</td>
			</tr>
			<tr>
				<td>{PHP.L.Total} : {ADMIN_USER_EXTRAFIELDS_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_USER_EXTRAFIELDS_COUNTER_ROW}</td>
			</tr>
			</table>
			<h4>{PHP.L.adm_extrafield_new} :</h4>
			<form action="{ADMIN_USER_EXTRAFIELDS_URL_FORM_ADD}" method="post">
				<table class="cells">
				<tr>
					<td class="coltop" style="width:20%;">{PHP.L.extf_Name}</td>
					<td class="coltop" style="width:25%;">{PHP.L.extf_Type}</td>
					<td class="coltop" style="width:45%;">{PHP.L.extf_Base_HTML}</td>
					<td class="coltop" style="width:10%;">&nbsp;</td>
				</tr>
				<tr>
					<td>
						<input type="text" name="field_name" value="" />
						<br />
						{PHP.L.extf_Description}
						<br />
						<textarea name="field_description" rows="1" cols="20"></textarea>
					</td>
					<td>
						<select name="field_type">
<!-- BEGIN: USER_EXTRAFIELDS_FORM_ADD_SELECT_FIELD_TYPE -->
							<option{ADMIN_USER_EXTRAFIELDS_SELECT_FIELD_TYPE_OPTION_SELECTED}>{ADMIN_USER_EXTRAFIELDS_SELECT_FIELD_TYPE_OPTION}</option>
<!-- END: USER_EXTRAFIELDS_FORM_ADD_SELECT_FIELD_TYPE -->
						</select>
						<br />
						<span style="font-size: 80%;">{PHP.L.adm_extrafield_selectable_values}</span>
						<textarea name="field_variants" rows="1" cols="20"></textarea>
					</td>
					<td>
						<textarea name="field_html" rows="2" cols="40"></textarea>
						<span style="font-size: 80%;"><input type="checkbox" name="field_noalter" /> {PHP.L.adm_extrafield_noalter}</span>
					</td>
					<td>
						<input type="submit" value="{PHP.L.Add}" />
					</td>
				</tr>
				</table>
			</form>
<!-- END: USER_EXTRAFIELDS -->