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
			<h2 class="forums">{FORUMS_POSTS_PAGETITLE}</h2>
<!-- BEGIN: FORUMS_POSTS_ADMIN -->
			<form id="movetopic" action="{FORUMS_POSTS_MOVE_URL}" method="post" class="marginbottom10">
				<table class="flat">
					<tr>
						<td class="textright width10">{PHP.L.forums_topicoptions}:</td>
						<td class="width90">
							<a href="{FORUMS_POSTS_BUMP_URL}" title="{PHP.L.forums_explainbump}">{PHP.L.forums_bump}</a> {PHP.cfg.separator}
							<a href="{FORUMS_POSTS_LOCK_URL}" title="{PHP.L.forums_explainlock}">{PHP.L.Lock}</a> {PHP.cfg.separator}
							<a href="{FORUMS_POSTS_STICKY_URL}" title="{PHP.L.forums_explainsticky}">{PHP.L.forums_makesticky}</a> {PHP.cfg.separator}
							<a href="{FORUMS_POSTS_ANNOUNCE_URL}" title="{PHP.L.forums_explainannounce}">{PHP.L.forums_announcement}</a> {PHP.cfg.separator}
							<a href="{FORUMS_POSTS_PRIVATE_URL}" title="{PHP.L.forums_explainprivate}">{PHP.L.forums_private} (#)</a> {PHP.cfg.separator}
							<a href="{FORUMS_POSTS_CLEAR_URL}" title="{PHP.L.forums_explaindefault}">{PHP.L.Default}</a> {PHP.cfg.separator}
							<a href="{FORUMS_POSTS_DELETE_URL}" title="{PHP.L.forums_explaindelete}" class="confirmLink">{PHP.L.Delete}</a>
						</td>
					</tr>
					<tr>
						<td class="textright">{PHP.L.Move}:</td>
						<td>{FORUMS_POSTS_MOVEBOX_SELECT}<span class="small spaced">{FORUMS_POSTS_MOVEBOX_KEEP} {PHP.L.forums_keepmovedlink}</span><button type="submit">{PHP.L.Move}</button></td>
					</tr>
				</table>
			</form>
<!-- END: FORUMS_POSTS_ADMIN -->
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
							<!-- IF {PHP.cot_plugins_active.whosonline} -->
							<img src="themes/{PHP.theme}/img/online{FORUMS_POSTS_ROW_USERONLINE}.png" title="{PHP.L.Status}: {FORUMS_POSTS_ROW_USERONLINETITLE}" alt="" class="userstatus" />
							<!-- ENDIF -->
							{FORUMS_POSTS_ROW_USERNAME}
						</h4>
					</td>
					<td class="small centerall textright {FORUMS_POSTS_ROW_ODDEVEN}">
						<a name="{FORUMS_POSTS_ROW_ID}" id="{FORUMS_POSTS_ROW_POSTID}" href="{FORUMS_POSTS_ROW_IDURL}" rel="nofollow">#{FORUMS_POSTS_ROW_ORDER}</a><span class="spaced">{PHP.cfg.separator}</span>{FORUMS_POSTS_ROW_CREATION}<!-- IF {FORUMS_POSTS_ROW_POSTERIP} --><span class="spaced">{PHP.cfg.separator}</span>{FORUMS_POSTS_ROW_POSTERIP}<!-- ENDIF --><!-- IF {FORUMS_POSTS_ROW_QUOTE} --><span class="spaced">{PHP.cfg.separator}</span>{FORUMS_POSTS_ROW_QUOTE}<!-- ENDIF --><!-- IF {FORUMS_POSTS_ROW_EDIT} --><span class="spaced">{PHP.cfg.separator}</span>{FORUMS_POSTS_ROW_EDIT}<!-- ENDIF --><!-- IF {FORUMS_POSTS_ROW_DELETE} --><span class="spaced">{PHP.cfg.separator}</span>{FORUMS_POSTS_ROW_DELETE}<!-- ENDIF --> {FORUMS_POSTS_ROW_BOTTOM}
					</td>
				</tr>
				<tr>
					<td class="{FORUMS_POSTS_ROW_ODDEVEN}">
						<p><!-- IF {FORUMS_POSTS_ROW_USERAVATAR} -->{FORUMS_POSTS_ROW_USERAVATAR}<!-- ELSE -->{PHP.R.forums_noavatar}<!-- ENDIF -->
						<p>{FORUMS_POSTS_ROW_USERMAINGRPTITLE}</p>
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
						{PHP.L.forums_posts}: {FORUMS_POSTS_ROW_USERPOSTCOUNT}
					</td>
					<td class="small {FORUMS_POSTS_ROW_ODDEVEN}">
						{FORUMS_POSTS_ROW_USERTEXT}
					</td>
				</tr>
				<!-- END: FORUMS_POSTS_ROW -->
			</table>
			<!-- IF {FORUMS_POSTS_PAGES} --><p class="paging">{FORUMS_POSTS_PAGEPREV}{FORUMS_POSTS_PAGENEXT}{FORUMS_POSTS_PAGES}</p><!-- ENDIF -->
		</div>
<!-- BEGIN: FORUMS_POSTS_TOPICLOCKED -->
		<div class="error">{FORUMS_POSTS_TOPICLOCKED_BODY}</div>
<!-- END: FORUMS_POSTS_TOPICLOCKED -->

<!-- BEGIN: FORUMS_POSTS_ANTIBUMP -->
		<div>{FORUMS_POSTS_ANTIBUMP_BODY}</div>
<!-- END: FORUMS_POSTS_ANTIBUMP -->

{FILE "{PHP.cfg.themes_dir}/{PHP.usr.theme}/warnings.tpl"}

<!-- BEGIN: FORUMS_POSTS_NEWPOST -->
		<form action="{FORUMS_POSTS_NEWPOST_SEND}" method="post" name="newpost">
			<table class="flat">
				<tr>
					<td>{FORUMS_POSTS_NEWPOST_TEXT}
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