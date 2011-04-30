<!-- BEGIN: MAIN -->
	<div class="mboxHD">{PHP.L.Tags}</div>
	<div class="mboxBody">
		<div class="pageBody">
			<div class="pageTop"></div>
			<div class="pageText">
				<form action="{TAGS_ACTION}" method="post">
					<input type="text" name="t" value="{TAGS_QUERY}" />
					<input type="submit" value="&gt;&gt;" />
					<select name="order">
						<option value="">{PHP.L.tags_Orderby}</option><option value="">--</option>
						{TAGS_ORDER}
					</select><br />
					<em>{TAGS_HINT}</em>
				</form>
			</div>
		</div>

		<div class="fmsg" style="padding:0 5px; width:770px; overflow-x:auto;">
		<!-- BEGIN: TAGS_CLOUD -->
		<div class="block">
			{TAGS_CLOUD_BODY}
		</div>
		<!-- END: TAGS_CLOUD -->

		<!-- BEGIN: TAGS_RESULT -->
		<h3>{TAGS_RESULT_TITLE}</h3>
		<table class="cells" cellspacing="1" cellpadding="2">
			<tr><td style="background:transparent;"><div class="pagnav">{TAGS_PAGEPREV} {TAGS_PAGNAV} {TAGS_PAGENEXT}</div></td></tr>
			<!-- BEGIN: TAGS_RESULT_ROW -->
			<tr>
				<td style="background:transparent;">
				<strong><a href="{TAGS_RESULT_ROW_URL}">{TAGS_RESULT_ROW_TITLE}</a></strong><br />
				<span class="desc">{TAGS_RESULT_ROW_PATH} {PHP.L.Tags}: {TAGS_RESULT_ROW_TAGS}</span>
				</td>
			</tr>
			<!-- END: TAGS_RESULT_ROW -->
		</table>
		<!-- BEGIN: TAGS_RESULT_NONE -->
		<div class="error">{PHP.L.Noitemsfound}</div>
		<!-- END: TAGS_RESULT_NONE -->
		<!-- END: TAGS_RESULT -->
		</div>
		<div class="pagnav">{TAGS_PAGEPREV} {TAGS_PAGNAV} {TAGS_PAGENEXT}</div>
	</div>
<!-- END: MAIN -->