<!-- BEGIN: MAIN -->

		<div id="center" class="column">
			<div class="block">
				<h2 class="tags">Поиск по тегу</h2>
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
				<h2 class="tags">Все теги</h2>
				{TAGS_CLOUD_BODY}
			</div>
			<!-- END: TAGS_CLOUD -->
			<!-- BEGIN: TAGS_RESULT -->
			<div class="block">
				<h2 class="search">{TAGS_RESULT_TITLE}</h2>
				<ol>
					<!-- BEGIN: TAGS_RESULT_ROW -->
					<li style="margin-bottom:10px;">
						<span class="strong"><a href="{TAGS_RESULT_ROW_URL}">{TAGS_RESULT_ROW_TITLE}</a></span><br />
						<span class="small">{PHP.L.Sections}: {TAGS_RESULT_ROW_PATH}<br />{PHP.L.Tags}: {TAGS_RESULT_ROW_TAGS}</span>
					</li>
					<!-- END: TAGS_RESULT_ROW -->
				</ol>
			</div>
			<!-- END: TAGS_RESULT -->
			<p class="paging">{TAGS_PAGEPREV}{TAGS_PAGNAV}{TAGS_PAGENEXT}</p>
		</div>
		<div id="side" class="column">
			<div class="block">
				<h2 class="info">{PHP.L.Tags}</h2>
				<p>{TAGS_HINT}</p>
			</div>
		</div>

<!-- END: MAIN -->