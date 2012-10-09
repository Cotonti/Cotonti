<!-- BEGIN: MAIN -->

<div id="content">
    <div class="padding20 whitee">
        <h1>{PHP.L.Tags}</h1>
        <form action="{TAGS_ACTION}" method="post">
            <input type="text" name="t" value="{TAGS_QUERY}" />
            <input type="submit" class="submit" value="{PHP.L.Filter}" />
            <select name="order">
                <option value="">{PHP.L.tags_Orderby}</option>
                <option value="">--</option>
			{TAGS_ORDER}
            </select>
            <br />
            <em>{TAGS_HINT}</em>
        </form>
        &nbsp;
        <!-- BEGIN: TAGS_CLOUD -->
		{TAGS_CLOUD_BODY}
                <!-- END: TAGS_CLOUD -->
                <!-- BEGIN: TAGS_RESULT -->
                <h3>{TAGS_RESULT_TITLE}</h3>
                <div class="pagnav">{TAGS_PAGEPREV} {TAGS_PAGNAV} {TAGS_PAGENEXT}</div>
                <ul>
                    <!-- BEGIN: TAGS_RESULT_ROW -->
                    <li>
                        <strong class="admin"><a href="{TAGS_RESULT_ROW_URL}">{TAGS_RESULT_ROW_TITLE}</a></strong><br />
                        <span class="hint">{TAGS_RESULT_ROW_PATH} {PHP.L.Tags}: {TAGS_RESULT_ROW_TAGS}</span>
                    </li>
                    <!-- END: TAGS_RESULT_ROW -->
					<!-- BEGIN: TAGS_RESULT_NONE -->
					<li>
						<div class="error">
							{PHP.L.Noitemsfound}
						</div>
					</li>
					<!-- END: TAGS_RESULT_NONE -->
                </ul>
                <!-- END: TAGS_RESULT -->
                <div class="pagnav">{TAGS_PAGEPREV} {TAGS_PAGNAV} {TAGS_PAGENEXT}</div>
    </div>

</div>
<br class="clear" />
<!-- END: MAIN -->