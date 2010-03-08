<!-- BEGIN: MAIN -->

			<div id="left">

				<h1>{PHP.L.Private_Messages}</h1>
				<p class="breadcrumb">{PHP.skinlang.list.bread}: <a href="users.php">{PHP.L.Users}</a> {PHP.cfg.separator} <a href="users.php?m=details&amp;id={PHP.usr.id}&amp;u={PHP.usr.name}">{PHP.usr.name}</a> {PHP.cfg.separator} {PM_PAGETITLE} </p>
				<p class="details">{PM_SUBTITLE}</p>

				<form action="{PM_FORM_UPDATE}" method="post">
				<p style="border-bottom:1px solid #ececec">&nbsp;</p>

				<!-- BEGIN: PM_ROW -->
				<div class="<!-- IF {PHP.row.pm_state} == 0 -->newpm <!-- ELSE -->{PM_ROW_ODDEVEN} <!-- ENDIF --> nou padding5 toprow mail">
				<a href="pm.php?m=message&amp;id={PHP.row.pm_id}"> <span class="fleft">{PM_ROW_SELECT} &nbsp; </span>
				<p style="width:100px; float:left;">{PHP.row.pm_fromuser} &nbsp; </p>
				&raquo; &nbsp;{PHP.row.pm_title} &nbsp; <span class="fright">{PM_ROW_DATE}</span></a></div>
				<!-- END: PM_ROW -->

				<!-- BEGIN: PM_ROW_EMPTY -->
				<p class="error">{PHP.skinlang.list.none}</p>
				<!-- END: PM_ROW_EMPTY -->

				<!-- BEGIN: PM_FOOTER -->
				<!-- IF {PM_TOP_PAGES} -->
				<div class="paging">{PM_TOP_PAGEPREV}&nbsp;{PM_TOP_PAGES}&nbsp;{PM_TOP_PAGENEXT}</div>
				<!-- ENDIF -->
				<!-- END: PM_FOOTER -->

				&nbsp;<p class="point">
				<!-- IF {PHP.cfg.jquery} AND {PM_ARCHIVE} -->
				<img src="skins/{PHP.skin}/img/icon-this.gif" alt="{PHP.L.Options}" />
				<input type="button" value="{PHP.skinlang.pm.Selectall}" onClick="$('.checkbox').attr('checked', 'checked');" />
				<input type="button" value="{PHP.skinlang.pm.Unselectall}" onClick="$('.checkbox').removeAttr('checked');" />
				<!-- ENDIF -->
				{PM_DELETE} {PM_ARCHIVE} </p>
				</form>

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