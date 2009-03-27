<!-- BEGIN: PAG_CATORDER -->
		<div>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">{ADMIN_PAG_CATORDER_ADMINWARNINGS}</div>
<!-- ENDIF -->
			<form id="chgorder" action="{ADMIN_PAG_CATORDER_URL_FORM}" method="post">
				<table class="cells">
				<tr>
					<td class="coltop">{PHP.L.Code}</td>
					<td class="coltop">{PHP.L.Path}</td>
					<td class="coltop">{PHP.L.Title}</td>
					<td class="coltop">{PHP.L.Order}</td>
				</tr>
<!-- BEGIN: PAG_CATORDER_ROW -->
				<tr>
					<td>{ADMIN_PAG_CATORDER_ROW_CODE}</td>
					<td>{ADMIN_PAG_CATORDER_ROW_PATH}</td>
					<td>{ADMIN_PAG_CATORDER_ROW_TITLE}</td>
					<td>
						<select name="{ADMIN_PAG_CATORDER_ROW_FORM_SORT_NAME}" size="1">
<!-- BEGIN: PAG_CATORDER_ROW_SELECT_SORT -->
							<option value="{ADMIN_PAG_CATORDER_ROW_SELECT_SORT_VALUE}"{ADMIN_PAG_CATORDER_ROW_SELECT_SORT_SELECTED}>{ADMIN_PAG_CATORDER_ROW_SELECT_SORT_NAME}</option>
<!-- END: PAG_CATORDER_ROW_SELECT_SORT -->
						</select>
						<select name="{ADMIN_PAG_CATORDER_ROW_FORM_WAY_NAME}" size="1">
<!-- BEGIN: PAG_CATORDER_ROW_SELECT_WAY -->
							<option value="{ADMIN_PAG_CATORDER_ROW_SELECT_WAY_VALUE}"{ADMIN_PAG_CATORDER_ROW_SELECT_WAY_SELECTED}>{ADMIN_PAG_CATORDER_ROW_SELECT_WAY_NAME}</option>
<!-- END: PAG_CATORDER_ROW_SELECT_WAY -->
						</select>
					</td>
				</tr>
<!-- END: PAG_CATORDER_ROW -->
				<tr>
					<td colspan="4">
						<div class="pagnav">{ADMIN_PAG_CATORDER_PAGINATION_PREV} {ADMIN_PAG_CATORDER_PAGNAV} {ADMIN_PAG_CATORDER_PAGINATION_NEXT}</div>
					</td>
				</tr>
				<tr>
					<td colspan="4">{PHP.L.Total} : {ADMIN_PAG_CATORDER_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_PAG_CATORDER_COUNTER_ROW}</td>
				</tr>
				<tr>
					<td colspan="4"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
				</table>
			</form>
		</div>
<!-- END: PAG_CATORDER -->