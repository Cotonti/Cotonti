<!-- BEGIN: MAIN -->
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

		<h2>{PHP.L.Pages} ({ADMIN_PAGE_TOTALDBPAGES})</h2>
		{FILE "{PHP.cfg.themes_dir}/nemesis/warnings.tpl"}
		<div class="block">
			<ul class="follow">
				<li><a title="{PHP.L.Configuration}" href="{ADMIN_PAGE_URL_CONFIG}">{PHP.L.Configuration}</a></li>
				<li><a href="{ADMIN_PAGE_URL_ADD}">{PHP.L.page_addtitle}</a></li>
				<li><a href="{ADMIN_PAGE_URL_EXTRAFIELDS}">{PHP.L.adm_extrafields_desc}</a></li>
				<li><a href="{ADMIN_PAGE_URL_STRUCTURE}">{PHP.L.Categories}</a></li>
			</ul>
		</div>
		<div class="block">
			<h3>{PHP.L.Pages}:</h3>
			<form id="form_valqueue" name="form_valqueue" method="post" action="{ADMIN_PAGE_FORM_URL}">
			<table class="cells">
				<tr>
					<td class="right" colspan="4">
						<input type="hidden" name="paction" value="" />
						<!-- IF {ADMIN_PAGE_TOTALITEMS} > 1 -->{PHP.L.adm_sort} {ADMIN_PAGE_ORDER} {ADMIN_PAGE_WAY};<!-- ENDIF --> {PHP.L.Show} {ADMIN_PAGE_FILTER}
						<input name="paction" type="submit" value="{PHP.L.Filter}" onclick="this.form.paction.value=this.value" />
					</td>
				</tr>
				<tr>
					<td class="coltop width5">
						<!-- IF {PHP.cfg.jquery} -->
						<input name="allchek" class="checkbox" type="checkbox" value="" onclick="$('.checkbox').attr('checked', this.checked);" />
						<!-- ENDIF -->
					</td>
					<td class="coltop width5">{PHP.L.Id}</td>
					<td class="coltop width70">{PHP.L.Title}</td>
					<td class="coltop width20">{PHP.L.Action}</td>
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
										<td class="width20">{PHP.L.Category}:</td>
										<td class="width80"><a href="{ADMIN_PAGE_CAT_URL}">{ADMIN_PAGE_CATICON}{ADMIN_PAGE_CAT_TITLE}</a></td>
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
						<!-- IF {PHP.row.page_state} == 1 --><a title="{PHP.L.Validate}" href="{ADMIN_PAGE_URL_FOR_VALIDATED}" class="ajax">{PHP.R.icon_news}</a><!-- ENDIF -->
						<a title="{PHP.L.Delete}" href="{ADMIN_PAGE_URL_FOR_DELETED}" class="ajax">{PHP.R.icon_delete}</a>
						<a title="{PHP.L.Open}" href="{ADMIN_PAGE_ID_URL}" target="_blank">{PHP.R.admin_icon_jumpto}</a>
						<a title="{PHP.L.Edit}" href="{ADMIN_PAGE_URL_FOR_EDIT}" target="_blank">{PHP.R.admin_icon_config}</a>
					</td>
				</tr>
<!-- END: PAGE_ROW -->
<!-- IF {PHP.is_row_empty} -->
				<tr>
					<td class="centerall" colspan="4">{PHP.L.None}</td>
				</tr>
<!-- ENDIF -->
				<tr>
					<td class="valid" colspan="4">
						<!-- IF {PHP.filter} != {PHP.L.adm_validated} --><input name="paction" type="submit" value="{PHP.L.Validate}" onclick="this.form.paction.value=this.value" /><!-- ENDIF -->
						<input name="paction" type="submit" value="{PHP.L.Delete}" onclick="this.form.paction.value=this.value" />
					</td>
				</tr>
			</table>
			<p class="paging">
				{ADMIN_PAGE_PAGINATION_PREV}{ADMIN_PAGE_PAGNAV}{ADMIN_PAGE_PAGINATION_NEXT}<span>{PHP.L.Total}: {ADMIN_PAGE_TOTALITEMS}, ???: {ADMIN_PAGE_ON_PAGE}</span>
			</p>
			</form>
		</div>
<!-- END: MAIN -->