<!-- BEGIN: CONFIG -->
		<div id="{ADMIN_CONFIG_AJAX_OPENDIVID}">
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">{ADMIN_CONFIG_ADMINWARNINGS}</div>
<!-- ENDIF -->
<!-- BEGIN: EDIT -->
			<form name="saveconfig" id="saveconfig" action="{ADMIN_CONFIG_FORM_URL}" method="post"{ADMIN_CONFIG_FORM_URL_AJAX}>
				<table class="cells">
				<tr>
					<td class="coltop" colspan="2">{PHP.L.Configuration}</td>
					<td class="coltop">{PHP.L.Reset}</td>
				</tr>
<!-- BEGIN: ADMIN_CONFIG_ROW -->
				<tr>
					<td style="width:25%;">{ADMIN_CONFIG_ROW_CONFIG_TITLE} : </td>
					<td style="width:68%;">
<!-- BEGIN: ADMIN_CONFIG_ROW_TYPE_1 -->
						<input type="text" class="text" name="{ADMIN_CONFIG_ROW_CONFIG_NAME}" value="{ADMIN_CONFIG_ROW_CONFIG_VALUE}" size="32" maxlength="255" />
<!-- END: ADMIN_CONFIG_ROW_TYPE_1 -->
<!-- BEGIN: ADMIN_CONFIG_ROW_TYPE_2 -->
<!-- BEGIN: ADMIN_CONFIG_ROW_TYPE_2_SELECT -->
						<select name="{ADMIN_CONFIG_ROW_CONFIG_NAME}" size="1">
<!-- BEGIN: ADMIN_CONFIG_ROW_TYPE_2_OTP -->
							<option value="{ADMIN_CONFIG_ROW_CONFIG_OPTION_VALUE}"{ADMIN_CONFIG_ROW_CONFIG_OPTION_SELECTED}>{ADMIN_CONFIG_ROW_CONFIG_OPTION_VALUE}</option>
<!-- END: ADMIN_CONFIG_ROW_TYPE_2_OTP -->
						</select>
<!-- END: ADMIN_CONFIG_ROW_TYPE_2_SELECT -->
<!-- BEGIN: ADMIN_CONFIG_ROW_TYPE_2_SELECTBOXLEVELS -->
						{ADMIN_CONFIG_ROW_CONFIG_OPTION}
<!-- END: ADMIN_CONFIG_ROW_TYPE_2_SELECTBOXLEVELS -->
<!-- BEGIN: ADMIN_CONFIG_ROW_TYPE_2_TEXT -->
						<input type="text" class="text" name="{ADMIN_CONFIG_ROW_CONFIG_OPTION_NAME}" value="{ADMIN_CONFIG_ROW_CONFIG_OPTION_VALUE}" size="8" maxlength="11" />
<!-- END: ADMIN_CONFIG_ROW_TYPE_2_TEXT -->
<!-- END: ADMIN_CONFIG_ROW_TYPE_2 -->
<!-- BEGIN: ADMIN_CONFIG_ROW_TYPE_3 -->
<!-- IF {PHP.config_value} == 1 -->
						<input type="radio" class="radio" name="{ADMIN_CONFIG_ROW_CONFIG_NAME}" value="1" checked="checked" />{PHP.L.Yes}&nbsp;&nbsp;<input type="radio" class="radio" name="{ADMIN_CONFIG_ROW_CONFIG_NAME}" value="0" />{PHP.L.No}
<!-- ELSE -->
						<input type="radio" class="radio" name="{ADMIN_CONFIG_ROW_CONFIG_NAME}" value="1" />{PHP.L.Yes}&nbsp;&nbsp;<input type="radio" class="radio" name="{ADMIN_CONFIG_ROW_CONFIG_NAME}" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
<!-- END: ADMIN_CONFIG_ROW_TYPE_3 -->
<!-- BEGIN: ADMIN_CONFIG_ROW_TYPE_4 -->
						<select name="{ADMIN_CONFIG_ROW_CONFIG_NAME}" size="1">
<!-- BEGIN: ADMIN_CONFIG_ROW_TYPE_4_OTP -->
							<option value="{ADMIN_CONFIG_ROW_CONFIG_OPTION_VALUE}"{ADMIN_CONFIG_ROW_CONFIG_OPTION_SELECTED}>{ADMIN_CONFIG_ROW_CONFIG_OPTION_NAME}</option>
<!-- END: ADMIN_CONFIG_ROW_TYPE_4_OTP -->
						</select>
<!-- END: ADMIN_CONFIG_ROW_TYPE_4 -->
<!-- BEGIN: ADMIN_CONFIG_ROW_TYPE_5 -->
						<textarea name="{ADMIN_CONFIG_ROW_CONFIG_NAME}" rows="8" cols="56">{ADMIN_CONFIG_ROW_CONFIG_VALUE}</textarea>
<!-- END: ADMIN_CONFIG_ROW_TYPE_5 -->
<!-- IF {PHP.if_config_more)} -->
						<div class="adminconfigmore">{ADMIN_CONFIG_ROW_CONFIG_MORE}</div>
<!-- ENDIF -->
					</td>
					<td style="text-align:center; width:7%;">&nbsp;
<!-- IF {PHP.o} == 'core' -->
						[<a href="{ADMIN_CONFIG_ROW_CONFIG_MORE_URL}"{ADMIN_CONFIG_ROW_CONFIG_MORE_URL_AJAX}>R</a>]
<!-- ENDIF -->
					</td>
				</tr>
<!-- END: ADMIN_CONFIG_ROW -->
				<tr>
					<td colspan="3"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
				</table>
			</form>
<!-- END: EDIT -->
<!-- BEGIN: DEFAULT -->
			<h4>{PHP.L.Core} :</h4>
			<ul>
<!-- BEGIN: ADMIN_CONFIG_ROW_CORE -->
				<li><a href="{ADMIN_CONFIG_ROW_CORE_URL}"{ADMIN_CONFIG_ROW_CORE_URL_AJAX}>{ADMIN_CONFIG_ROW_CORE_NAME}</a></li>
<!-- END: ADMIN_CONFIG_ROW_CORE -->
			</ul>
			<h4>{PHP.L.Plugins} :</h4>
			<ul>
<!-- BEGIN: ADMIN_CONFIG_ROW_PLUG -->
				<li><a href="{ADMIN_CONFIG_ROW_PLUG_URL}"{ADMIN_CONFIG_ROW_PLUG_URL_AJAX}>{ADMIN_CONFIG_ROW_PLUG_NAME}</a></li>
<!-- END: ADMIN_CONFIG_ROW_PLUG -->
			</ul>
<!-- END: DEFAULT -->
		</div>
<!-- END: CONFIG -->