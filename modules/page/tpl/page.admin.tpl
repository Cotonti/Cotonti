<!-- BEGIN: MAIN -->
<div class="block button-toolbar">
	<a href="{ADMIN_PAGE_URL_CONFIG}" class="button">{PHP.L.Configuration}</a>
	<a href="{ADMIN_PAGE_URL_STRUCTURE}" class="button">{PHP.L.Categories}</a>
	<a href="{ADMIN_PAGE_URL_EXTRAFIELDS}" class="button">{PHP.L.adm_extrafields_desc}</a>
	<a href="{ADMIN_PAGE_URL_ADD}" class="button special">{PHP.L.page_addtitle}</a>
</div>

{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

<div class="block">
	<!-- IF {ADMIN_PAGE_TOTALDBPAGES} -->
	<form name="form_valqueue" method="get" action="{PHP|cot_url('admin', 'm=page')}">
		<!-- IF !{PHP|cot_plugin_active('urleditor')} OR {PHP.cfg.plugin.urleditor.preset} != 'handy' -->
		<input type="hidden" name="m" value="page" />
		<!-- ENDIF -->
		<table class="cells">
			<tr>
				<td class="coltop w-5"></td>
				<td class="coltop w-5"></td>
				<td class="coltop w-15 filterSelect">{ADMIN_PAGE_FILTER}</td>
				<td class="coltop w-50">
					<!-- IF {TOTAL_ENTRIES} > 1 -->
					{PHP.L.adm_sort} {ADMIN_PAGE_ORDER} {ADMIN_PAGE_WAY}
					<!-- ENDIF -->
				</td>
				<td class="coltop w-25"><button type="submit">{PHP.L.Filter}</button></td>
			</tr>
		</table>
	</form>
	<!-- ENDIF -->
	<form id="form_valqueue" name="form_valqueue" method="post" action="{ADMIN_PAGE_FORM_URL}">
		<table class="cells">
			<tr>
				<td class="coltop w-5">
					<!-- IF {PHP.cfg.jquery} -->
					<input name="allchek" class="checkbox" type="checkbox" value="" onclick="$('.checkbox').attr('checked', this.checked);" />
					<!-- ENDIF -->
				</td>
				<td class="coltop w-5">{PHP.L.Id}</td>
				<td class="coltop w-15">{PHP.L.Status}</td>
				<td class="coltop w-50">{PHP.L.Title}</td>
				<td class="coltop w-25">{PHP.L.Action}</td>
			</tr>
			<!-- BEGIN: PAGE_ROW -->
			<tr>
				<td class="centerall {ADMIN_PAGE_ODDEVEN}">
					<input name="s[{ADMIN_PAGE_ID}]" type="checkbox" class="checkbox" />
				</td>
				<td class="centerall {ADMIN_PAGE_ODDEVEN}">
					{ADMIN_PAGE_ID}
				</td>
				<td>
					{ADMIN_PAGE_LOCAL_STATUS}
				</td>
				<td class="{ADMIN_PAGE_ODDEVEN}">
					<div id="mor_{PHP.ii}" class='mor_info_on_off'>
						<span class="strong" style="cursor:hand;">{ADMIN_PAGE_TITLE}</span>
						<div class="moreinfo">
							<hr />
							<table class="flat">
								<tr>
									<td class="w-20">{PHP.L.Category}:</td>
									<td class="w-80">{ADMIN_PAGE_CAT_PATH_SHORT}</td>
								</tr>
								<tr>
									<td>{PHP.L.Description}:</td>
									<td>{ADMIN_PAGE_DESCRIPTION}</td>
								</tr>
								<tr>
									<td>{PHP.L.Text}:</td>
									<td>{ADMIN_PAGE_TEXT}</td>
								</tr>
							</table>
						</div>
					</div>
				</td>
				<td class="action {ADMIN_PAGE_ODDEVEN}">
					<!-- IF {PHP.row.page_state} == 1 --><a href="{ADMIN_PAGE_URL_FOR_VALIDATED}" class="button confirmLink">{PHP.L.Validate}</a><!-- ENDIF -->
					<a href="{ADMIN_PAGE_URL_FOR_DELETED}" class="button confirmLink">{PHP.L.Delete}</a>
					<a href="{ADMIN_PAGE_ID_URL}" target="_blank" class="button special">{PHP.L.Open}</a>
					<a href="{ADMIN_PAGE_URL_FOR_EDIT}" target="_blank" class="button">{PHP.L.Edit}</a>
				</td>
			</tr>
			<!-- END: PAGE_ROW -->
			<!-- IF !{TOTAL_ENTRIES} -->
			<tr>
				<td class="centerall" colspan="5">{PHP.L.None}</td>
			</tr>
			<!-- ELSE -->
			<tr>
				<td class="valid" colspan="5">
					<!-- IF {PHP.filter} != 'validated' -->
					<button name="paction" type="submit" value="validate" class="confirm">{PHP.L.Validate}</button>
					<!-- ENDIF -->
					<button name="paction" type="submit" value="delete" class="confirm">{PHP.L.Delete}</button>
				</td>
			</tr>
			<!-- ENDIF -->
		</table>
	</form>
	<!-- IF {TOTAL_ENTRIES} -->
	<p class="paging">
		{PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}
		<span>{PHP.L.Total}: {TOTAL_ENTRIES}, {PHP.L.Onpage}: {ENTRIES_ON_CURRENT_PAGE}</span>
	</p>
	<!-- ENDIF -->
</div>
<style>
	.filterSelect select{
		width: calc(100% - .150rem);
	}
</style>
<script type="text/javascript">
	$('.moreinfo').hide();
	$('.mor_info_on_off').click(function() {
		let kk = $(this).attr('id');
		$('#' + kk).children('.moreinfo').slideToggle(100);
	});

	let submitButtons = document.querySelectorAll('.confirm');
	let form = document.getElementById('form_valqueue');
	submitButtons.forEach(function(elem) {
		elem.addEventListener('click', function(e) {
			let checkedCnt = form.querySelectorAll('input[type=checkbox]:checked').length;
			if (checkedCnt < 1) {
				e.preventDefault();
				return false;
			}

			let message = 'Are you sure?';
			switch(this.value) {
				case 'delete':
					message = '{PHP.L.page_confirm_delete}';
					break;

				case 'validate':
					message = '{PHP.L.page_confirm_validate}';
					break;
			}

			if (!confirm(message)) {
				e.preventDefault();
			}
		});
	});
</script>
<!-- END: MAIN -->