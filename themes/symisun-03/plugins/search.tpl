<!-- BEGIN: MAIN -->

<div id="content">  
	<div class="padding20 whitee">    
		<h1>{PHP.L.Search}</h1>	
		<div class="breadcrumb">{PHP.themelang.list.bread}: <a href="{PHP|cot_url('index')}">{PHP.L.Home}</a> {PHP.L.Search}</div>    
		<p><em>{PHP.themelang.search.Generalhint}</em></p>    &nbsp;	
    <form id='search' action='{PLUGIN_SEARCH_ACTION}' method='post'>		
		<input type='hidden' name='a' value='search' />		
			<div id="adminmenu">
				<ul>
					<li>
						<a href='{PHP|cot_url('plug','e=search')}' 				
						<!-- IF !{PHP.tab} -->
						class='current'				
						<!-- ENDIF -->				
						>{PHP.L.plu_tabs_all}
						</a>
					</li>				
					<li>
						<a href='{PHP|cot_url('plug','e=search&amp;tab=frm')}'
						<!-- IF {PHP.tab} == "frm" -->
						class='current'
						<!-- ENDIF -->
						>{PHP.L.Forums}
						</a>
					</li>
					<li>
						<a href='{PHP|cot_url('plug','e=search&amp;tab=pag')}'
						<!-- IF {PHP.tab} == "pag" -->
						class='current'
						<!-- ENDIF -->
						>{PHP.L.Pages}
						</a>
					</li>
				</ul>			
				<div class="clear"></div>		
			</div>		
			<div class="padding10" style="border:1px solid #ccc; background-color:#f9f9f9">			
				<table class="cells">
					<tr>
						<td>
							<div style='padding:15px 0 6px 15px;'>
								{PHP.L.plu_search_req}: {PLUGIN_SEARCH_TEXT} 
								<input type="submit" value="{PHP.L.plu_search_key}" />
								<div style='padding-left:55px' class='desc'>{PHP.L.plu_search_example}</div>
							</div>
						</td>
					</tr>
				</table>
			<!-- BEGIN: EASY_OPTIONS -->			
				<table class="flat">
					<tr>
						<td style='width:50%'> {PHP.L.plu_pag_set_sec}:
							<div>{PLUGIN_PAGE_SEC_LIST}</div>
							<div class='desc'>{PHP.L.plu_ctrl_list}</div>
						</td>
						<td style='padding-left:25px'>
							<div style='padding:10px 0'>{PHP.L.plu_other_opt}:</div>
							<div>{PLUGIN_PAGE_SEARCH_NAMES}</div>
							<div style='margin:5px 0'>{PLUGIN_PAGE_SEARCH_DESC}</div>
							<div>{PLUGIN_PAGE_SEARCH_TEXT}</div>
						</td>
					</tr>
				</table>
				<div style='margin:10px 0'>
					<table>
						<tr>
							<td style='width:50%'>
								{PHP.L.plu_frm_set_sec}:
								<div>{PLUGIN_FORUM_SEC_LIST}</div>
								<div class='desc'>{PHP.L.plu_ctrl_list}</div>
							</td>
							<td style='padding-left:25px'>
								<div style='padding:10px 0'>{PHP.L.plu_other_opt}:</div>
								<div>{PLUGIN_FORUM_SEARCH_NAMES}</div>
								<div style='margin:5px 0'>{PLUGIN_FORUM_SEARCH_POST}</div>
							</td>
						</tr>
					</table>
				</div> 
			<!-- END: EASY_OPTIONS -->
			
			<!-- BEGIN: PAGES_OPTIONS -->
			<div style='margin:20px 0'>
				<table>
					<tr>
						<td style='width:50%'>
							{PHP.L.plu_pag_set_sec}:
							<div>{PLUGIN_PAGE_SEC_LIST}</div>
							<div class='desc'>{PHP.L.plu_ctrl_list}</div>
							<div style='padding:15px 0 0 0'>{PHP.L.plu_res_sort}:</div>
							<div><span>{PLUGIN_PAGE_RES_SORT}</span><span style='margin-left:12px'>{PLUGIN_PAGE_RES_DESC}</span><span style='margin-left:12px'>{PLUGIN_PAGE_RES_ASC}</span></div>
						</td>
						<td style='padding-left:25px'><div style='padding:10px 0'>{PHP.L.plu_other_opt}:</div>
							<div>{PLUGIN_PAGE_SEARCH_NAMES}</div>
							<div style='margin:5px 0'>{PLUGIN_PAGE_SEARCH_DESC}</div>
							<div style='margin:5px 0'>{PLUGIN_PAGE_SEARCH_TEXT}</div>
							<div>{PLUGIN_PAGE_SEARCH_FILE}</div>
							<div style='padding:15px 0 0 0'>{PHP.L.plu_other_date}:</div>
							{PLUGIN_PAGE_SEARCH_DATE}
						</td>
					</tr>
				</table>
			</div>
			<!-- END: PAGES_OPTIONS -->
							
			<!-- BEGIN: FORUMS_OPTIONS -->
			<div style='margin:20px 0'>	
				<table>
					<tr>
						<td style='width:50%'> 
							{PHP.L.plu_frm_set_sec}:
							<div>{PLUGIN_FORUM_SEC_LIST}</div>
							<div class='desc'>{PHP.L.plu_ctrl_list}</div>
							<div style='padding:15px 0 0 0'>{PHP.L.plu_res_sort}:</div>
							<div><span>{PLUGIN_FORUM_RES_SORT}</span><span style='margin-left:12px'>{PLUGIN_FORUM_RES_DESC}</span><span style='margin-left:12px'>{PLUGIN_FORUM_RES_ASC}</span></div>
						</td>
						<td style='padding-left:25px'><div style='padding-bottom:10px'>{PHP.L.plu_other_opt}:</div>
							<div>{PLUGIN_FORUM_SEARCH_NAMES}</div>
							<div style='margin:5px 0'>{PLUGIN_FORUM_SEARCH_POST}</div>
							<div>{PLUGIN_FORUM_SEARCH_ANSW}</div>
							<div style='padding:15px 0 0 0'>{PHP.L.plu_other_date}:</div>
							<div>{PLUGIN_FORUM_SEARCH_DATE}</div>
						</td>
					</tr>
				</table>
			</div>
			<!-- END: FORUMS_OPTIONS -->
	</form>		
