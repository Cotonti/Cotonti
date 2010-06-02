<!-- BEGIN: MAIN -->

		<div id="center" class="column">
			<div class="block">
				<h2 class="folder">{LIST_PAGETITLE}</h2>
				<!-- BEGIN: LIST_ROWCAT -->
					<h3><a href="{LIST_ROWCAT_URL}" title="{LIST_ROWCAT_TITLE}">{LIST_ROWCAT_TITLE}</a> ({LIST_ROWCAT_COUNT})</h3>
					<!-- IF {LIST_ROWCAT_DESC} -->
					<p class="small">{LIST_ROWCAT_DESC}</p>
					<!-- ENDIF -->
				<!-- END: LIST_ROWCAT -->
				<!-- IF {LISTCAT_PAGNAV} -->
				<p class="pagnav">{LISTCAT_PAGEPREV}{LISTCAT_PAGNAV}{LISTCAT_PAGENEXT}</p>
				<!-- ENDIF -->
			</div>
		</div>
		<div id="side" class="column">
		<!-- IF {PHP.usr.isadmin} -->
			<div class="block">
				<h2 class="admin">{PHP.L.Admin}</h2>
				<ul class="bullets">
					<li><a href="admin.php">{PHP.L.Adminpanel}</a></li>
					<li>{LIST_SUBMITNEWPAGE}</li>
				</ul>
			</div>
		<!-- ENDIF -->
			<div class="block">
				<h2 class="tags">{LIST_TOP_TAG_CLOUD}</h2>
				{LIST_TAG_CLOUD}
			</div>
		</div>

<!-- END: MAIN -->