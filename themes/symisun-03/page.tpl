<!-- BEGIN: MAIN -->

	<div id="content">
    	<div class="padding20">
            <div id="left">
            	<h1>{PAGE_SHORTTITLE}</h1>
				<p class="details">{PHP.themelang.index.by} {PAGE_OWNER}, {PAGE_DATE} in {PAGE_CATPATH}
                <!-- IF {PHP.usr.isadmin} -->
                | {PAGE_ADMIN_COUNT} {PHP.themelang.page.views}
                <!-- ENDIF -->
                </p>
                {PAGE_TEXT}
                <br class="clear" /> 
                &nbsp;
            	<div class="breadcrumb">{PHP.themelang.list.bread}: <a href="{PHP|cot_url('index')}">{PHP.L.Home}</a>{PAGE_CATPATH} {PAGE_SHORTTITLE}</div>
			</div>

			<div id="right">
                <!-- BEGIN: PAGE_FILE -->
					<h3>{PHP.L.Download}</h3>
					<div class="box padding15 admin">

                	<!-- BEGIN: MEMBERSONLY -->
					{PAGE_FILE_ICON} {PAGE_SHORTTITLE}<br/>
					<!-- END: MEMBERSONLY -->

                    <!-- BEGIN: DOWNLOAD -->
                    {PAGE_FILE_ICON} <a href="{PAGE_FILE_URL}"> {PAGE_SHORTTITLE}</a><br/>
                    <!-- END: DOWNLOAD -->

                    {PHP.L.Filesize}: {PAGE_FILE_SIZE}{PHP.L.kb}, {PHP.L.Downloaded}: {PAGE_FILE_COUNT}
					</div>
                <!-- END: PAGE_FILE -->
              
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
		
                <h3><a href="{PHP.pag.page_id|cot_url('page','id=$this')}#com">{PHP.L.comments_comments}: {PAGE_COMMENTS_COUNT}</a></h3>

                <h3><a href="{PAGE_COMMENTS_RSS}">Subscribe via RSS</a></h3>
				
                <!-- BEGIN: PAGE_ADMIN -->
                <h3 class="adm">{PHP.L.Administration}</h3>
                <div class="boxa padding15 admin">
                    {PAGE_ADMIN_EDIT}<br />
                    {PAGE_ADMIN_UNVALIDATE}
                </div>
                <!-- END: PAGE_ADMIN -->
                
                <!-- BEGIN: PAGE_MULTI -->
                <div class="box padding15">
                
                <div class="paging">{PAGE_MULTI_TABNAV}</div>
                <div class="block">
                	<h5>{PHP.L.Summary}</h5>
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