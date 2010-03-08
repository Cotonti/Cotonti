<!-- BEGIN: MAIN -->

			<div id="left">

				<h1>{PAGE_SHORTTITLE}</h1>

				<p class="breadcrumb">{PHP.skinlang.list.bread}: <a href="index.php">{PHP.L.Home}</a> {PHP.cfg.separator} {PAGE_CATPATH}</p>

				<div class="pagetext">
					{PAGE_TEXT}
				</div>

				<!-- BEGIN: PAGE_MULTI -->
				<div class="paging">{PAGE_MULTI_TABNAV}</div>
				<div class="multi">
					<strong>{PHP.skinlang.page.Summary}: </strong>
					{PAGE_MULTI_TABTITLES}
				</div>
				<!-- END: PAGE_MULTI -->

				<div class="seccat right small gray">

					<span class="fleft">{PHP.skinlang.index.by} {PAGE_OWNER}, {PAGE_BEGIN}
					<!-- IF {PHP.usr.isadmin} -->
					 - {PAGE_ADMIN_COUNT} {PHP.skinlang.page.views}
					<!-- ENDIF -->
					</span>

					<!-- IF {PAGE_BEGIN} != {PAGE_DATE} -->
					<em>{PHP.skinlang.page.update} {PAGE_DATE}</em>
					<!-- ELSE -->
					<em>{PAGE_DESC}</em>
					<!-- ENDIF -->

				</div>

				<hr />
				{PAGE_COMMENTS_DISPLAY}

			</div>

		</div>
	</div>

	<div id="right">

		<!-- BEGIN: PAGE_FILE -->
		<h3>{PHP.L.Download}</h3>

			<!-- BEGIN: MEMBERSONLY -->
			<div class="restrict padding15">
				{PHP.skinlang.page.members}<br />
				<a href="users.php?m=auth">{PHP.L.Login} {PHP.skinlang.forumspost.to} {PHP.L.Download}</a>
			</div>
			<!-- END: MEMBERSONLY -->

			<!-- BEGIN: DOWNLOAD -->
			<div class="download padding15">
				<a href="{PAGE_FILE_URL}">{PAGE_FILE_ICON} {PAGE_FILE_NAME}</a>
				<strong>{PAGE_FILE_SIZE}{PHP.L.kb}</strong> <br />
				<span class="small">{PHP.skinlang.page.downloaded}: {PAGE_FILE_COUNT}</span>
			</div>
			<!-- END: DOWNLOAD -->

		<!-- END: PAGE_FILE -->

		<h3><a href="page.php?id={PHP.pag.page_id}#com">{PHP.L.Comments}: {PHP.pag.page_comcount}</a></h3>
		<div class="h3"><div class="colright">{PAGE_RATINGS}</div>{PHP.L.Ratings}{PAGE_RATINGS_DISPLAY}</div>
		<h3><a href="{PAGE_COMMENTS_RSS}">{PHP.skinlang.list.rss}</a></h3>

		<!-- IF {PHP.tag_i} > 0 -->
		<h3>{PHP.L.Tags}</h3>
		<div class="box padding15 pageadd">
		<!-- ENDIF -->

			<!-- BEGIN: PAGE_TAGS_ROW -->
			<!-- IF {PHP.tag_i} > 0 -->, <!-- ENDIF --><a href="{PAGE_TAGS_ROW_URL}">{PAGE_TAGS_ROW_TAG}</a> 
			<!-- END: PAGE_TAGS_ROW -->

		<!-- IF {PHP.tag_i} > 0 -->
		</div>
		<!-- ENDIF -->

		<!-- BEGIN: PAGE_ADMIN -->
		<h3 class="adm">{PHP.skinlang.page.admin}</h3>
		<div class="boxa padding15 admin">
			{PAGE_ADMIN_EDIT} {PAGE_ADMIN_UNVALIDATE}
		</div>
		<!-- END: PAGE_ADMIN -->

		&nbsp;

	</div>

	<br class="clear" />

<!-- END: MAIN -->