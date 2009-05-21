<!-- BEGIN: MAIN -->
<div id="main">
<form action="{TAGS_ACTION}" method="post">
<input type="text" name="t" value="{TAGS_QUERY}" /> <input type="submit" value="&gt;&gt;" /><br />
<em>{TAGS_HINT}</em>
</form>
<br />

<!-- BEGIN: TAGS_CLOUD -->
<div class="block">
{TAGS_CLOUD_BODY}
</div>
<!-- END: TAGS_CLOUD -->

<!-- BEGIN: TAGS_RESULT -->

<h3>{TAGS_RESULT_TITLE}</h3>

<!-- BEGIN: TAGS_RESULT_ROW -->
<div>
<h4><a href="{TAGS_RESULT_ROW_URL}">{TAGS_RESULT_ROW_TITLE}</a></h4>
{TAGS_RESULT_ROW_PATH} {PHP.L.Tags}: {TAGS_RESULT_ROW_TAGS}
</div>
<!-- END: TAGS_RESULT_ROW -->

<!-- END: TAGS_RESULT -->

<div class="pagnav">{TAGS_PAGEPREV} {TAGS_PAGNAV} {TAGS_PAGENEXT}</div>

</div>
<!-- END: MAIN -->