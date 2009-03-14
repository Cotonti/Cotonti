<!-- BEGIN: ADMINQV -->
<table style="margin:0 10px 20px 10px;">
<tr>
	<td>
		<b>{PHP.L.plu_ql_b1_title}</b>
		<ul>
		<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=main">{PHP.L.plu_ql_b1_1}</a></li>
		<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=title">{PHP.L.plu_ql_b1_2}</a></li>
		<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=skin">{PHP.L.plu_ql_b1_3}</a></li>
		<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=menus">{PHP.L.plu_ql_b1_4}</a></li>
		<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=lang">{PHP.L.plu_ql_b1_5}</a></li>
		<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=time">{PHP.L.plu_ql_b1_6}</a></li>
		<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=trash">{PHP.L.Trash}</a></li>
		</ul>
		<b>{PHP.L.plu_ql_b4_title}</b>
		<ul>
		<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=plug">{PHP.L.Plugins}</a></li>
		<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=forums">{PHP.L.Forums}</a></li>
		<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=comments">{PHP.L.Comments}</a></li>
		<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=polls">{PHP.L.Polls}</a></li>
		<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=ratings">{PHP.L.Ratings}</a></li>
		</ul>
	</td>
	<td style="padding-left:20px;">
		<b>{PHP.L.Pages}</b>
		<ul>
		<li><a href="admin.php?m=page&amp;s=structure">{PHP.L.plu_ql_b2_1}</a></li>
		<li><a href="admin.php?m=page&amp;s=extrafields">{PHP.L.plu_ql_b2_2}</a></li>
		<li><a href="admin.php?m=page&amp;s=catorder">{PHP.L.plu_ql_b2_3}</a></li>
		<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=parser">{PHP.L.plu_ql_b2_4}</a></li>
		</ul>
	</td>
	<td style="padding-left:20px;">
		<b>{PHP.L.Users}</b>
		<ul>
		<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=users">{PHP.L.plu_ql_b3_1}</a></li>
		<li><a href="admin.php?m=users&amp;s=extrafields">{PHP.L.plu_ql_b3_2}</a></li>
		<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=pfs">{PHP.L.PFS}</a></li>
		<li><a href="admin.php?m=users">{PHP.L.plu_ql_b3_4}</a></li>
		<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=pm">{PHP.L.Private_Messages}</a></li>
		</ul>
	</td>
</tr>
</table>

<h4>{PHP.L.plu_title}</h4>
<table style="width:100%;">
<tr>
	<td style="width:50%; vertical-align:top; padding:8px;">
		<table class="cells">
		<tr><td colspan="2" class="coltop">{PHP.L.plu_pastdays}</td></tr>
		<tr><td><a href="{ADMINQV_NEWUSERS_URL}">{PHP.L.plu_newusers}</a></td>
		<td style="text-align:center;">{ADMINQV_NEWUSERS}</td></tr>
		<tr><td><a href="{ADMINQV_NEWPAGES_URL}">{PHP.L.plu_newpages}</a></td>
		<td style="text-align:center;">{ADMINQV_NEWPAGES}</td></tr>
		<tr><td><a href="{ADMINQV_NEWTOPICS_URL}">{PHP.L.plu_newtopics}</a></td>
		<td style="text-align:center;">{ADMINQV_NEWTOPICS}</td></tr>
		<tr><td><a href="{ADMINQV_NEWPOSTS_URL}">{PHP.L.plu_newposts}</a></td>
		<td style="text-align:center;">{ADMINQV_NEWPOSTS}</td></tr>
		<tr><td><a href="{ADMINQV_NEWCOMMENTS_URL}">{PHP.L.plu_newcomments}</a></td>
		<td style="text-align:center;">{ADMINQV_NEWCOMMENTS}</td></tr>
		<tr><td>{PHP.L.plu_newpms}</td>
		<td style="text-align:center;">{ADMINQV_NEWPMS}</td></tr>
		</table>

		<h4>Cotonti :</h4>
		<table class="cells">
		<tr><td>{PHP.L.Version} / {PHP.L.Database}</td>
		<td style="text-align:right;">{ADMINQV_VERSION} / {ADMINQV_DB_VERSION}</td></tr>
		<tr><td>{PHP.L.plu_db_rows}</td>
		<td style="text-align:right;">{ADMINQV_DB_TOTAL_ROWS}</td></tr>
		<tr><td>{PHP.L.plu_db_indexsize}</td>
		<td style="text-align:right;">{ADMINQV_DB_INDEXSIZE}</td></tr>
		<tr><td>{PHP.L.plu_db_datassize}</td>
		<td style="text-align:right;">{ADMINQV_DB_DATASSIZE}</td></tr>
		<tr><td>{PHP.L.plu_db_totalsize}</td>
		<td style="text-align:right;">{ADMINQV_DB_TOTALSIZE}</td></tr>
		<tr><td>{PHP.L.Plugins}</td>
		<td style="text-align:right;">{ADMINQV_TOTALPLUGINS}</td></tr>
		<tr><td>{PHP.L.Hooks}</td>
		<td style="text-align:right;">{ADMINQV_TOTALHOOKS}</td></tr>
		</table>
	</td>
	<td style="width:50%; vertical-align:top; padding:8px;">
		<table class="cells">
		<tr><td colspan="4" class="coltop">{PHP.L.plu_hitsmonth}</td></tr>
		<!-- BEGIN: ADMINQV_ROW -->
		<tr>
			<td style="width:96px;">{ADMINQV_DAY}</td>
			<td style="text-align:right; width:128px;">{PHP.L.Hits}: {ADMINQV_HITS}</td>
			<td style="text-align:right; width:40px;">{ADMINQV_PERCENTBAR}%</td>
			<td><div style="width:128px;"><div class="bar_back"><div class="bar_front" style="width:{ADMINQV_PERCENTBAR}%;"></div></div></div></td>
		</tr>
		<!-- END: ADMINQV_ROW -->
		<tr><td colspan="4"><a href="{ADMINQV_MORE_HITS_URL}">{PHP.L.More}</a></td></tr>
		</table>
	</td>
</tr>
</table>
<!-- END: ADMINQV -->