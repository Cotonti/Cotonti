<!-- BEGIN: FORUMS -->
			<ul>
				<li><a href="{ADMIN_FORUMS_CONF_URL}">{PHP.L.Configuration} : <img src="images/admin/config.gif" alt="" /></a></li>
<!-- IF {PHP.lincif_conf} -->
				<li><a href="{ADMIN_FORUMS_CONF_STRUCTURE_URL}">{PHP.L.adm_forum_structure}</a></li>
<!-- ELSE -->
				<li>{PHP.L.adm_forum_structure}</li>
<!-- ENDIF -->
			</ul>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">{ADMIN_FORUMS_ADMINWARNINGS}</div>
<!-- ENDIF -->
<!-- BEGIN: EDIT -->
			<form id="updatesection" action="{ADMIN_FORUMS_EDIT_FORM_URL}" method="post">
				<table class="cells">
				<tr>
					<td>{PHP.L.Section} :</td>
					<td>{ADMIN_FORUMS_EDIT_FS_ID}</td>
				</tr>
<!-- BEGIN: EDIT_FORUMS_MASTER -->
				<tr>
					<td>{PHP.L.adm_forums_master} :</td>
					<td>
						<select name="rmaster">
							<option value="0">--</option>
<!-- BEGIN: EDIT_FORUMS_MASTER_ROW -->
<!-- IF {PHP.ifmaster} -->
							<option value="{PHP.rowa.fs_id}" selected="selected">{ADMIN_FORUMS_EDIT_FORUMS_MASTER_ROW_CFS}</option>
<!-- ELSE -->
							<option value="{PHP.rowa.fs_id}">{ADMIN_FORUMS_EDIT_FORUMS_MASTER_ROW_CFS}</option>
<!-- ENDIF -->
<!-- END: EDIT_FORUMS_MASTER_ROW -->
						</select>
					</td>
				</tr>
<!-- END: EDIT_FORUMS_MASTER -->
				<tr>
					<td>{PHP.L.Category} :</td>
					<td>{ADMIN_FORUMS_EDIT_SELECTBOX_FORUMCAT}</td>
				</tr>
				<tr>
					<td>{PHP.L.Title} :</td>
					<td><input type="text" class="text" name="rtitle" value="{ADMIN_FORUMS_EDIT_FS_TITLE}" size="56" maxlength="128" /></td>
				</tr>
				<tr>
					<td>{PHP.L.Description} :</td>
					<td><input type="text" class="text" name="rdesc" value="{ADMIN_FORUMS_EDIT_FS_DESC}" size="56" maxlength="255" /></td></tr>
				<tr>
					<td>{PHP.L.Icon} :</td>
					<td> <input type="text" class="text" name="ricon" value="{ADMIN_FORUMS_EDIT_FS_ICON}" size="40" maxlength="255" /></td>
				</tr>
				<tr>
					<td>{PHP.L.adm_diplaysignatures} :</td>
					<td>
<!-- IF {PHP.fs_allowusertext} -->
						<input type="radio" class="radio" name="rallowusertext" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowusertext" value="0" />{PHP.L.No}
<!-- ELSE -->
						<input type="radio" class="radio" name="rallowusertext" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowusertext" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_enablebbcodes} :</td>
					<td>
<!-- IF {PHP.fs_allowbbcodes} -->
						<input type="radio" class="radio" name="rallowbbcodes" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowbbcodes" value="0" />{PHP.L.No}
<!-- ELSE -->
						<input type="radio" class="radio" name="rallowbbcodes" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowbbcodes" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_enablesmilies} :</td>
					<td>
<!-- IF {PHP.fs_allowsmilies} -->
						<input type="radio" class="radio" name="rallowsmilies" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowsmilies" value="0" />{PHP.L.No}
<!-- ELSE -->
						<input type="radio" class="radio" name="rallowsmilies" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowsmilies" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_enableprvtopics} :</td>
					<td>
<!-- IF {PHP.fs_allowprvtopics} -->
						<input type="radio" class="radio" name="rallowprvtopics" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowprvtopics" value="0" />{PHP.L.No}
<!-- ELSE -->
						<input type="radio" class="radio" name="rallowprvtopics" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowprvtopics" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_enableviewers} :</td>
					<td>
<!-- IF {PHP.fs_allowviewers} -->
						<input type="radio" class="radio" name="rallowviewers" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowviewers" value="0" />{PHP.L.No}
<!-- ELSE -->
						<input type="radio" class="radio" name="rallowprvtopics" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowprvtopics" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_enablepolls} :</td>
					<td>
