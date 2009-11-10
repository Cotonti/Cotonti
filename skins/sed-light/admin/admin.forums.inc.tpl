<!-- BEGIN: FORUMS -->
	<div id="{ADMIN_FORUMS_AJAX_OPENDIVID}">
		<h2>{PHP.L.Forums}</h2>
<!-- IF {PHP.is_adminwarnings} -->
			<div class="error">
				<h4>{PHP.L.Message}</h4>
				<p>{ADMIN_FORUMS_ADMINWARNINGS}</p>
			</div>
<!-- ENDIF -->
		<ul class="follow">
			<li><a title="{PHP.L.Configuration}" href="{ADMIN_FORUMS_CONF_URL}">{PHP.L.Configuration}</a></li>
<!-- IF {PHP.lincif_conf} -->
			<li><a href="{ADMIN_FORUMS_CONF_STRUCTURE_URL}">{PHP.L.adm_forum_structure}</a></li>
<!-- ELSE -->
			<li>{PHP.L.adm_forum_structure}</li>
<!-- ENDIF -->
		</ul>
<!-- BEGIN: EDIT -->
		<form id="updatesection" action="{ADMIN_FORUMS_EDIT_FORM_URL}" method="post">
		<table class="cells">
			<tr>
				<td style="width:30%;">{PHP.L.Section}:</td>
				<td style="width:70%;">{ADMIN_FORUMS_EDIT_FS_ID}</td>
			</tr>
<!-- BEGIN: EDIT_FORUMS_MASTER -->
			<tr>
				<td>{PHP.L.adm_forums_master}:</td>
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
				<td>{PHP.L.Category}:</td>
				<td>{ADMIN_FORUMS_EDIT_SELECTBOX_FORUMCAT}</td>
			</tr>
			<tr>
				<td>{PHP.L.Title}:</td>
				<td><input type="text" class="text" name="rtitle" value="{ADMIN_FORUMS_EDIT_FS_TITLE}" size="56" maxlength="128" /></td>
			</tr>
			<tr>
				<td>{PHP.L.Description}:</td>
				<td><input type="text" class="text" name="rdesc" value="{ADMIN_FORUMS_EDIT_FS_DESC}" size="56" maxlength="255" /></td></tr>
			<tr>
				<td>{PHP.L.Icon}:</td>
				<td> <input type="text" class="text" name="ricon" value="{ADMIN_FORUMS_EDIT_FS_ICON}" size="40" maxlength="255" /></td>
			</tr>
			<tr>
				<td>{PHP.L.adm_diplaysignatures}:</td>
				<td>
<!-- IF {PHP.fs_allowusertext} -->
					<input type="radio" class="radio" name="rallowusertext" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowusertext" value="0" />{PHP.L.No}
<!-- ELSE -->
					<input type="radio" class="radio" name="rallowusertext" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowusertext" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
				</td>
			</tr>
			<tr>
				<td>{PHP.L.adm_enablebbcodes}:</td>
				<td>
<!-- IF {PHP.fs_allowbbcodes} -->
						<input type="radio" class="radio" name="rallowbbcodes" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowbbcodes" value="0" />{PHP.L.No}
<!-- ELSE -->
						<input type="radio" class="radio" name="rallowbbcodes" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowbbcodes" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_enablesmilies}:</td>
					<td>
<!-- IF {PHP.fs_allowsmilies} -->
						<input type="radio" class="radio" name="rallowsmilies" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowsmilies" value="0" />{PHP.L.No}
<!-- ELSE -->
						<input type="radio" class="radio" name="rallowsmilies" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowsmilies" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_enableprvtopics}:</td>
					<td>
<!-- IF {PHP.fs_allowprvtopics} -->
						<input type="radio" class="radio" name="rallowprvtopics" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowprvtopics" value="0" />{PHP.L.No}
<!-- ELSE -->
						<input type="radio" class="radio" name="rallowprvtopics" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowprvtopics" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_enableviewers}:</td>
					<td>
