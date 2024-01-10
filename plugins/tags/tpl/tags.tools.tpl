<!-- BEGIN: MAIN -->
<script type="text/javascript">
	// @todo перенести в подвал. Избавиться от jQuery
	$(document).ready(function() {
		$('.moreinfo').hide();
		$(".mor_info_on_off").click(function() {
			var kk = $(this).attr('id');
			$('#'+kk).children('.moreinfo').slideToggle(100);
		});
	});
</script>
{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}
<div class="block button-toolbar">
	<a title="{PHP.L.Configuration}" href="{ADMIN_TAGS_CONFIG_URL}" class="button">{PHP.L.Configuration}</a>
</div>

<div class="block">
	<h2>{PHP.L.viewdeleteentries}:</h2>
	<table class="cells">
		<tr>
			<td class="right" colspan="5">
				<form name="sortfiltertag" class="filter" action="{ADMIN_TAGS_FILTERS_ACTION}" method="GET">
					{ADMIN_TAGS_FILTERS_PARAMS}
					<!-- IF {ADMIN_TAGS_TOTALITEMS} > 1 -->
					{PHP.L.adm_sort} {ADMIN_TAGS_FILTERS_ORDER} {ADMIN_TAGS_FILTERS_WAY}
					<!-- ENDIF -->
					<span class="marginleft10">{PHP.L.Show}</span> {ADMIN_TAGS_FILTERS_FILTER}
					<span class="marginleft10">{PHP.L.Search}</span> {ADMIN_TAGS_FILTERS_SEARCH}
					<button type="submit">{PHP.L.Filter}</button>
				</form>
			</td>
		</tr>
		<tr>
			<td class="coltop width15">{PHP.L.Code}</td>
			<td class="coltop width5">{PHP.L.adm_area}</td>
			<td class="coltop width5">{PHP.L.Count}</td>
			<td class="coltop" style="width:250px;"> {PHP.L.adm_tag_item_area}</td>
			<td class="coltop width35">{PHP.L.Action}</td>
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
	</table>
	<p class="paging">
		{ADMIN_TAGS_PREVIOUS_PAGE}{ADMIN_TAGS_PAGINATION}{ADMIN_TAGS_NEXT_PAGE}
		<span>{PHP.L.Total}: {ADMIN_TAGS_TOTAL_ENTRIES}, {PHP.L.Onpage}: {ADMIN_TAGS_COUNTER_ROW}</span>
	</p>
</div>
<!-- END: MAIN -->