<!-- IF {PHP.fs_allowpolls} -->
						<input type="radio" class="radio" name="rallowpolls" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowpolls" value="0" />{PHP.L.No}
<!-- ELSE -->
						<input type="radio" class="radio" name="rallowpolls" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowpolls" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_countposts} :</td>
					<td>
<!-- IF {PHP.fs_countposts} -->
						<input type="radio" class="radio" name="rcountposts" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rcountposts" value="0" />{PHP.L.No}
<!-- ELSE -->
						<input type="radio" class="radio" name="rcountposts" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rcountposts" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.Locked} :</td>
					<td>
<!-- IF {PHP.fs_state} -->
						<input type="radio" class="radio" name="rstate" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rstate" value="0" />{PHP.L.No}
<!-- ELSE -->
						<input type="radio" class="radio" name="rstate" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rstate" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_autoprune} :</td>
					<td><input type="text" class="text" name="rautoprune" value="{ADMIN_FORUMS_EDIT_FS_AUTOPRUNE}" size="3" maxlength="7" /></td>
				</tr>
				<tr>
					<td>{PHP.L.adm_postcounters} :</td>
					<td><a href="{ADMIN_FORUMS_EDIT_RESYNC_URL}">{PHP.L.Resync}</a></td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
				</table>
			</form>
<!-- END: EDIT -->
<!-- BEGIN: DEFULT -->
			<h4>{PHP.L.editdeleteentries} :</h4>
			<form name="updateorder" id="updateorder" action="{ADMIN_FORUMS_DEFULT_FORM_UPDATEORDER_URL}" method="post"{ADMIN_FORUMS_DEFULT_FORM_UPDATEORDER_URL_AJAX}>
				<table class="cells">
				<tr>
					<td class="coltop">{PHP.L.Delete}</td>
					<td class="coltop">{PHP.L.Section} {PHP.L.adm_clicktoedit}</td>
					<td class="coltop">{PHP.L.Order}</td>
					<td class="coltop">{PHP.L.adm_enableprvtopics}</td>
					<td class="coltop" style="width:48px;">{PHP.L.Topics}</td>
					<td class="coltop" style="width:48px;">{PHP.L.Posts}</td>
					<td class="coltop" style="width:48px;">{PHP.L.Views}</td>
					<td class="coltop" style="width:80px;">{PHP.L.Rights}</td>
					<td class="coltop" style="width:64px;">{PHP.L.Open}</td>
				</tr>
<!-- BEGIN: ROW -->
<!-- IF {PHP.show_fn} -->
				<tr>
					<td>&nbsp;</td>
					<td colspan="8"><strong><a href="{ADMIN_FORUMS_DEFULT_ROW_FN_URL}">{ADMIN_FORUMS_DEFULT_ROW_FN_TITLE} ({ADMIN_FORUMS_DEFULT_ROW_FN_PATH})</a></strong></td>
				</tr>
<!-- ENDIF -->
				<tr>
					<td>&nbsp;
<!-- IF {PHP.usr.isadmin} -->
						[<a href="{ADMIN_FORUMS_DEFULT_ROW_DELETE_URL}"{ADMIN_FORUMS_DEFULT_ROW_DELETE_URL_AJAX}>x</a>]
<!-- ENDIF -->
					</td>
					<td><a href="{ADMIN_FORUMS_DEFULT_ROW_FS_EDIT_URL}">{ADMIN_FORUMS_DEFULT_ROW_FS_TITLE}</a></td>
					<td style="text-align:center;"><a href="{ADMIN_FORUMS_DEFULT_ROW_FS_ORDER_UP_URL}"{ADMIN_FORUMS_DEFULT_ROW_FS_ORDER_UP_URL_AJAX}>{PHP.sed_img_up}</a> <a href="{ADMIN_FORUMS_DEFULT_ROW_FS_ORDER_DOWN_URL}"{ADMIN_FORUMS_DEFULT_ROW_FS_ORDER_DOWN_URL_AJAX}>{PHP.sed_img_down}</a></td>
					<td style="text-align:center;">{ADMIN_FORUMS_DEFULT_ROW_FS_ALLOWPRVTOPICS}</td>
					<td style="text-align:right;">{ADMIN_FORUMS_DEFULT_ROW_FS_TOPICCOUNT}</td>
					<td style="text-align:right;">{ADMIN_FORUMS_DEFULT_ROW_FS_POSTCOUNT}</td>
					<td style="text-align:right;">{ADMIN_FORUMS_DEFULT_ROW_FS_VIEWCOUNT}</td>
					<td style="text-align:center;"><a href="{ADMIN_FORUMS_DEFULT_ROW_FS_RIGHTS_URL}"><img src="images/admin/rights2.gif" alt="" /></a></td>
					<td style="text-align:center;"><a href="{ADMIN_FORUMS_DEFULT_ROW_FS_TOPICS_URL}"><img src="images/admin/jumpto.gif" alt="" /></a></td>
				</tr>
