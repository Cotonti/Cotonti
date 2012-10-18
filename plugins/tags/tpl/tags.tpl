<!-- BEGIN: MAIN -->

		<div class="col3-2 first">
			<div class="block">
				<h2 class="tags">{PHP.L.tags_Search_tags}</h2>
				<form action="{TAGS_ACTION}" method="post">
					<input type="text" name="t" value="{TAGS_QUERY}" />
					<input type="submit" value="&raquo;&raquo;" />
					<select name="order">
						<option value="">{PHP.L.tags_Orderby}</option>
						<option value="">--</option>
						{TAGS_ORDER}
					</select>
				</form>
			</div>
			<!-- BEGIN: TAGS_CLOUD -->
			<div class="block">
				<h2 class="tags">{PHP.L.tags_All}</h2>
				{TAGS_CLOUD_BODY}
			</div>
			<!-- END: TAGS_CLOUD -->
			<!-- BEGIN: TAGS_RESULT -->
			<div class="block">
				<h2 class="search">{TAGS_RESULT_TITLE}</h2>
				<ol>
					<!-- BEGIN: TAGS_RESULT_ROW -->
					<li class="marginbottom10">
						<span class="strong"><a href="{TAGS_RESULT_ROW_URL}">{TAGS_RESULT_ROW_TITLE}</a></span><br />
						<span class="small">{PHP.L.Sections}: {TAGS_RESULT_ROW_PATH}<br />
						{PHP.L.Tags}: {TAGS_RESULT_ROW_TAGS}</span>
						<!-- IF {TAGS_RESULT_ROW_TEXT_CUT} -->
						<p>{TAGS_RESULT_ROW_TEXT_CUT}</p>
						<!-- ENDIF -->
					</li>
					<!-- END: TAGS_RESULT_ROW -->
				</ol>
				<!-- BEGIN: TAGS_RESULT_NONE -->
				<div class="error">
					{PHP.L.Noitemsfound}
				</div>
				<!-- END: TAGS_RESULT_NONE -->
			</div>
			<!-- END: TAGS_RESULT -->
			<!-- IF {TAGS_PAGNAV} --><p class="paging">{TAGS_PAGEPREV}{TAGS_PAGNAV}{TAGS_PAGENEXT}</p><!-- ENDIF -->
		</div>

		<div class="col3-1">
			<div class="block">
				<h2 class="info">{PHP.L.Tags}</h2>
				{TAGS_HINT}
			</div>
		</div>

<!-- END: MAIN -->