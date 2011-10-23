<!-- BEGIN: MAIN -->

<div class="col3-2 first">
	<div class="block content">
		<h2 class="folder">{LIST_CATTITLE}</h2>
		<div class="blockbody">
			<table>
				<thead>
					<tr><th>{PHP.L.Title}</th><th>{PHP.L.Status}</th></tr>
				</thead>
				<tbody>
					<!-- BEGIN: LIST_ROW -->
					<tr>
						<td><strong>{LIST_ROW_TITLE}</strong></td>
						<td>{LIST_ROW_LOCALSTATUS}</td>
					</tr>
					<!-- END: LIST_ROW -->
				</tbody>
			</table>
		</div>
	</div>
	<!-- IF {LIST_TOP_PAGINATION} -->
	<p class="paging clear"><span>{PHP.L.Page} {LIST_TOP_CURRENTPAGE} {PHP.L.Of} {LIST_TOP_TOTALPAGES}</span>{LIST_TOP_PAGEPREV}{LIST_TOP_PAGINATION}{LIST_TOP_PAGENEXT}</p>
	<!-- ENDIF -->
</div>

<div class="col3-1">
	<!-- IF {PHP.usr.auth_write} -->
	<div class="block">
		<div class="blockheader admin">{PHP.L.Admin}</div>
		<div class="blockbody">
			<ul class="bullets">
				<!-- IF {PHP.usr.isadmin} -->
				<li><a href="{PHP|cot_url('admin')}">{PHP.L.Adminpanel}</a></li>
				<!-- ENDIF -->
				<li>{LIST_SUBMITNEWPAGE}</li>
			</ul>
		</div>
	</div>
	<!-- ENDIF -->
	<div class="block">
		<div class="blockheader tags">{PHP.L.Tags}</div>
		<div class="blockbody">
			{LIST_TAG_CLOUD}
		</div>
	</div>
	{FILE "{PHP.cfg.themes_dir}/{PHP.theme}/inc/contact.tpl"}
</div>

<!-- END: MAIN -->