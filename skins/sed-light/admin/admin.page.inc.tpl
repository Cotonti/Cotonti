<!-- BEGIN: PAGE -->
	<script type="text/javascript">
		$(document).ready(function()
		{
			$('.moreinfo').hide();
			$(".mor_info_on_off").click(function()
			{
				var kk = $(this).attr('id');
				$('#'+kk).children('.moreinfo').slideToggle(100);
			});
		});
	</script>
	<div id="{ADMIN_PAGE_AJAX_OPENDIVID}">
		<h2>{PHP.L.Pages} ({ADMIN_PAGE_TOTALDBPAGES})</h2>
		<ul class="follow">
			<li>
				<a title="{PHP.L.Configuration}" href="{ADMIN_PAGE_URL_CONFIG}">{PHP.L.Configuration}: {PHP.R.admin_icon_config}</a>
			</li>
			<li>
<!-- IF {PHP.lincif_page} -->
				<a href="{ADMIN_PAGE_URL_ADD}">{PHP.L.addnewentry}</a>
<!-- ELSE -->
				{PHP.L.addnewentry}
<!-- ENDIF -->
			</li>
			<li>
<!-- IF {PHP.lincif_conf} -->
				<a href="{ADMIN_PAGE_URL_EXTRAFIELDS}">{PHP.L.adm_extrafields_desc}</a>
<!-- ELSE -->
				{PHP.L.adm_extrafields_desc}
<!-- ENDIF -->
			</li>
			<li>
				<a href="{ADMIN_PAGE_URL_LIST_ALL}">{PHP.L.adm_showall}</a>
			</li>
		</ul>
<!-- IF {PHP.is_adminwarnings} -->
		<div class="error">{ADMIN_PAGE_ADMINWARNINGS}</div>
<!-- ENDIF -->
		<h3>{PHP.L.adm_valqueue}:</h3>
		<form id="form_valqueue" name="form_valqueue" method="post" action="{ADMIN_PAGE_FORM_URL}">
			<table class="cells">
			<tr>
				<td class="coltop" style="width:5%;">
<!-- IF {PHP.cfg.jquery} -->
					<input name="allchek" class="checkbox" type="checkbox" value="" onclick="$('.checkbox').attr('checked', this.checked);" />
<!-- ENDIF -->
				</td>
				<td class="coltop" style="width:5%;">#</td>
				<td class="coltop" style="width:75%;">{PHP.L.Title}</td>
				<td class="coltop" style="width:15%;">{PHP.L.Action}</td>
			</tr>
<!-- BEGIN: PAGE_ROW -->
			<tr>
				<td class="centerall {ADMIN_PAGE_ODDEVEN}"><input name="s[{ADMIN_PAGE_ID}]" type="checkbox" class="checkbox" /></td>
				<td class="centerall {ADMIN_PAGE_ODDEVEN}"><a href="{ADMIN_PAGE_ID_URL}">{ADMIN_PAGE_ID}</a></td>
				<td class="{ADMIN_PAGE_ODDEVEN}">
					<a href="{ADMIN_PAGE_URL}">{ADMIN_PAGE_SHORTTITLE}</a>
					<div style='display:inline;' id="mor_{PHP.ii}" class='mor_info_on_off'>
						<span style='float:right;'>{PHP.R.admin_icon_versions}</span>
						<div class='moreinfo'>
							<hr /><div style='float:right;text-align:center;width:80px;'><b>{PHP.L.Category}:</b> <br /><a href="{ADMIN_PAGE_CAT_URL}">{ADMIN_PAGE_CATICON}<br />{ADMIN_PAGE_CAT_TITLE}</a></div>
								  <b>{PHP.L.Description}:</b> {ADMIN_PAGE_DESC}
							<hr /><b>{PHP.L.Text}:</b> {ADMIN_PAGE_TEXT}
							<hr /><b>{PHP.L.Tags}:</b> <!-- BEGIN: ADMIN_TAGS_ROW -->&nbsp;<a title="{ADMIN_TAGS_ROW_TAG}" href="{ADMIN_TAGS_ROW_URL}">{ADMIN_TAGS_ROW_TAG}</a>&nbsp;<!-- END: ADMIN_TAGS_ROW --><!-- BEGIN: ADMIN_NO_TAGS -->&nbsp;{PHP.L.tags_Tag_cloud_none}<!-- END: ADMIN_NO_TAGS -->
							<hr /><div style='float:right;text-align:center;vertical-align:middle;'><b>{PHP.L.File}:</b>&nbsp;<div style='float:right;'><!-- IF {ADMIN_PAGE_FILE_ICON} != '' --><a href='{ADMIN_PAGE_FILE_URL}' title='{PHP.fileex}'>{ADMIN_PAGE_FILE_ICON}</a><!-- ENDIF --><!-- IF {ADMIN_PAGE_FILE_BOOL} --><br />{ADMIN_PAGE_FILE_SIZE}<!-- ELSE -->{ADMIN_PAGE_FILE}<!-- ENDIF --></div></div>
								<b>{PHP.L.Author}:</b> {ADMIN_PAGE_AUTHOR}
							<br /><b>{PHP.L.Owner}:</b> {ADMIN_PAGE_OWNER}
							<br /><b>{PHP.L.Date}:</b> {ADMIN_PAGE_DATE}
							<br /><b>{PHP.L.Begin}:</b> {ADMIN_PAGE_BEGIN}
							<br /><b>{PHP.L.Expire}:</b> {ADMIN_PAGE_EXPIRE}
							<br /><b>{PHP.L.Key}:</b> {ADMIN_PAGE_KEY}
							<br /><b>{PHP.L.Parser}:</b> {ADMIN_PAGE_TYPE}
						</div>
					</div>
				</td>
				<td class="centerall action {ADMIN_PAGE_ODDEVEN}"><a title="{PHP.L.Edit}" href="{ADMIN_PAGE_URL_FOR_EDIT}">{PHP.R.admin_icon_config}</a><a title="{PHP.L.Validate}" href="{ADMIN_PAGE_URL_FOR_VALIDATED}"{ADMIN_PAGE_URL_FOR_VALIDATED_AJAX}>{PHP.R.admin_icon_jumpto}</a><a title="{PHP.L.Delete}" href="{ADMIN_PAGE_URL_FOR_DELETED}"{ADMIN_PAGE_URL_FOR_DELETED_AJAX}>{PHP.R.admin_icon_delete}</a></td>
			</tr>
<!-- END: PAGE_ROW -->
<!-- IF {PHP.is_row_empty} -->
			<tr>
				<td colspan="12">{PHP.L.None}</td>
			</tr>
<!-- ENDIF -->
			<tr>
				<td class="supertruper" colspan="12"><div class="pagnav">{ADMIN_PAGE_PAGINATION_PREV} {ADMIN_PAGE_PAGNAV} {ADMIN_PAGE_PAGINATION_NEXT}</div></td>
			</tr>
			<tr>
				<td class="supertruper" colspan="12">{PHP.L.Total} : {ADMIN_PAGE_TOTALITEMS}, {PHP.L.adm_polls_on_page} : {ADMIN_PAGE_ON_PAGE}</td>
			</tr>
			<tr>
				<td class="supertruper" colspan="12"><input name="paction" type="submit" value="{PHP.L.Validate}"{ADMIN_PAGE_FORM_VALIDATE_AJAX} /> &nbsp; <input name="paction" type="submit" value="{PHP.L.Delete}"{ADMIN_PAGE_FORM_DELETE_AJAX} /></td>
			</tr>
			</table>
		</form>
	</div>
<!-- END: PAGE -->