</div>

  <!-- BEGIN: ERROR -->
	{FILE "./themes/symisun-03/warnings.tpl"}
  <!-- END: ERROR -->    	
  
  <!-- BEGIN: RESULTS -->	
  <!-- BEGIN: PAGES -->	
	<fieldset>
		<legend>{PHP.L.plu_result}: {PHP.L.plu_tabs_pag}</legend>
			<table class="cells">
				<!-- BEGIN: ITEM -->
				<tr>
					<td colspan="2" class="{PLUGIN_PR_ODDEVEN}">{PLUGIN_PR_TITLE}</td>
				</tr>
				<tr>	
					<td colspan="2" class="{PLUGIN_PR_ODDEVEN}">{PLUGIN_PR_TEXT}</td>	
				</tr>
				<tr>
					<td class="{PLUGIN_PR_ODDEVEN}"><div class="desc">{PHP.L.plu_last_date}: {PLUGIN_PR_TIME}</div></td>
					<td class="{PLUGIN_PR_ODDEVEN}"><div class="desc">{PHP.L.plu_section}: {PLUGIN_PR_CATEGORY}</div></td>
				</tr>	
			<!-- END: ITEM -->
			</table>
	</fieldset>
  <!-- END: PAGES -->
  <!-- BEGIN: FORUMS -->
	<fieldset>
		<legend>{PHP.L.plu_result}: {PHP.L.plu_tabs_frm}</legend>
			<table class="cells">
				<!-- BEGIN: ITEM -->
				<tr>
					<td colspan="2" class="{PLUGIN_FR_ODDEVEN}">{PLUGIN_FR_TITLE}</td>
				</tr>	
				<tr>
					<td colspan="2" class="{PLUGIN_FR_ODDEVEN}">{PLUGIN_FR_TEXT}</td>
				</tr>
				<tr>
					<td class="{PLUGIN_FR_ODDEVEN}"><div class="desc">{PHP.L.plu_last_date}: {PLUGIN_FR_TIME}</div></td>
					<td class="{PLUGIN_FR_ODDEVEN}"><div class="desc">{PHP.L.plu_section}: {PLUGIN_FR_CATEGORY}</div></td>
				</tr>
				<!-- END: ITEM -->
			</table>	
	</fieldset>	
<!-- END: FORUMS -->
	<div class="pagnav">{PLUGIN_PAGEPREV} {PLUGIN_PAGENAV} {PLUGIN_PAGENEXT}</div>
<!-- END: RESULTS -->
</div>
</div>
<br class="clear" />
<!-- END: MAIN -->