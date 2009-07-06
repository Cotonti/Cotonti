<!-- BEGIN: MAIN -->

			<h2>{PLUGIN_TITLE}</h2>
			<div class='main_all'>{PHP.L.plu_subtitle_all}</div>
			<div class='main_all'>
                        <form id='search' name='search' method='post'>
                        <input type='hidden' name='a' value='search'>
                        <div style='padding:15px; background-color:#e1e2f7'>
                              <div>
                                    <table class='flat'>
                                    <tr>
                                          <td style='width:55px'>{PHP.L.plu_search_req}:</td>
                                          <td>{PLUGIN_SEARCH_TEXT}</td>
                                          <td style='width:85px'>{PLUGIN_SEARCH_KEY}</td>
                                          <td class='a_cm'><a href='plug.php?e=search'>{PHP.L.plu_tabs_all}</a> | <a href='plug.php?e=search&tab=frm'>{PHP.L.plu_tabs_frm}</a> | <a href='plug.php?e=search&tab=pag'>{PHP.L.plu_tabs_pag}</a></td>
                                    </tr>
                                    </table>
                              </div>
                              <div style='padding-left:55px' class='cells_com'>{PHP.L.plu_search_example}</div>
                        </div>

                        <!-- BEGIN: EASY_OPTIONS -->
                        <div style='margin:20px 0'>
                              <table>
                              <tr>
                                    <td style='width:50%'>
                                          <div style='padding-bottom:10px'>{PHP.L.plu_pag_set_sec}:</div>
                                          <div class='frame'>
                                                <div>{PLUGIN_PAGE_SEC_LIST}</div>
                                                <div class='cells_com'>{PHP.L.plu_ctrl_list}</div>
                                          </div>
                                    </td>
                                    <td style='padding-left:25px'>
                                          <div style='padding-bottom:10px'>{PHP.L.plu_other_opt}:</div>
                                          <div class='frame'>
                                                <div>{PLUGIN_PAGE_SEARCH_NAMES}</div>
                                                <div style='margin:5px 0'>{PLUGIN_PAGE_SEARCH_DESC}</div>
                                                <div>{PLUGIN_PAGE_SEARCH_TEXT}</div>
                                          </div>
                                    </td>
                              </tr>
                              </table>
                        </div>
                        <div style='margin:20px 0'>
                              <table>
                              <tr>
                                    <td style='width:50%'>
                                          <div style='padding-bottom:10px'>{PHP.L.plu_frm_set_sec}:</div>
                                          <div class='frame'>
                                                <div>{PLUGIN_FORUM_SEC_LIST}</div>
                                                <div class='cells_com'>{PHP.L.plu_ctrl_list}</div>
                                          </div>
                                    </td>
                                    <td style='padding-left:25px'>
                                          <div style='padding-bottom:10px'>{PHP.L.plu_other_opt}:</div>
                                          <div class='frame'>
                                                <div>{PLUGIN_FORUM_SEARCH_NAMES}</div>
                                                <div style='margin:5px 0'>{PLUGIN_FORUM_SEARCH_POST}</div>
                                          </div>
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
                                          <div style='padding-bottom:10px'>{PHP.L.plu_pag_set_sec}:</div>
                                          <div class='frame'>
                                                <div>{PLUGIN_PAGE_SEC_LIST}</div>
                                                <div class='cells_com'>{PHP.L.plu_ctrl_list}</div>
                                          </div>
                                          <div style='padding:15px 0 10px 0'>{PHP.L.plu_res_sort}:</div>
                                          <div class='frame'><span>{PLUGIN_PAGE_RES_SORT}</span><span style='margin-left:12px'>{PLUGIN_PAGE_RES_DESC}</span><span style='margin-left:12px'>{PLUGIN_PAGE_RES_ASC}</span></div>
                                    </td>
                                    <td style='padding-left:25px'>
                                          <div style='padding-bottom:10px'>{PHP.L.plu_other_opt}:</div>
                                          <div class='frame'>
                                                <div>{PLUGIN_PAGE_SEARCH_NAMES}</div>
                                                <div style='margin:5px 0'>{PLUGIN_PAGE_SEARCH_DESC}</div>
                                                <div style='margin:5px 0'>{PLUGIN_PAGE_SEARCH_TEXT}</div>
                                                <div>{PLUGIN_PAGE_SEARCH_FILE}</div>
                                          </div>
                                          <div style='padding:15px 0 10px 0'>{PHP.L.plu_other_date}:</div>
                                          <div class='frame'>{PLUGIN_PAGE_SEARCH_DATE}</div>
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
                                          <div style='padding-bottom:10px'>{PHP.L.plu_frm_set_sec}:</div>
                                          <div class='frame'>
                                                <div>{PLUGIN_FORUM_SEC_LIST}</div>
                                                <div class='cells_com'>{PHP.L.plu_ctrl_list}</div>
                                          </div>
                                          <div style='padding:15px 0 10px 0'>{PHP.L.plu_res_sort}:</div>
                                          <div class='frame'><span>{PLUGIN_FORUM_RES_SORT}</span><span style='margin-left:12px'>{PLUGIN_FORUM_RES_DESC}</span><span style='margin-left:12px'>{PLUGIN_FORUM_RES_ASC}</span></div>
                                    </td>
                                    <td style='padding-left:25px'>
                                          <div style='padding-bottom:10px'>{PHP.L.plu_other_opt}:</div>
                                          <div class='frame'>
                                                <div>{PLUGIN_FORUM_SEARCH_NAMES}</div>
                                                <div style='margin:5px 0'>{PLUGIN_FORUM_SEARCH_POST}</div>
                                                <div>{PLUGIN_FORUM_SEARCH_ANSW}</div>
                                          </div>
                                          <div style='padding:15px 0 10px 0'>{PHP.L.plu_other_date}:</div>
                                          <div class='frame'>{PLUGIN_FORUM_SEARCH_DATE}</div>
                                    </td>
                              </tr>
                              </table>
                        </div>
                        <!-- END: FORUMS_OPTIONS -->

                        </form>
                  </div>

                  <!-- BEGIN: ERROR -->
                  <div class='main_all'>
                        <div class='error' style='margin:15px 0'>{PLUGIN_ERROR}</div>
                  </div>
                  <!-- END: ERROR -->
                  
                  <!-- BEGIN: EASY_PAGES_RESULTS -->
                  <div class='main_all'>
				<h1>{PHP.L.plu_result}: {PHP.L.plu_tabs_pag}</h1>
				<div style='margin:5px 0 30px 0'>{PLUGIN_EASY_PAGE_FOUND}</div>
				<div>
                              <!-- BEGIN: ITEM -->
                              <div style='margin:30px 0'>
                                    <h3>{PLUGIN_PR_TITLE}</h3>
                                    <div style='margin-top:5px' class='mark'>{PLUGIN_PR_TEXT}</div>
                                    <div class='cells_com'>{PHP.L.plu_last_date}: {PLUGIN_PR_TIME}<span style='margin-left:20px'>{PHP.L.plu_section}: {PLUGIN_PR_CATEGORY}</span></div>
                              </div>
                              <!-- END: ITEM -->
				</div>
                  </div>
                  <!-- END: EASY_PAGES_RESULTS -->
                        
                  <!-- BEGIN: EASY_FORUMS_RESULTS -->
                  <div class='main_all'>
				<h1>{PHP.L.plu_result}: {PHP.L.plu_tabs_frm}</h1>
				<div style='margin:5px 0 30px 0'>{PLUGIN_EASY_FORUM_FOUND}</div>
				<div>
                              <!-- BEGIN: ITEM -->
                              <div style='margin:30px 0'>
                                    <h3>{PLUGIN_FR_TITLE}</h3>
                                    <div style='margin-top:5px' class='mark'>{PLUGIN_FR_TEXT}</div>
                                    <div class='cells_com'>{PHP.L.plu_last_date}: {PLUGIN_FR_TIME}<span style='margin-left:20px'>{PHP.L.plu_section}: {PLUGIN_FR_CATEGORY}</span></div>
                              </div>
                              <!-- END: ITEM -->
				</div>
                  </div>
                  <!-- END: EASY_FORUMS_RESULTS -->

                  <!-- BEGIN: PAGES_RESULTS -->
                  <div class='main_all'>
				<h1>{PHP.L.plu_result}: {PHP.L.plu_tabs_pag}</h1>
				<div style='margin:5px 0 30px 0'>{PLUGIN_PAGE_FOUND}</div>
				<div>
                              <!-- BEGIN: ITEM -->
                              <div style='margin:30px 0'>
                                    <h3>{PLUGIN_PR_TITLE}</h3>
                                    <div style='margin-top:5px' class='mark'>{PLUGIN_PR_TEXT}</div>
                                    <div class='cells_com'>{PHP.L.plu_last_date}: {PLUGIN_PR_TIME}<span style='margin-left:20px'>{PHP.L.plu_section}: {PLUGIN_PR_CATEGORY}</span></div>
                              </div>
                              <!-- END: ITEM -->
				</div>
                  </div>
                  <!-- END: PAGES_RESULTS -->
                        
                  <!-- BEGIN: FORUMS_RESULTS -->
                  <div class='main_all'>
				<h1>{PHP.L.plu_result}: {PHP.L.plu_tabs_frm}</h1>
				<div style='margin:5px 0 30px 0'>{PLUGIN_FORUM_FOUND}</div>
				<div>
                              <!-- BEGIN: ITEM -->
                              <div style='margin:30px 0'>
                                    <h3>{PLUGIN_FR_TITLE}</h3>
                                    <div style='margin-top:5px' class='mark'>{PLUGIN_FR_TEXT}</div>
                                    <div class='cells_com'>{PHP.L.plu_last_date}: {PLUGIN_FR_TIME}<span style='margin-left:20px'>{PHP.L.plu_section}: {PLUGIN_FR_CATEGORY}</span></div>
                              </div>
                              <!-- END: ITEM -->
				</div>
                  </div>
                  <!-- END: FORUMS_RESULTS -->

<!-- END: MAIN -->


