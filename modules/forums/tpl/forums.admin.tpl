<!-- BEGIN: MAIN -->
	<div id="ajaxBlock">
		<h2>{PHP.L.Forums}</h2>
		{FILE ./themes/nemesis/warnings.tpl}
		<ul class="follow">
			<li><a title="{PHP.L.Configuration}" href="{ADMIN_FORUMS_CONF_URL}">{PHP.L.Configuration}</a></li>
			<!-- IF {PHP.lincif_conf} --><li><a href="{ADMIN_FORUMS_CONF_STRUCTURE_URL}">{PHP.L.adm_forum_structure}</a></li><!-- ELSE --><li>{PHP.L.adm_forum_structure}</li><!-- ENDIF -->
		</ul>
<!-- BEGIN: EDIT -->
		<form id="updatesection" action="{ADMIN_FORUMS_EDIT_FORM_URL}" method="post">
		<table class="cells">
			<tr>
				<td class="width30">{PHP.L.Section}:</td>
				<td class="width70">{ADMIN_FORUMS_EDIT_FS_ID}</td>
			</tr>
			<tr>
				<td>{PHP.L.adm_forums_master}:</td>
				<td>{ADMIN_FORUMS_EDIT_FS_MASTER}</td>
			</tr>
			<tr>
				<td>{PHP.L.Category}:</td>
				<td>{ADMIN_FORUMS_EDIT_SELECTBOX_FORUMCAT}</td>
			</tr>
			<tr>
				<td>{PHP.L.Title}:</td>
				<td>{ADMIN_FORUMS_EDIT_FS_TITLE}</td>
			</tr>
			<tr>
				<td>{PHP.L.Description}:</td>
				<td>{ADMIN_FORUMS_EDIT_FS_DESC}</td></tr>
			<tr>
				<td>{PHP.L.Icon}:</td>
				<td>{ADMIN_FORUMS_EDIT_FS_ICON}</td>
			</tr>
			<tr>
				<td>{PHP.L.adm_diplaysignatures}:</td>
				<td>{ADMIN_FORUMS_EDIT_FS_ALLOWUSERTEXT}</td>
			</tr>
			<tr>
				<td>{PHP.L.adm_enablebbcodes}:</td>
				<td>{ADMIN_FORUMS_EDIT_FS_ALLOWBBCODES}</td>
			<tr>
				<td>{PHP.L.adm_enablesmilies}:</td>
				<td>{ADMIN_FORUMS_EDIT_FS_ALLOWSMILES}</td>
			</tr>
			<tr>
				<td>{PHP.L.adm_enableprvtopics}:</td>
				<td>{ADMIN_FORUMS_EDIT_FS_ALLOWPRVTOPICS}</td>
			</tr>
			<tr>
				<td>{PHP.L.adm_enableviewers}:</td>
				<td>{ADMIN_FORUMS_EDIT_FS_ALLOWVIEWERS}</td>
			</tr>
			<tr>
				<td>{PHP.L.adm_enablepolls}:</td>
				<td>{ADMIN_FORUMS_EDIT_FS_ALLOWPOLLS}</td>
			</tr>
			<tr>
				<td>{PHP.L.adm_countposts}:</td>
				<td>{ADMIN_FORUMS_EDIT_FS_COUNTPOSTS}</td>
			</tr>
			<tr>
				<td>{PHP.L.Locked}:</td>
				<td>{ADMIN_FORUMS_EDIT_FS_STATE}</td>
			</tr>
			<tr>
				<td>{PHP.L.adm_autoprune}:</td>
				<td>{ADMIN_FORUMS_EDIT_FS_AUTOPRUNE}</td>
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
		<form name="updateorder" id="updateorder" action="{ADMIN_FORUMS_DEFAULT_FORM_UPDATEORDER_URL}" method="post" class="ajax">
			<table class="cells">
				<tr>
					<td class="coltop width5"></td>
					<td class="coltop width30">{PHP.L.Section} (<span class="lower">{PHP.L.Views}</span>)</td>
					<td class="coltop width10">{PHP.L.Order}</td>
					<td class="coltop width15">{PHP.L.adm_enableprvtopics}</td>
					<td class="coltop width10">{PHP.L.Topics}</td>
					<td class="coltop width10">{PHP.L.Posts}</td>
					<td class="coltop width20">{PHP.L.Action}</td>
				</tr>
