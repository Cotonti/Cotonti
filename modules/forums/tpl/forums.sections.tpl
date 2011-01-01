<!-- BEGIN: MAIN -->

<div class="block">
	<h2 class="forums">{FORUMS_SECTIONS_PAGETITLE}</h2>
	<!-- BEGIN: FORUMS_SECTIONS -->
	<table class="cells">
		<thead>
			<tr>
				<td class="coltop" class="width10">&nbsp;</td>
				<td class="coltop" class="width40">
					<a href="forums.php?c=fold#top" rel="nofollow">{PHP.L.forums_foldall}</a><span class="spaced">/</span><a href="forums.php?c=unfold#top" rel="nofollow">{PHP.L.forums_unfoldall}</a>
				</td>
				<td class="coltop" class="width20">{PHP.L.Lastpost}</td>
				<td class="coltop" class="width10">{PHP.L.Topics}</td>
				<td class="coltop" class="width10">{PHP.L.Posts}</td>
				<td class="coltop" class="width10">{PHP.L.Activity}</td>
			</tr>
		</thead>

		<!-- BEGIN: CAT -->
		<tbody id="{FORUMS_SECTIONS_ROW_CAT_CODE}">
			<tr>
				<td class="forumssection" colspan="6">
						{FORUMS_SECTIONS_ROW_TITLE}
				</td>
			</tr>
		</tbody>
		<tbody id="blk_{FORUMS_SECTIONS_ROW_CAT}" {$style}>
			   <!-- BEGIN: SECTION -->
			   <tr>
				<td class="centerall">
					{FORUMS_SECTIONS_ROW_ICON}
				</td>
				<td>
					<h4><a href="{FORUMS_SECTIONS_ROW_URL}">{FORUMS_SECTIONS_ROW_TITLE}</a></h4>
					<!-- IF {FORUMS_SECTIONS_ROW_DESC} -->
					<p class="small">{FORUMS_SECTIONS_ROW_DESC}</p>
					<!-- ENDIF -->
					<ul class="subforums">
						<!-- BEGIN: SUBSECTION -->
						<li>{PHP.R.forums_icon_subforum}{FORUMS_SECTIONS_ROW_TITLE}</li>
						<!-- END: SUBSECTION -->
					</ul>
				</td>
				<td class="centerall">
						{FORUMS_SECTIONS_ROW_LASTPOST}<br />
						{FORUMS_SECTIONS_ROW_LASTPOSTDATE} {FORUMS_SECTIONS_ROW_LASTPOSTER}
				</td>
				<td class="centerall">{FORUMS_SECTIONS_ROW_TOPICCOUNT}</td>
				<td class="centerall">{FORUMS_SECTIONS_ROW_POSTCOUNT}</td>
				<td class="centerall">{FORUMS_SECTIONS_ROW_ACTIVITY}</td>
			</tr>
			<!-- END: SECTION -->

		</tbody>
		<!-- END: CAT -->

	</table>
	<!-- END: FORUMS_SECTIONS -->
	<p class="paging"><span class="a1"><a href="plug.php?e=search&amp;tab=frm">{PHP.L.forums_searchinforums}</a></span><span class="a1"><a href="plug.php?e=forumstats">{PHP.L.Statistics}</a></span><span class="a1"><a href="forums.php?n=markall" rel="nofollow">{PHP.L.forums_markasread}</a></span></p>
</div>

<div class="block">
	<h2 class="tags">{PHP.L.Tags}</h2>
			{FORUMS_SECTIONS_TAG_CLOUD}
</div>

<!-- END: MAIN -->