<!-- IF {PHP.fs_allowviewers} -->
						<input type="radio" class="radio" name="rallowviewers" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowviewers" value="0" />{PHP.L.No}
<!-- ELSE -->
						<input type="radio" class="radio" name="rallowprvtopics" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowprvtopics" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_enablepolls}:</td>
					<td>
<!-- IF {PHP.fs_allowpolls} -->
						<input type="radio" class="radio" name="rallowpolls" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowpolls" value="0" />{PHP.L.No}
<!-- ELSE -->
						<input type="radio" class="radio" name="rallowpolls" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rallowpolls" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_countposts}:</td>
					<td>
<!-- IF {PHP.fs_countposts} -->
						<input type="radio" class="radio" name="rcountposts" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rcountposts" value="0" />{PHP.L.No}
<!-- ELSE -->
						<input type="radio" class="radio" name="rcountposts" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rcountposts" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.Locked}:</td>
					<td>
<!-- IF {PHP.fs_state} -->
						<input type="radio" class="radio" name="rstate" value="1" checked="checked" />{PHP.L.Yes} <input type="radio" class="radio" name="rstate" value="0" />{PHP.L.No}
<!-- ELSE -->
						<input type="radio" class="radio" name="rstate" value="1" />{PHP.L.Yes} <input type="radio" class="radio" name="rstate" value="0" checked="checked" />{PHP.L.No}
<!-- ENDIF -->
					</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_autoprune}:</td>
					<td><input type="text" class="text" name="rautoprune" value="{ADMIN_FORUMS_EDIT_FS_AUTOPRUNE}" size="3" maxlength="7" /></td>
				</tr>
				<tr>
					<td>{PHP.L.adm_postcounters}:</td>
					<td><a href="{ADMIN_FORUMS_EDIT_RESYNC_URL}">{PHP.L.Resync}</a></td>
				</tr>
				<tr>
					<td class="valid" colspan="2"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
				</tr>
		</table>
		</form>
<!-- END: EDIT -->
<!-- BEGIN: DEFULT -->
		<h3>{PHP.L.editdeleteentries}:</h3>
		<form name="updateorder" id="updateorder" action="{ADMIN_FORUMS_DEFULT_FORM_UPDATEORDER_URL}" method="post"{ADMIN_FORUMS_DEFULT_FORM_UPDATEORDER_URL_AJAX}>
		<table class="cells">
			<tr>
				<td class="coltop" style="width:30%;">{PHP.L.Section} {PHP.L.adm_clicktoedit}</td>
				<td class="coltop" style="width:10%;">{PHP.L.Order}</td>
				<td class="coltop" style="width:15%;">{PHP.L.adm_enableprvtopics}</td>
				<td class="coltop" style="width:10%;">{PHP.L.Topics}</td>
				<td class="coltop" style="width:10%;">{PHP.L.Posts}</td>
				<td class="coltop" style="width:10%;">{PHP.L.Views}</td>
				<td class="coltop" style="width:15%;">{PHP.L.Action}</td>
			</tr>
<!-- BEGIN: ROW -->
<!-- IF {PHP.show_fn} -->
			<tr>
				<td class="strong" colspan="7"><a href="{ADMIN_FORUMS_DEFULT_ROW_FN_URL}">{ADMIN_FORUMS_DEFULT_ROW_FN_TITLE} ({ADMIN_FORUMS_DEFULT_ROW_FN_PATH})</a></td>
			</tr>
