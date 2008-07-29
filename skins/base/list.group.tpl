<!-- BEGIN: MAIN -->

<div id="title">

	{LIST_PAGETITLE}

</div>

<div id="subtitle">

	{LIST_CATDESC}

</div>

<div id="main">

	<ul>

		<!-- BEGIN: LIST_ROWCAT -->

		<li style="margin-top:8px;">
			<strong><a href="{LIST_ROWCAT_URL}">{LIST_ROWCAT_TITLE}</a> ({LIST_ROWCAT_COUNT})</strong><br />
			<span class="desc">{LIST_ROWCAT_DESC}</span>
		</li>

		<!-- END: LIST_ROWCAT -->

		<!-- BEGIN: LIST_ROW -->

		<li>
			<strong><a href="{LIST_ROW_URL}">{LIST_ROW_TITLE}</a></strong> {LIST_ROW_FILEICON}<br />
			<span class="desc">{LIST_ROW_DESC} ({LIST_ROW_COUNT} {PHP.skinlang.list.hits})</span>
		</li>

		<!-- END: LIST_ROW -->

	</ul>

</div>

<!-- END: MAIN -->
