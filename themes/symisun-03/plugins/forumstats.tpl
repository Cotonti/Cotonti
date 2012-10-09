<!-- BEGIN: MAIN -->

<div id="content">  
	<div class="padding20 whitee">    
		<h1>{PHP.L.forumstats_title}</h1>

                <fieldset>
		<legend>{PHP.L.forumstats_title}</legend>
                    <div>
			<label>{PHP.L.forumstats_sections}</label>
			{FORUMSTATS_TOTALSECTIONS}
                    </div>
                    <div>
			<label>{PHP.L.forumstats_topics}</label>
			{FORUMSTATS_TOTALTOPICS}
                    </div>
                    <div>
			<label>{PHP.L.forumstats_posts}</label>
			{FORUMSTATS_TOTALPOSTS}
                    </div>
                    <div>
			<label>{PHP.L.forumstats_views}</label>
			{FORUMSTATS_TOTALVIEWS}
                    </div>

               </fieldset>

              <fieldset>
		<legend>{PHP.L.forumstats_repliedtop10}</legend>
                   <!-- BEGIN: FORUMSTATS_REPLIEDTOP_USER -->
                    <div>
			<label>{FORUMSTATS_REPLIEDTOP_II}. </label>
			{FORUMSTATS_REPLIEDTOP_FORUMS} {PHP.cfg.separator} <a href="{FORUMSTATS_REPLIEDTOP_URL}">{FORUMSTATS_REPLIEDTOP_TITLE}</a> ({FORUMSTATS_REPLIEDTOP_POSTCOUNT})
                    </div>
                   <!-- END: FORUMSTATS_REPLIEDTOP_USER -->
                   <!-- BEGIN: FORUMSTATS_REPLIEDTOP_NO_USER -->
                    <div>
			<label>{FORUMSTATS_REPLIEDTOP_II}.</label>
			{FORUMSTATS_REPLIEDTOP_FORUMS} {PHP.cfg.separator} {PHP.L.forumstats_hidden} ({FORUMSTATS_REPLIEDTOP_POSTCOUNT})
                    </div>
                   <!-- END: FORUMSTATS_REPLIEDTOP_NO_USER -->
               </fieldset>

              <fieldset>
		<legend>{PHP.L.forumstats_viewedtop10}</legend>
                   <!-- BEGIN: FORUMSTATS_VIEWEDTOP_USER -->
                    <div>
			<label>{FORUMSTATS_VIEWEDTOP_II}. </label>
			{FORUMSTATS_VIEWEDTOP_FORUMS} {PHP.cfg.separator} <a href="{FORUMSTATS_VIEWEDTOP_URL}">{FORUMSTATS_VIEWEDTOP_TITLE}</a> ({FORUMSTATS_VIEWEDTOP_VIEWCOUNT})
                    </div>
                   <!-- END: FORUMSTATS_VIEWEDTOP_USER -->
                   <!-- BEGIN: FORUMSTATS_VIEWEDTOP_NO_USER -->
                    <div>
			<label>{FORUMSTATS_VIEWEDTOP_II}.</label>
			{FORUMSTATS_VIEWEDTOP_FORUMS} {PHP.cfg.separator} {PHP.L.forumstats_hidden} ({FORUMSTATS_VIEWEDTOP_VIEWCOUNT})
                    </div>
                   <!-- END: FORUMSTATS_VIEWEDTOP_NO_USER -->
               </fieldset>

              <fieldset>
		<legend>{PHP.L.forumstats_posterstop10}</legend>
                   <!-- BEGIN: POSTERSTOP -->
                    <div>
			<label>{FORUMSTATS_POSTERSTOP_II}.</label>
			{FORUMSTATS_POSTERSTOP_USER_NAME} ({FORUMSTATS_POSTERSTOP_USER_POSTCOUNT})
                    </div>
                   <!-- END: POSTERSTOP -->
               </fieldset>

                </div>
    </div>

<br class="clear" />

<!-- END: MAIN -->