<!-- BEGIN: MAIN -->
<div class="block button-toolbar">
	<a href="{ADMIN_TAGS_CONFIG_URL}" class="button">{PHP.L.Configuration}</a>
</div>

{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

<div class="block">
	<form name="sortfiltertag" class="filter" action="{ADMIN_TAGS_FILTERS_ACTION}" method="GET">
	<table class="cells">
		<tr>
			<td class="coltop w-15">{ADMIN_TAGS_FILTERS_FILTER}</td>
			<td class="coltop w-10"></td>
			<td class="coltop w-10"></td>
			<td class="coltop">
				{ADMIN_TAGS_FILTERS_PARAMS}
				<!-- IF {TOTAL_ENTRIES} > 1 -->
				{PHP.L.adm_sort} {ADMIN_TAGS_FILTERS_ORDER} {ADMIN_TAGS_FILTERS_WAY}
				<!-- ENDIF -->
			</td>
			<td class="coltop w-35">
				{PHP.L.Search} {ADMIN_TAGS_FILTERS_SEARCH}
				<button type="submit">{PHP.L.Filter}</button>
			</td>
		</tr>
	</table>
	</form>
	<table class="cells">
		<tr>
			<td class="coltop w-15">{PHP.L.Code}</td>
			<td class="coltop w-10">{PHP.L.adm_area}</td>
			<td class="coltop w-10">{PHP.L.Count}</td>
			<td class="coltop">{PHP.L.adm_tag_item_area}</td>
			<td class="coltop w-35">{PHP.L.Action}</td>
		</tr>
		<!-- BEGIN: ADMIN_TAGS_ROW -->
		<tr>
			<td class="textcenter"><b>{ADMIN_TAGS_ROW_CODE}</b></td>
			<td class="textcenter">{ADMIN_TAGS_ROW_AREA}</td>
			<td class="textcenter">{ADMIN_TAGS_ROW_COUNT}</td>
			<td>
				<div id="mor_{PHP.ii}" class='mor_info_on_off'>
					<span style="cursor:pointer;">{ADMIN_TAGS_ROW_ITEMS}</span><br />
					<div class="moreinfo">
						<!-- BEGIN: ADMIN_TAGS_ROW_ITEMS -->
						<!-- IF {ADMIN_TAGS_ROW_ITEM_URL} --><a href="{ADMIN_TAGS_ROW_ITEM_URL}"><!-- ENDIF -->{ADMIN_TAGS_ROW_ITEM_TITLE}<!-- IF {ADMIN_TAGS_ROW_ITEM_URL} --></a><!-- ENDIF -->
						<br />
						<!-- END: ADMIN_TAGS_ROW_ITEMS -->
					</div>
				</div>
			</td>
			<td class="centerall action">
				<form name="tagedit{PHP.ii}" action="{ADMIN_TAGS_ROW_FORM_ACTION}" method="POST">
					<input type="hidden" name="action" value="edit" />
					<input type="hidden" name="old_tag" value="{ADMIN_TAGS_ROW_CODE|htmlspecialchars($this)}" />
					<input type="text" name="tag" value="{ADMIN_TAGS_ROW_CODE|htmlspecialchars($this)}" maxlength="255" />
					<button type="submit">{PHP.L.Edit}</button>
					<a href="{ADMIN_TAGS_ROW_DELETE_CONFIRM_URL}" class="button confirmLink">{PHP.L.Delete}</a>
				</form>
			</td>
		</tr>
		<!-- END: ADMIN_TAGS_ROW -->
		<!-- IF !{TOTAL_ENTRIES} -->
		<tr>
			<td class="textcenter" colspan="5">{PHP.L.None}</td>
		</tr>
		<!-- ENDIF -->
	</table>
	<!-- IF {TOTAL_ENTRIES} -->
	<p class="paging">
		{PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}
		<span>{PHP.L.Total}: {TOTAL_ENTRIES}, {PHP.L.Onpage}: {ENTRIES_ON_CURRENT_PAGE}</span>
	</p>
	<!-- ENDIF -->
</div>
<script type="text/javascript">
	$('.moreinfo').hide();
	$('.mor_info_on_off').click(function() {
		let kk = $(this).attr('id');
		$('#' + kk).children('.moreinfo').slideToggle(100);
	});
</script>
<!-- END: MAIN -->