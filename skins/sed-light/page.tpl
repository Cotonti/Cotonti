<!-- BEGIN: MAIN -->

	<div class="mboxHD">{PAGE_TITLE}</div>
	<div class="mboxBody">

		<div class="pageBody">
			<div class="pageTop"></div>
			<div class="pageText">

				<div id="subtitle">{PAGE_DESC}</div>

				<div style="float:right;text-align:right;">
				<span class="rss-icon">
				<a href="{PAGE_COMMENTS_RSS}"><img src="skins/{PHP.skin}/img/rss-icon.png" border="0" alt="" /></a>
				</span>
					{PHP.skinlang.page.Comments} {PAGE_COMMENTS}<br />{PHP.skinlang.page.Ratings} {PAGE_RATINGS}</div>
					{PHP.skinlang.page.Author} {PAGE_AUTHOR}<br />
					{PHP.skinlang.page.Submittedby} {PAGE_OWNER} &nbsp; {PHP.skinlang.page.Date} {PAGE_DATE}<br />

				<!-- BEGIN: PAGE_ADMIN -->
				{PAGE_ADMIN_UNVALIDATE} &nbsp; {PAGE_ADMIN_EDIT} &nbsp; ({PAGE_ADMIN_COUNT})<br />
				<!-- END: PAGE_ADMIN -->
				{PAGE_RATINGS_DISPLAY}
			</div>
		</div>

		<div class="fmsg" style="padding:0 5px; width:770px; overflow-x:auto;" curpos="0" maxpos="0" pageincrement="624" increment="10" disabled="true" collapsed="true">{PAGE_TEXT}</div>

		<!-- BEGIN: PAGE_MULTI -->
		<div class="paging">{PAGE_MULTI_TABNAV}</div>
		<div class="block">
			<h5>{PHP.skinlang.page.Summary}</h5>
			{PAGE_MULTI_TABTITLES}
		</div>
		<!-- END: PAGE_MULTI -->

		<div class="block">
		<strong>{PHP.L.Tags}:</strong>
		<!-- BEGIN: PAGE_TAGS_ROW -->
		<a href="{PAGE_TAGS_ROW_URL}">{PAGE_TAGS_ROW_TAG}</a>
		<!-- END: PAGE_TAGS_ROW -->
		<!-- BEGIN: PAGE_NO_TAGS -->
		{PAGE_NO_TAGS}
		<!-- END: PAGE_NO_TAGS -->
		</div>

		<!-- BEGIN: PAGE_FILE -->
		<br /><br /><hr />

		<div class="pageBody">
			<div class="pageTop"></div>
			<div class="pageText">
				<!-- BEGIN: MEMBERSONLY -->
				{PAGE_FILE_ICON} {PAGE_SHORTTITLE}<br/>
				<!-- END: MEMBERSONLY -->
				<!-- BEGIN: DOWNLOAD -->
				{PAGE_FILE_ICON}<a href="{PAGE_FILE_URL}">{PHP.L.Download} : {PAGE_SHORTTITLE}</a><br/>
				<!-- END: DOWNLOAD -->
				{PHP.L.Size}: {PAGE_FILE_SIZE}{PHP.L.kb}, {PHP.skinlang.page.downloaded} {PAGE_FILE_COUNT} {PHP.skinlang.page.times}
			</div>
		</div>
		<!-- END: PAGE_FILE -->

		<div class="pageBody">
			<div class="pageTop"></div>
			<div class="pageText">
				{PAGE_COMMENTS_DISPLAY}
			</div>
		</div>

	</div>

<!-- END: MAIN -->