<!-- BEGIN: FCACHE -->
				<tr>
					<td>&nbsp;
<!-- IF {PHP.usr.isadmin} -->
						[<a href="{ADMIN_FORUMS_DEFULT_ROW_DELETE_URL}"{ADMIN_FORUMS_DEFULT_ROW_DELETE_URL_AJAX}>x</a>]
<!-- ENDIF -->
					</td>
					<td><a href="{ADMIN_FORUMS_DEFULT_ROW_FS_EDIT_URL}">{ADMIN_FORUMS_DEFULT_ROW_FS_TITLE}</a></td>
					<td style="text-align:center;"><a href="{ADMIN_FORUMS_DEFULT_ROW_FS_ORDER_UP_URL}"{ADMIN_FORUMS_DEFULT_ROW_FS_ORDER_UP_URL_AJAX}>{PHP.sed_img_up}</a> <a href="{ADMIN_FORUMS_DEFULT_ROW_FS_ORDER_DOWN_URL}"{ADMIN_FORUMS_DEFULT_ROW_FS_ORDER_DOWN_URL_AJAX}>{PHP.sed_img_down}</a></td>
					<td style="text-align:center;">{ADMIN_FORUMS_DEFULT_ROW_FS_ALLOWPRVTOPICS}</td>
					<td style="text-align:right;">{ADMIN_FORUMS_DEFULT_ROW_FS_TOPICCOUNT}</td>
					<td style="text-align:right;">{ADMIN_FORUMS_DEFULT_ROW_FS_POSTCOUNT}</td>
					<td style="text-align:right;">{ADMIN_FORUMS_DEFULT_ROW_FS_VIEWCOUNT}</td>
					<td style="text-align:center;"><a href="{ADMIN_FORUMS_DEFULT_ROW_FS_RIGHTS_URL}"><img src="images/admin/rights2.gif" alt="" /></a></td>
					<td style="text-align:center;"><a href="{ADMIN_FORUMS_DEFULT_ROW_FS_TOPICS_URL}"><img src="images/admin/jumpto.gif" alt="" /></a></td>
				</tr>
<!-- END: FCACHE -->
<!-- END: ROW -->
				<!--//<tr>
					<td colspan="9"><div class="pagnav">{ADMIN_FORUMS_PAGINATION_PREV} {ADMIN_FORUMS_PAGNAV} {ADMIN_FORUMS_PAGINATION_NEXT}</div></td>
				</tr>//-->
				<tr>
					<td colspan="9"><!--//{PHP.L.Total} : {ADMIN_FORUMS_TOTALITEMS}, //-->{PHP.L.adm_polls_on_page}: {ADMIN_FORUMS_COUNTER_ROW}</td>
				</tr>
				<!--//<tr>
					<td colspan="9"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>//-->
				</table>
			</form>
			<h4>{PHP.L.addnewentry} :</h4>
			<form name="addsection" id="addsection" action="{ADMIN_FORUMS_DEFULT_FORM_ADD_URL}" method="post"{ADMIN_FORUMS_DEFULT_FORM_ADD_URL_AJAX}>
				<table class="cells">
				<tr>
					<td>{PHP.L.Category} :</td>
					<td>{ADMIN_FORUMS_DEFULT_FORM_ADD_SELECTBOX_FORUMCAT}</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_forums_master} :</td>
					<td>
						<select name="nmaster">
							<option value="0">--</option>
<!-- BEGIN: FORMADDSELECT -->
							<option value="{ADMIN_FORUMS_DEFULT_FORM_ADD_OPTION_FS_ID}">{ADMIN_FORUMS_DEFULT_FORM_ADD_OPTION_CFS}</option>
<!-- END: FORMADDSELECT -->
						</select>
					</td>
				</tr>
				<tr>
					<td>{PHP.L.Title} :</td>
					<td><input type="text" class="text" name="ntitle" value="" size="64" maxlength="128" /> {PHP.L.adm_required}</td>
				</tr>
				<tr>
					<td>{PHP.L.Description} :</td>
					<td><input type="text" class="text" name="ndesc" value="" size="64" maxlength="255" /></td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" class="submit" value="{PHP.L.Add}" /></td>
				</tr>
				</table>
			</form>
<!-- END: DEFULT -->
<!-- END: FORUMS -->