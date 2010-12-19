<!-- BEGIN: MAIN -->

			<div id="left" class="whitee">

				<h1>{PHP.L.Tags}</h1>

				<!-- you are here -->
				<p class="breadcrumb">{PHP.themelang.list.bread}: <a href="index.php">{PHP.L.Home}</a> {PHP.cfg.separator} <a href="plug.php?e=tags">{PHP.L.Tags}</a><!-- IF {TAGS_QUERY} --> {PHP.cfg.separator} {TAGS_QUERY}<!-- ENDIF --></p>

				<!-- BEGIN: TAGS_CLOUD -->
				{TAGS_CLOUD_BODY}
				<!-- END: TAGS_CLOUD -->

				<!-- BEGIN: TAGS_RESULT -->
				<h3>{TAGS_RESULT_TITLE}</h3>
				<div class="paging">{TAGS_PAGEPREV} {TAGS_PAGNAV} {TAGS_PAGENEXT}</div>
				<ul>
				<!-- BEGIN: TAGS_RESULT_ROW -->
					<li> <strong class="admin nou"><a href="{TAGS_RESULT_ROW_URL}">{TAGS_RESULT_ROW_TITLE}</a></strong><br />
					<span class="hint">{TAGS_RESULT_ROW_PATH} {PHP.L.Tags}: {TAGS_RESULT_ROW_TAGS}</span> </li>
				<!-- END: TAGS_RESULT_ROW -->
				</ul>
				<!-- END: TAGS_RESULT -->
				<div class="paging">{TAGS_PAGEPREV} {TAGS_PAGNAV} {TAGS_PAGENEXT}</div>

			</div>

		</div>
	</div>

	<div id="right">
		<h3>{PHP.L.Search}</h3>
		<div class="box padding15">
			<form action="{TAGS_ACTION}" method="post">
			<input type="text" name="t" value="{TAGS_QUERY}" />
			<select name="order">
				<option value="">{PHP.L.tags_Orderby}</option>
				<option value="">--</option>
				{TAGS_ORDER}
			</select>
			<p style="margin:10px 0"><input type="submit" class="submit" value="{PHP.L.Filter}" /></p>
			<em class="fs9">{TAGS_HINT}</em>
			</form>
		</div>
		&nbsp;
	</div>

	<br class="clear" />

<!-- END: MAIN -->