<!-- BEGIN: ROW -->
				<!-- IF {PHP.show_fn} --><tr>
					<td class="strong" colspan="7"><a href="{ADMIN_FORUMS_DEFAULT_ROW_FN_URL}">{ADMIN_FORUMS_DEFAULT_ROW_FN_TITLE} ({ADMIN_FORUMS_DEFAULT_ROW_FN_PATH})</a></td>
				</tr><!-- ENDIF -->
				<tr>
					<td class="centerall">{PHP.R.icon_folder}</td>
					<td><a href="{ADMIN_FORUMS_DEFAULT_ROW_FS_EDIT_URL}">{ADMIN_FORUMS_DEFAULT_ROW_FS_TITLE}</a> ({ADMIN_FORUMS_DEFAULT_ROW_FS_VIEWCOUNT})</td>
					<td class="centerall"><a title="{PHP.L.Up}" href="{ADMIN_FORUMS_DEFAULT_ROW_FS_ORDER_UP_URL}" class="ajax">{PHP.cot_img_up}</a> <a title="{PHP.L.Down}" href="{ADMIN_FORUMS_DEFAULT_ROW_FS_ORDER_DOWN_URL}" class="ajax">{PHP.cot_img_down}</a></td>
					<td class="centerall">{ADMIN_FORUMS_DEFAULT_ROW_FS_ALLOWPRVTOPICS}</td>
					<td class="centerall">{ADMIN_FORUMS_DEFAULT_ROW_FS_TOPICCOUNT}</td>
					<td class="centerall">{ADMIN_FORUMS_DEFAULT_ROW_FS_POSTCOUNT}</td>
					<td class="centerall action">
						<a title="{PHP.L.Rights}" href="{ADMIN_FORUMS_DEFAULT_ROW_FS_RIGHTS_URL}">{PHP.R.admin_icon_rights2}</a>
						<a title="{PHP.L.Open}" href="{ADMIN_FORUMS_DEFAULT_ROW_FS_TOPICS_URL}">{PHP.R.admin_icon_jumpto}</a>
						<!-- IF {PHP.usr.isadmin} --><a title="{PHP.L.Delete}" href="{ADMIN_FORUMS_DEFAULT_ROW_DELETE_URL}" class="ajax">{PHP.R.admin_icon_delete}</a><!-- ENDIF -->
					</td>
				</tr>
<!-- BEGIN: FCACHE -->
				<tr>
					<td class="centerall">{PHP.R.icon_subfolder}</td>
					<td class="leftindent"><a href="{ADMIN_FORUMS_DEFAULT_ROW_FS_EDIT_URL}">{ADMIN_FORUMS_DEFAULT_ROW_FS_TITLE}</a></td>
					<td class="centerall"><a href="{ADMIN_FORUMS_DEFAULT_ROW_FS_ORDER_UP_URL}" class="ajax">{PHP.cot_img_up}</a> <a href="{ADMIN_FORUMS_DEFAULT_ROW_FS_ORDER_DOWN_URL}" class="ajax">{PHP.cot_img_down}</a></td>
					<td class="centerall">{ADMIN_FORUMS_DEFAULT_ROW_FS_ALLOWPRVTOPICS}</td>
					<td class="centerall">{ADMIN_FORUMS_DEFAULT_ROW_FS_TOPICCOUNT}</td>
					<td class="centerall">{ADMIN_FORUMS_DEFAULT_ROW_FS_POSTCOUNT}</td>
					<td class="centerall">{ADMIN_FORUMS_DEFAULT_ROW_FS_VIEWCOUNT}</td>
					<td class="centerall action">
						<a href="{ADMIN_FORUMS_DEFAULT_ROW_FS_RIGHTS_URL}">{PHP.R.admin_icon_rights2}</a>
						<a href="{ADMIN_FORUMS_DEFAULT_ROW_FS_TOPICS_URL}">{PHP.R.admin_icon_jumpto}</a>
						<!-- IF {PHP.usr.isadmin} --><a href="{ADMIN_FORUMS_DEFAULT_ROW_DELETE_URL}" class="ajax">{PHP.R.admin_icon_delete}</a><!-- ENDIF -->
					</td>
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
		<h3>{PHP.L.Add}:</h3>
		<form name="addsection" id="addsection" action="{ADMIN_FORUMS_DEFAULT_FORM_ADD_URL}" method="post" class="ajax">
			<table class="cells">
				<tr>
					<td class="width30">{PHP.L.Category}:</td>
					<td class="width70">{ADMIN_FORUMS_DEFAULT_FORM_ADD_SELECTBOX_FORUMCAT}</td>
				</tr>
				<tr>
					<td>{PHP.L.adm_forums_master}:</td>
					<td>{ADMIN_FORUMS_DEFAULT_FORM_ADD_MASTER}</td>
				</tr>
				<tr>
					<td>{PHP.L.Title}:</td>
					<td>{ADMIN_FORUMS_DEFAULT_FORM_ADD_TITLE} {PHP.L.adm_required}</td>
				</tr>
				<tr>
					<td>{PHP.L.Description}:</td>
					<td>{ADMIN_FORUMS_DEFAULT_FORM_ADD_DESC}</td>
				</tr>
				<tr>
					<td class="valid" colspan="2"><button type="submit">{PHP.L.Add}</button></td>
				</tr>
			</table>
		</form>
<!-- END: DEFULT -->
	</div>
<!-- END: MAIN -->