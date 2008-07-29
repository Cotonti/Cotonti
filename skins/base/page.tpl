<!-- BEGIN: MAIN -->

<div id="title">

	{PAGE_TITLE}

</div>

<div id="subtitle">


	{PAGE_DESC}<br />
	{PHP.skinlang.page.Author} {PAGE_AUTHOR}<br />
	{PHP.skinlang.page.Submittedby} {PAGE_OWNER} &nbsp; {PHP.skinlang.page.Date} {PAGE_DATE}<br />

	<!-- BEGIN: PAGE_ADMIN -->

	{PAGE_ADMIN_UNVALIDATE} &nbsp; {PAGE_ADMIN_EDIT} &nbsp; ({PAGE_ADMIN_COUNT})<br />

	<!-- END: PAGE_ADMIN -->

	{PHP.skinlang.page.Comments} {PAGE_COMMENTS} &nbsp; {PHP.skinlang.page.Ratings} {PAGE_RATINGS} <br />
	{PAGE_COMMENTS_DISPLAY}{PAGE_RATINGS_DISPLAY}

</div>

<div id="main">

	{PAGE_TEXT}

	<!-- BEGIN: PAGE_MULTI -->

		<div class="paging">

			{PAGE_MULTI_TABNAV}

		</div>

		<div class="block">
			<h5>{PHP.skinlang.page.Summary}</h5>

			{PAGE_MULTI_TABTITLES}

		</div>

	<!-- END: PAGE_MULTI -->

	<!-- BEGIN: PAGE_FILE -->

		<div class="download">

			<a href="{PAGE_FILE_URL}">Download : {PAGE_SHORTTITLE} {PAGE_FILE_ICON}</a><br/>
			Size: {PAGE_FILE_SIZE}KB, downloaded {PAGE_FILE_COUNT} times

		</div>

	<!-- END: PAGE_FILE -->

</div>

<!-- END: MAIN -->