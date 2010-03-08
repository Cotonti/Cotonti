<!-- BEGIN: MAIN -->

			<div id="left">

				<h1>{PHP.L.Private_Messages}</h1>

<!-- BEGIN: BEFORE_AJAX -->
<div id="ajaxBlock">
<!-- END: BEFORE_AJAX -->

				<p class="breadcrumb">{PHP.skinlang.list.bread}: <a href="users.php">{PHP.L.Users}</a> {PHP.cfg.separator} <a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{PHP.usr.name}</a> {PHP.cfg.separator} {PM_PAGETITLE}</p>
				<p class="details">{PM_SUBTITLE}</p>

				{PHP.L.Sender}: {PM_FROMUSER}<br />
				{PHP.L.Recipient}: {PM_TOUSER}<br />
				{PHP.L.Date}: {PM_DATE}<br /><br />
				{PHP.L.Subject}: <strong><!-- IF {PM_TITLE} -->{PM_TITLE}<!-- ELSE -->{PHP.skinlang.pm.Newmessage}<!-- ENDIF --></strong>
				<hr />

				<p class="postbox padding10">{PM_TEXT}</p>&nbsp;

				<p style="text-align:right">
				<a href="pm.php?m=message&amp;id={PHP.id}#pmreply" class="comm"><span>{PHP.L.Reply}</span></a> 
				<a href="pm.php?m=message&amp;id={PHP.id}&amp;q=quote" class="comm"><span>{PHP.L.Quote}</span></a>
				{PM_DELETE}
				{PM_HISTORY}
				</p>

				<!-- BEGIN: REPLY -->
				<a id="pmreply" name="pmreply"></a>
				<h2>{PHP.L.pm_replyto}</h2>
				<form action="{PM_FORM_SEND}" method="post">
				{PHP.L.Subject}: <strong style="font-size:1.4em">{PM_FORM_TITLE}</strong>
				<div class="pageadd centerall">{PM_FORM_TEXTBOXER}<input type="submit" value="{PHP.L.Reply}" class="submit" /></div>
				</form>
				<!-- END: REPLY -->

				<div id="ajaxHistory"> &nbsp;
					<!-- BEGIN: HISTORY -->
					<table class="cells" border="0" cellspacing="1" cellpadding="2">
					<!-- BEGIN: PM_ROW -->
					<tr>
						<td class="{PM_ROW_ODDEVEN}" style="width:126px;">{PM_ROW_USER_NAME}<br />{PM_ROW_DATE}</td>
						<td class="{PM_ROW_ODDEVEN}">{PM_ROW_TEXT}</td>
					</tr>
					<!-- END: PM_ROW -->
					<!-- BEGIN: PM_ROW_EMPTY -->
					<tr>
						<td colspan="2" style="padding:16px;">{PHP.L.None}</td>
					</tr>
					<!-- END: PM_ROW_EMPTY -->
					</table>
					<div class="paging">{PM_PAGEPREV}&nbsp;{PM_PAGES}&nbsp;{PM_PAGENEXT}</div>
					<!-- END: HISTORY -->
				</div>

<!-- IF {PM_AJAX_MARKITUP} -->
<script type="text/javascript">
$(document).ready(function() {$("textarea.editor").markItUp(mySettings);});
</script>
<!-- ENDIF -->
<!-- IF {PHP.cfg.jquery} -->
<script type="text/javascript" src="{PHP.cfg.modules_dir}/pm/js/pm.js"></script>
<!-- ENDIF -->
<!-- BEGIN: AFTER_AJAX -->
</div>
<!-- END: AFTER_AJAX -->

			</div>

		</div>
	</div>

	<div id="right">
		<h3 class="black">{PHP.skinlang.header.logged} {PHP.usr.name}</h3>
		<h3><a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{PHP.L.View} {PHP.L.Profile}</a></h3>
		<h3><a href="users.php?m=profile">{PHP.L.Update} {PHP.L.Profile}</a></h3>
		<h3><span style="background-color:#94af66; color:#fff">{PHP.L.Private_Messages}</span></h3>
		<div class="padding15 admin" style="padding-bottom:0">
			<ul>
				<li>{PM_INBOX}</li>
				<li>{PM_ARCHIVES}</li>
				<li>{PM_SENTBOX}</li>
				<li>{PM_SENDNEWPM}</li>
			</ul>
		</div>
		<h3><a href="pfs.php">{PHP.L.PFS}</a></h3>
		<h3><a href="users.php">{PHP.L.Users}</a></h3>
		&nbsp;
	</div>

	<br class="clear" />

<!-- END: MAIN -->