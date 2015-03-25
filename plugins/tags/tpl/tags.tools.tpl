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
		<h2>{PHP.L.tags_All}</h2>
		{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
			<div class="block button-toolbar">
				<a title="{PHP.L.Configuration}" href="{ADMIN_TAGS_CONFIG_URL}" class="button">{PHP.L.Configuration}</a>
			</div>
			<h3>{PHP.L.viewdeleteentries}:</h3>
			<table class="cells">
				<tr>
					<td class="right" colspan="5">
						<form name="sortfiltertag" action="{ADMIN_TAGS_FORM_ACTION}" method="post">
							<!-- IF {ADMIN_TAGS_TOTALITEMS} > 1 -->{PHP.L.adm_sort} {ADMIN_TAGS_ORDER} {ADMIN_TAGS_WAY};<!-- ENDIF --> {PHP.L.Show} {ADMIN_TAGS_FILTER}; {PHP.L.Search} <input name="tag" type="text" value="" />
							<input name="paction" type="submit" value="{PHP.L.Filter}" />
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
					<td class="textcenter"><b>{ADMIN_TAGS_CODE}</b></td>
					<td class="textcenter">{ADMIN_TAGS_AREA}</td>
					<td class="textcenter">{ADMIN_TAGS_COUNT}</td>
					<td>
						<div id="mor_{PHP.ii}" class='mor_info_on_off'>
							<span style="cursor:pointer;">{ADMIN_TAGS_ITEMS}</span><br />
							<div class="moreinfo">
<!-- BEGIN: ADMIN_TAGS_ROW_ITEMS -->
								{ADMIN_TAGS_ITEM_TITLE}<br />
<!-- END: ADMIN_TAGS_ROW_ITEMS -->
							</div>
						</div>
					</td>
					<td class="centerall action">
						<form name="tagedit{PHP.ii}" action="{ADMIN_TAGS_FORM_ACTION}" method="post">
							<input name="old_tag" type="hidden" value="{ADMIN_TAGS_CODE|htmlspecialchars($this)}" />
							<input name="d" type="hidden" value="{PHP.d}" />
							<input name="sorttype" type="hidden" value="{PHP.sorttype}" />
							<input name="sortway" type="hidden" value="{PHP.sortway}" />
							<input name="filter" type="hidden" value="{PHP.filter}" />
							{ADMIN_TAGS_TAG}
							<input name="action" type="submit" value="{PHP.L.Edit}" /><!--//<a title="{PHP.L.Edit}" href="{ADMIN_TAGS_URL_FOR_EDIT}" target="_blank" class="button">{PHP.L.Edit}</a>//-->
							<input name="action" type="submit" value="{PHP.L.Delete}" /><!--//<a title="{PHP.L.Delete}" href="{ADMIN_TAGS_DEL_URL}" class="ajax button">{PHP.L.Delete}</a>//-->
						</form>
					</td>
				</tr>
<!-- END: ADMIN_TAGS_ROW -->
			</table>
			<p class="paging">{ADMIN_TAGS_PAGINATION_PREV}{ADMIN_TAGS_PAGNAV}{ADMIN_TAGS_PAGINATION_NEXT}<span>{PHP.L.Total}: {ADMIN_TAGS_TOTALITEMS}, {PHP.L.Onpage}: {ADMIN_TAGS_COUNTER_ROW}</span></p>
<!-- END: MAIN -->