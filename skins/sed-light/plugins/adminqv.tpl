<!-- BEGIN: ADMINQV -->

	<div id="center" class="column">
		<!-- BEGIN: UPDATE -->
		<div class="block">
			<h3>{PHP.L.adminqv_update_notice}:</h3>
			<p>{ADMINQV_UPDATE_REVISION} {ADMINQV_UPDATE_MESSAGE}</p>
		</div>
		<!-- END: UPDATE -->
		<div class="block">
			<h3>{PHP.L.plu_hitsmonth}</h3>
			<table class="cells">
				<!-- BEGIN: ADMINQV_ROW -->
				<tr>
					<td style="width:15%;">{ADMINQV_DAY}</td>
					<td class="centerall" style="width:40%;">
						<div class="bar_back">
							<div class="bar_front" style="width:{ADMINQV_PERCENTBAR}%;"></div>
						</div>
					</td>
					<td style="width:25%;">{PHP.L.Hits}: {ADMINQV_HITS}</td>
					<td class="textcenter" style="width:20%;">{ADMINQV_PERCENTBAR}%</td>
				</tr>
				<!-- END: ADMINQV_ROW -->
			</table>
			<p><a href="{ADMINQV_MORE_HITS_URL}">{PHP.L.More}...</a></p>
		</div>
		<div class="block">
			<h3>{PHP.L.plu_title}</h3>
			<table class="cells">
				<tr>
					<td style="width:80%;"><a href="{ADMINQV_NEWUSERS_URL}">{PHP.L.plu_newusers}</a></td>
					<td class="textcenter" style="width:20%;">{ADMINQV_NEWUSERS}</td>
				</tr>
				<tr>
					<td><a href="{ADMINQV_NEWPAGES_URL}">{PHP.L.plu_newpages}</a></td>
					<td class="textcenter">{ADMINQV_NEWPAGES}</td>
				</tr>
				<tr>
					<td><a href="{ADMINQV_NEWTOPICS_URL}">{PHP.L.plu_newtopics}</a></td>
					<td class="textcenter">{ADMINQV_NEWTOPICS}</td>
				</tr>
				<tr>
					<td><a href="{ADMINQV_NEWPOSTS_URL}">{PHP.L.plu_newposts}</a></td>
					<td class="textcenter">{ADMINQV_NEWPOSTS}</td>
				</tr>
				<tr>
					<td><a href="{ADMINQV_NEWCOMMENTS_URL}">{PHP.L.plu_newcomments}</a></td>
					<td class="textcenter">{ADMINQV_NEWCOMMENTS}</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_newpms}</td>
					<td class="textcenter">{ADMINQV_NEWPMS}</td>
				</tr>
			</table>
		</div>
		<div class="block">
			<h3>Cotonti:</h3>
			<table class="cells">
				<tr>
					<td style="width:80%;">{PHP.L.Version} ({PHP.L.adminqv_rev_title})</td>
					<td class="textcenter" style="width:20%;">{ADMINQV_VERSION} ({ADMINQV_REVISION})</td>
				</tr>
				<tr>
					<td>{PHP.L.Database}</td>
					<td class="textcenter">{ADMINQV_DB_VERSION}</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_db_rows}</td>
					<td class="textcenter">{ADMINQV_DB_TOTAL_ROWS}</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_db_indexsize}</td>
					<td class="textcenter">{ADMINQV_DB_INDEXSIZE}</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_db_datassize}</td>
					<td class="textcenter">{ADMINQV_DB_DATASSIZE}</td>
				</tr>
				<tr>
					<td>{PHP.L.plu_db_totalsize}</td>
					<td class="textcenter">{ADMINQV_DB_TOTALSIZE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Plugins}</td>
					<td class="textcenter">{ADMINQV_TOTALPLUGINS}</td>
				</tr>
				<tr>
					<td>{PHP.L.Hooks}</td>
					<td class="textcenter">{ADMINQV_TOTALHOOKS}</td>
				</tr>
			</table>
		</div>
	</div>
	<div id="side" class="column">
		<div class="block">
			<h3>{PHP.L.plu_ql_b1_title}</h3>
			<ul class="follow">
				<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=main">{PHP.L.plu_ql_b1_1}</a></li>
				<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=title">{PHP.L.plu_ql_b1_2}</a></li>
				<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=skin">{PHP.L.plu_ql_b1_3}</a></li>
				<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=menus">{PHP.L.plu_ql_b1_4}</a></li>
				<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=lang">{PHP.L.plu_ql_b1_5}</a></li>
				<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=time">{PHP.L.plu_ql_b1_6}</a></li>
				<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=trash">{PHP.L.Trash}</a></li>
			</ul>
		</div>
		<div class="block">
			<h3>{PHP.L.Configuration}</h3>
			<ul class="follow">
				<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=plug">{PHP.L.Plugins}</a></li>
				<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=forums">{PHP.L.Forums}</a></li>
				<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=comments">{PHP.L.Comments}</a></li>
				<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=polls">{PHP.L.Polls}</a></li>
				<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=ratings">{PHP.L.Ratings}</a></li>
			</ul>
		</div>
		<div class="block">
			<h3>{PHP.L.Pages}</h3>
			<ul class="follow">
				<li><a href="page.php?m=add">{PHP.L.addnewentry}</a></li>
				<li><a href="admin.php?m=page&amp;s=structure">{PHP.L.plu_ql_b2_1}</a></li>
				<li><a href="admin.php?m=page&amp;s=extrafields">{PHP.L.plu_ql_b2_2}</a></li>
				<li><a href="admin.php?m=page&amp;s=catorder">{PHP.L.plu_ql_b2_3}</a></li>
				<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=parser">{PHP.L.plu_ql_b2_4}</a></li>
			</ul>
		</div>
		<div class="block">
			<h3>{PHP.L.Users}</h3>
			<ul class="follow">
				<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=users">{PHP.L.plu_ql_b3_1}</a></li>
				<li><a href="admin.php?m=users&amp;s=extrafields">{PHP.L.plu_ql_b3_2}</a></li>
				<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=pfs">{PHP.L.PFS}</a></li>
				<li><a href="admin.php?m=users">{PHP.L.plu_ql_b3_4}</a></li>
				<li><a href="admin.php?m=config&amp;n=edit&amp;o=core&amp;p=pm">{PHP.L.Private_Messages}</a></li>
			</ul>
		</div>
	</div>
	<div class="clear"></div>

<!-- END: ADMINQV -->