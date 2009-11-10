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
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">
				<h4>{PHP.L.Message}</h4>
				<p>{ADMIN_PAGE_ADMINWARNINGS}</p>
			</div>
<!-- ENDIF -->
		<ul class="follow">
			<li>
				<a title="{PHP.L.Configuration}" href="{ADMIN_PAGE_URL_CONFIG}">{PHP.L.Configuration}</a>
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
		<h3>{PHP.L.adm_valqueue}:</h3>
		<form id="form_valqueue" name="form_valqueue" method="post" action="{ADMIN_PAGE_FORM_URL}">
			<table class="cells">
			<tr>
				<td class="coltop" style="width:5%;">
<!-- IF {PHP.cfg.jquery} -->
					<input name="allchek" class="checkbox" type="checkbox" value="" onclick="$('.checkbox').attr('checked', this.checked);" />
<!-- ENDIF -->
				</td>
				<td class="coltop" style="width:5%;">{PHP.L.Id}</td>
				<td class="coltop" style="width:75%;">{PHP.L.Title}</td>
				<td class="coltop" style="width:15%;">{PHP.L.Action}</td>
			</tr>
<!-- BEGIN: PAGE_ROW -->
			<tr>
				<td class="centerall {ADMIN_PAGE_ODDEVEN}">
					<input name="s[{ADMIN_PAGE_ID}]" type="checkbox" class="checkbox" />
				</td>
				<td class="centerall {ADMIN_PAGE_ODDEVEN}">
					{ADMIN_PAGE_ID}
				</td>
				<td class="{ADMIN_PAGE_ODDEVEN}">
					<div id="mor_{PHP.ii}" class='mor_info_on_off'>
						<span class="strong" style="cursor:hand;">{ADMIN_PAGE_SHORTTITLE}</span>
						<div class="moreinfo">
							<hr />
							<table class="flat">
								<tr>
									<td style="width:20%;">{PHP.L.Category}:</td>
									<td style="width:80%;"><a href="{ADMIN_PAGE_CAT_URL}">{ADMIN_PAGE_CATICON}{ADMIN_PAGE_CAT_TITLE}</a></td>
								</tr>
								<tr>
									<td>{PHP.L.Description}:</td>
									<td>{ADMIN_PAGE_DESC}</td>
								</tr>
								<tr>
									<td>{PHP.L.Text}:</td>
									<td>{ADMIN_PAGE_TEXT}</td>
								</tr>
							</table>
						</div>
					</div>
				</td>
				<td class="centerall action {ADMIN_PAGE_ODDEVEN}">
					<a title="{PHP.L.Validate}" href="{ADMIN_PAGE_URL_FOR_VALIDATED}"{ADMIN_PAGE_URL_FOR_VALIDATED_AJAX}>{PHP.R.admin_icon_discheck1}</a>
					<a title="{PHP.L.Delete}" href="{ADMIN_PAGE_URL_FOR_DELETED}"{ADMIN_PAGE_URL_FOR_DELETED_AJAX}>{PHP.R.admin_icon_discheck0}</a>
					<a title="{PHP.L.Open}" href="{ADMIN_PAGE_ID_URL}">{PHP.R.admin_icon_jumpto}</a>
					<a title="{PHP.L.Edit}" href="{ADMIN_PAGE_URL_FOR_EDIT}">{PHP.R.admin_icon_config}</a>
				</td>
			</tr>
<!-- END: PAGE_ROW -->
<!-- IF {PHP.is_row_empty} -->
			<tr>
				<td colspan="4">{PHP.L.None}</td>
			</tr>
<!-- ENDIF -->
			<tr>
				<td class="valid" colspan="4">
					<input name="paction" type="submit" value="{PHP.L.Validate}"{ADMIN_PAGE_FORM_VALIDATE_AJAX} />
					<input name="paction" type="submit" value="{PHP.L.Delete}"{ADMIN_PAGE_FORM_DELETE_AJAX} />
				</td>
			</tr>
			</table>
			<p class="paging">
				{ADMIN_PAGE_PAGINATION_PREV}{ADMIN_PAGE_PAGNAV}{ADMIN_PAGE_PAGINATION_NEXT}<span class="a1">{PHP.L.Total}: {ADMIN_PAGE_TOTALITEMS}, {PHP.L.adm_polls_on_page}: {ADMIN_PAGE_ON_PAGE}</span>
			</p>
		</form>
	</div>
<!-- END: PAGE -->