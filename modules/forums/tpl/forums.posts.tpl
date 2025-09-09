<!-- BEGIN: MAIN -->
<!-- BEGIN: FORUMS_POSTS_TOPICPRIVATE -->
<div class="error">{PHP.L.forums_privatetopic}</div>
<!-- END: FORUMS_POSTS_TOPICPRIVATE -->

<!-- BEGIN: POLLS_VIEW -->
<div class="block">
	<h2 class="polls">{POLLS_TITLE}</h2>
	{POLLS_FORM}
</div>
<!-- END: POLLS_VIEW -->

<div class="block">
	<h2 class="forums">
		{FORUMS_POSTS_BREADCRUMBS}
		<a href="{FORUMS_POSTS_RSS}" style="float: right"><img
					src="{PHP.cfg.themes_dir}/nemesis/img/rss.png" alt="RSS" style="vertical-align: middle"></a>
	</h2>
	<!-- BEGIN: FORUMS_POSTS_ADMIN -->
	<table class="flat">
		<tr>
			<td class="textright width10">{PHP.L.forums_topicoptions}:</td>
			<td class="width90">
				<a href="{FORUMS_POSTS_BUMP_URL}" title="{PHP.L.forums_explainbump}">{PHP.L.forums_bump}</a> {PHP.cfg.separator}
				<a href="{FORUMS_POSTS_LOCK_URL}" title="{PHP.L.forums_explainlock}">{FORUMS_POSTS_LOCK_LABEL}</a> {PHP.cfg.separator}
				<a href="{FORUMS_POSTS_STICKY_URL}" title="{PHP.L.forums_explainsticky}">{PHP.L.forums_makesticky}</a> {PHP.cfg.separator}
				<a href="{FORUMS_POSTS_ANNOUNCE_URL}" title="{PHP.L.forums_explainannounce}">{PHP.L.forums_announcement}</a> {PHP.cfg.separator}
				<a href="{FORUMS_POSTS_PRIVATE_URL}" title="{PHP.L.forums_explainprivate}">{PHP.L.forums_private} (#)</a> {PHP.cfg.separator}
				<a href="{FORUMS_POSTS_CLEAR_URL}" title="{PHP.L.forums_explaindefault}">{PHP.L.Default}</a> {PHP.cfg.separator}
				<a href="{FORUMS_POSTS_DELETE_URL}" title="{PHP.L.forums_explaindelete}" class="confirmLink">{PHP.L.Delete}</a>
			</td>
		</tr>
		<tr>
			<td class="textright">{PHP.L.Move}:</td>
			<td>
				<form id="movetopic" action="{FORUMS_POSTS_MOVE_URL}" method="post">
					<div style="display: flex; gap: 5px; align-items: center">
						<div style="flex-grow: 1">{FORUMS_POSTS_MOVEBOX_SELECT}</div>
						<span class="small">{FORUMS_POSTS_MOVEBOX_KEEP} {PHP.L.forums_keepmovedlink}</span>
						<button type="submit">{PHP.L.Move}</button>
					</div>
				</form>
			</td>
		</tr>
	</table>
	<!-- END: FORUMS_POSTS_ADMIN -->
</div>

{FILE "{PHP.cfg.themes_dir}/{PHP.usr.theme}/warnings.tpl"}

<div class="block">
	{FORUMS_POSTS_TAGS}
	<table class="cells">
		<tr>
			<td class="coltop width20">{PHP.L.Author}</td>
			<td class="coltop width80">{PHP.L.Message}</td>
		</tr>
		<!-- BEGIN: FORUMS_POSTS_ROW -->
		<tr>
			<td class="{FORUMS_POSTS_ROW_ODDEVEN}">
				{FORUMS_POSTS_ROW_ANCHORLINK}
				<h4>
					<!-- IF {PHP|cot_plugin_active('whosonline')} -->
					<img
						src="themes/{PHP.theme}/img/online{FORUMS_POSTS_ROW_USER_ONLINE}.png"
						title="{PHP.L.Status}: {FORUMS_POSTS_ROW_USER_ONLINE_TITLE}" alt="" class="userstatus" />
					<!-- ENDIF -->
					{FORUMS_POSTS_ROW_USER_NAME}
				</h4>
			</td>
			<td class="small centerall textright {FORUMS_POSTS_ROW_ODDEVEN}">
				<a name="{FORUMS_POSTS_ROW_ID}" id="{FORUMS_POSTS_ROW_POSTID}" href="{FORUMS_POSTS_ROW_IDURL}" rel="nofollow">#{FORUMS_POSTS_ROW_ORDER}</a><span class="spaced">{PHP.cfg.separator}</span>{FORUMS_POSTS_ROW_CREATION}<!-- IF {FORUMS_POSTS_ROW_POSTERIP} --><span class="spaced">{PHP.cfg.separator}</span>{FORUMS_POSTS_ROW_POSTERIP}<!-- ENDIF --><!-- IF {FORUMS_POSTS_ROW_QUOTE} --><span class="spaced">{PHP.cfg.separator}</span>{FORUMS_POSTS_ROW_QUOTE}<!-- ENDIF --><!-- IF {FORUMS_POSTS_ROW_EDIT} --><span class="spaced">{PHP.cfg.separator}</span>{FORUMS_POSTS_ROW_EDIT}<!-- ENDIF --><!-- IF {FORUMS_POSTS_ROW_DELETE} --><span class="spaced">{PHP.cfg.separator}</span>{FORUMS_POSTS_ROW_DELETE}<!-- ENDIF --> {FORUMS_POSTS_ROW_BOTTOM}
			</td>
		</tr>
		<tr>
			<td class="{FORUMS_POSTS_ROW_ODDEVEN}">
				<p><!-- IF {FORUMS_POSTS_ROW_USER_AVATAR} -->{FORUMS_POSTS_ROW_USER_AVATAR}<!-- ELSE -->{PHP.R.forums_noavatar}<!-- ENDIF -->
				<p>{FORUMS_POSTS_ROW_USER_MAIN_GROUP_TITLE}</p>
			</td>
			<td class="{FORUMS_POSTS_ROW_ODDEVEN}">
				<div>
					{FORUMS_POSTS_ROW_TEXT}
				</div>
				<!-- IF {FORUMS_POSTS_ROW_UPDATEDBY} -->
				<div class="italic margintop10 grey">{FORUMS_POSTS_ROW_UPDATEDBY}</div>
				<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td class="small {FORUMS_POSTS_ROW_ODDEVEN}">
				{PHP.L.forums_posts}: {FORUMS_POSTS_ROW_USER_POSTCOUNT}
			</td>
			<td class="small {FORUMS_POSTS_ROW_ODDEVEN}">
				{FORUMS_POSTS_ROW_USER_TEXT}
			</td>
		</tr>
		<!-- END: FORUMS_POSTS_ROW -->
	</table>
	<!-- IF {PAGINATION} -->
	<p class="paging">{PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}</p>
	<!-- ENDIF -->
</div>
<!-- BEGIN: FORUMS_POSTS_TOPICLOCKED -->
<div class="error">{FORUMS_POSTS_TOPICLOCKED_BODY}</div>
<!-- END: FORUMS_POSTS_TOPICLOCKED -->

<!-- BEGIN: FORUMS_POSTS_ANTIBUMP -->
<div>{FORUMS_POSTS_ANTIBUMP_BODY}</div>
<!-- END: FORUMS_POSTS_ANTIBUMP -->

<!-- BEGIN: FORUMS_POSTS_NEWPOST -->
<form action="{FORUMS_POSTS_NEWPOST_SEND}" method="post" name="newpost">
	<table class="flat">
		<tr>
			<td>
				{FORUMS_POSTS_NEWPOST_TEXT}
				<!-- IF {FORUMS_POSTS_NEWPOST_PFS} -->{FORUMS_POSTS_NEWPOST_PFS}<!-- ENDIF -->
				<!-- IF {FORUMS_POSTS_NEWPOST_SFS} --><span class="spaced">{PHP.cfg.separator}</span>
				{FORUMS_POSTS_NEWPOST_SFS}<!-- ENDIF -->
				<!-- IF {PHP.cfg.forums.edittimeout} != 0 -->
				{PHP.L.forums_edittimeoutnote} {FORUMS_POSTS_NEWPOST_EDITTIMEOUT}
				<!-- ENDIF -->
			</td>
		</tr>
		<tr>
			<td class="valid"><button type="submit">{PHP.L.Reply}</button></td>
		</tr>
	</table>
</form>
<!-- END: FORUMS_POSTS_NEWPOST -->
<!-- END: MAIN -->