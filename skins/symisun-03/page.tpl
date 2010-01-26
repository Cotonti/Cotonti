<!-- BEGIN: MAIN -->

	<div id="content">
    	<div class="padding20">
            <div id="left">
            
            	<h1>{PAGE_SHORTTITLE}</h1>
                
				<p class="details">{PHP.skinlang.index.by} {PAGE_OWNER}, {PAGE_DATE} in {PAGE_CATPATH}
                <!-- IF {PHP.usr.isadmin} -->
                | {PAGE_ADMIN_COUNT} {PHP.skinlang.page.views}
                <!-- ENDIF -->
                </p>
                
                {PAGE_TEXT}
                
                <br class="clear" /> 
                &nbsp;
            	<div class="breadcrumb">{PHP.skinlang.list.bread}: <a href="index.php">{PHP.L.Home}</a>{PAGE_CATPATH}</div>
                
			</div>
			<div id="right">
                
                <h3><a href="page.php?id={PHP.pag.page_id}#com">{PHP.L.Comments}: {PHP.pag.page_comcount}</a></h3>
                
                <div class="h3"><div class="colright" style="margin-top:5px; display:inline">{PAGE_RATINGS}</div>{PHP.L.Ratings}{PAGE_RATINGS_DISPLAY}</div>
                
                <h3><a href="{PAGE_COMMENTS_RSS}">Subscribe via RSS</a></h3>
                
                <!-- IF {PHP.tag_i} > 0 -->
                <h3>{PHP.L.Tags}</h3>
                <div class="box padding15 admin">
                <!-- ENDIF -->
                	<!-- BEGIN: PAGE_TAGS_ROW -->
                    <!-- IF {PHP.tag_i} > 0 -->, <!-- ENDIF --><a href="{PAGE_TAGS_ROW_URL}">{PAGE_TAGS_ROW_TAG}</a> 
					<!-- END: PAGE_TAGS_ROW -->
                <!-- IF {PHP.tag_i} > 0 -->
                </div>
                <!-- ENDIF -->
                
                <!-- BEGIN: PAGE_ADMIN -->
                <h3 class="adm">{PHP.skinlang.page.admin}</h3>
                <div class="boxa padding15 admin">
                    {PAGE_ADMIN_EDIT}<br />
                    {PAGE_ADMIN_UNVALIDATE}
                </div>
                <!-- END: PAGE_ADMIN -->
                
                <!-- BEGIN: PAGE_FILE -->
                	<!-- BEGIN: MEMBERSONLY -->
					{PAGE_FILE_ICON} {PAGE_SHORTTITLE}<br/>
					<!-- END: MEMBERSONLY -->
                    <!-- BEGIN: DOWNLOAD -->
                    {PAGE_FILE_ICON}<a href="{PAGE_FILE_URL}">{PHP.L.Download}: {PAGE_SHORTTITLE}</a><br/>
                    <!-- END: DOWNLOAD -->
                    {PHP.skinlang.page.Filesize}: {PAGE_FILE_SIZE}{PHP.L.kb}, {PHP.skinlang.page.downloaded} {PAGE_FILE_COUNT} {PHP.skinlang.page.times}
                <!-- END: PAGE_FILE -->
                
                <!-- BEGIN: PAGE_MULTI -->
                <div class="box padding15">
                
                <div class="paging">{PAGE_MULTI_TABNAV}</div>
                <div class="block">
                	<h5>{PHP.skinlang.page.Summary}</h5>
                	{PAGE_MULTI_TABTITLES}
                </div>
                
                </div>
                <!-- END: PAGE_MULTI -->
                &nbsp;
                
            </div>
            <br class="clear" />
            <hr />
            {PAGE_COMMENTS_DISPLAY}
		</div>
	</div>
	<br class="clear" />

<!-- END: MAIN -->