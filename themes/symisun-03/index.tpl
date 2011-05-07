<!-- BEGIN: MAIN -->

	<div id="content">
    	<div class="padding20">
            <div id="left">
            	{INDEX_NEWS}
            </div>
            <div id="right">
            	
                <h3><a href="polls.php?id=viewall">{PHP.L.Polls}</a></h3>
                <div class="box padding15" id="poll">
                	{INDEX_POLLS}
                </div>
                
                <!-- IF {PHP.sys.whosonline_reg_count} > 0 -->
                <h3>{PHP.L.Members} {PHP.L.Online}</h3>
                <div class="box padding15" id="members">
                	{PHP.out.whosonline_reg_list}
                </div>
                &nbsp;
                <!-- ENDIF -->
            </div>
        </div>
    </div>
    <br class="clear" />&nbsp;
    
    <!-- IF {INDEX_TAG_CLOUD} != {PHP.L.tags_Tag_cloud_none} -->
    <div class="tag_cloud padding20" style="text-align:justify">
    	<h4>{INDEX_TOP_TAG_CLOUD}</h4><a href="plug.php?e=tags&amp;a=all" class="colright" style="margin-top:-20px">{PHP.L.tags_All}</a>
        <div class="padding20 indextags"><ul class="tag_cloud">{INDEX_TAG_CLOUD}</ul></div>
    </div>
    <!-- ENDIF -->

<!-- END: MAIN -->