<!-- ENDIF -->
			<tr>
				<td><a href="{ADMIN_FORUMS_DEFULT_ROW_FS_EDIT_URL}">{ADMIN_FORUMS_DEFULT_ROW_FS_TITLE}</a></td>
				<td class="centerall"><a title="{PHP.L.Up}" href="{ADMIN_FORUMS_DEFULT_ROW_FS_ORDER_UP_URL}"{ADMIN_FORUMS_DEFULT_ROW_FS_ORDER_UP_URL_AJAX}>{PHP.sed_img_up}</a> <a title="{PHP.L.Down}" href="{ADMIN_FORUMS_DEFULT_ROW_FS_ORDER_DOWN_URL}"{ADMIN_FORUMS_DEFULT_ROW_FS_ORDER_DOWN_URL_AJAX}>{PHP.sed_img_down}</a></td>
				<td class="centerall">{ADMIN_FORUMS_DEFULT_ROW_FS_ALLOWPRVTOPICS}</td>
				<td class="centerall">{ADMIN_FORUMS_DEFULT_ROW_FS_TOPICCOUNT}</td>
				<td class="centerall">{ADMIN_FORUMS_DEFULT_ROW_FS_POSTCOUNT}</td>
				<td class="centerall">{ADMIN_FORUMS_DEFULT_ROW_FS_VIEWCOUNT}</td>
				<td class="centerall action">
					<a title="{PHP.L.Rights}" href="{ADMIN_FORUMS_DEFULT_ROW_FS_RIGHTS_URL}">{PHP.R.admin_icon_rights2}</a>
					<a title="{PHP.L.Open}" href="{ADMIN_FORUMS_DEFULT_ROW_FS_TOPICS_URL}">{PHP.R.admin_icon_jumpto}</a>
					<!-- IF {PHP.usr.isadmin} --><a title="{PHP.L.Delete}" href="{ADMIN_FORUMS_DEFULT_ROW_DELETE_URL}"{ADMIN_FORUMS_DEFULT_ROW_DELETE_URL_AJAX}>{PHP.R.admin_icon_delete}</a><!-- ENDIF -->
				</td>
			</tr>
<!-- BEGIN: FCACHE -->
			<tr>
				<td>
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
				<td style="text-align:center;"><a href="{ADMIN_FORUMS_DEFULT_ROW_FS_RIGHTS_URL}">{PHP.R.admin_icon_rights2}</a></td>
				<td style="text-align:center;"><a href="{ADMIN_FORUMS_DEFULT_ROW_FS_TOPICS_URL}">{PHP.R.admin_icon_jumpto}</a></td>
			</tr>
<!-- END: FCACHE -->
<!-- END: ROW -->
			<!--//<tr>
				<td colspan="9"><div class="pagnav">{ADMIN_FORUMS_PAGINATION_PREV} {ADMIN_FORUMS_PAGNAV} {ADMIN_FORUMS_PAGINATION_NEXT}</div></td>
			</tr>//-->
			<!--//<tr>
				<td colspan="9"><input type="submit" class="submit" value="{PHP.L.Update}" /></td>
			</tr>//-->
		</table>
		</form>
		<p class="paging"><span class="a1"><!--//{PHP.L.Total}: {ADMIN_FORUMS_TOTALITEMS}, //-->{PHP.L.adm_polls_on_page}: {ADMIN_FORUMS_COUNTER_ROW}</span></p>
		<h3>{PHP.L.addnewentry}:</h3>
		<form name="addsection" id="addsection" action="{ADMIN_FORUMS_DEFULT_FORM_ADD_URL}" method="post"{ADMIN_FORUMS_DEFULT_FORM_ADD_URL_AJAX}>
		<table class="cells">
			<tr>
				<td style="width:30%;">{PHP.L.Category}:</td>
				<td style="width:70%;">{ADMIN_FORUMS_DEFULT_FORM_ADD_SELECTBOX_FORUMCAT}</td>
			</tr>
			<tr>
				<td>{PHP.L.adm_forums_master}:</td>
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
				<td>{PHP.L.Title}:</td>
				<td><input type="text" class="text" name="ntitle" value="" size="64" maxlength="128" /> {PHP.L.adm_required}</td>
			</tr>
			<tr>
				<td>{PHP.L.Description}:</td>
				<td><input type="text" class="text" name="ndesc" value="" size="64" maxlength="255" /></td>
			</tr>
			<tr>
				<td class="valid" colspan="2"><input type="submit" class="submit" value="{PHP.L.Add}" /></td>
			</tr>
		</table>
		</form>
<!-- END: DEFULT -->
	</div>
<!-- END: FORUMS -->