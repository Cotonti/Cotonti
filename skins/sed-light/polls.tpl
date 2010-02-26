<!-- BEGIN: POLL_VIEW -->
<div id = "poll_{POLL_ID}">
	<form action="{POLL_FORM_URL}" method="post" id="poll_form_{POLL_ID}" class="ajax" title="get-poll_{POLL_ID};polls.php;mode=ajax&poll_skin={PHP.skininput}">
		<input type="hidden" name="poll_id" value="{POLL_ID}" />
		<table class="cells">
			<!-- BEGIN: POLLTABLE -->
			<tr>
				<td><label>{POLL_INPUT}{POLL_OPTIONS}</label></td>
				<td>
					<div style="width:256px;">
						<div class="bar_back">
							<div class="bar_front" style="width:{POLL_PER}%;"></div>
						</div>
					</div>
				</td>
				<td>{POLL_PER}%</td>
				<td>{POLL_COUNT}</td>
			</tr>
			<!-- END: POLLTABLE -->
			<tr>
				<td colspan="4"><input type="submit" class="submit" value="{PHP.L.polls_Vote}" title="{PHP.L.polls_Vote}" /></td>
			</tr>
		</table>
		<script type="text/javascript">
			function anim(){
				$(".bar_front").each(function(){
					var percentage = $(this).width();
					if (percentage!=""){$(this).width(0).animate({width: percentage}, "slow");}
				});
			}
			anim();
		</script>
		<p>{POLL_VOTERS} {PHP.skinlang.polls.voterssince} {POLL_SINCE}</p>
	</form>
</div>
<!-- END: POLL_VIEW -->


<!-- BEGIN: POLL_VIEW_VOTED -->
<table class="cells">
	<!-- BEGIN: POLLTABLE -->
	<tr>
		<td>{POLL_OPTIONS}</td>
		<td>
			<div style="width:256px;">
				<div class="bar_back">
					<div class="bar_front" style="width:{POLL_PER}%;"></div>
				</div>
			</div>
		</td>
		<td>{POLL_PER}%</td>
		<td>{POLL_COUNT}</td>
	</tr>
	<!-- END: POLLTABLE -->
	<tr>
		<td colspan="4">{PHP.L.polls_alreadyvoted}</td>
	</tr>
</table>
<script type="text/javascript">
	function anim(){
		$(".bar_front").each(function(){
			var percentage = $(this).width();
			if (percentage!=""){$(this).width(0).animate({width: percentage}, "slow");}
		});
	}
	anim();
</script>
<p>{POLL_VOTERS} {PHP.skinlang.polls.voterssince} {POLL_SINCE}</p>
<!-- END: POLL_VIEW_VOTED -->

<!-- BEGIN: POLL_VIEW_LOCKED -->
<table class="cells">
	<!-- BEGIN: POLLTABLE -->
	<tr>
		<td>{POLL_OPTIONS}</td>
		<td>
			<div style="width:256px;">
				<div class="bar_back">
					<div class="bar_front" style="width:{POLL_PER}%;"></div>
				</div>
			</div>
		</td>
		<td>{POLL_PER}%</td>
		<td>{POLL_COUNT}</td>
	</tr>
	<!-- END: POLLTABLE -->
	<tr>
		<td colspan="4">{PHP.L.polls_alreadyvoted}</td>
	</tr>
</table>
<p>{POLL_VOTERS} {PHP.skinlang.polls.voterssince} {POLL_SINCE}</p>
<!-- END: POLL_VIEW_LOCKED -->

<!-- BEGIN: POLL_VIEW_DISABLED -->
<table class="cells">
	<!-- BEGIN: POLLTABLE -->
	<tr>
		<td>{POLL_OPTIONS}</td>
	</tr>
	<!-- END: POLLTABLE -->
	<tr>
		<td>{PHP.L.rat_registeredonly}</td>
	</tr>
</table>
<!-- END: POLL_VIEW_DISABLED -->

<!-- BEGIN: MAIN -->
<!-- BEGIN: POLLS_VIEW -->
<div class="mboxHD">{POLLS_TITLE}</div>
<div class="mboxBody">
	{POLLS_FORM}
			{PHP.L.Comments}: {POLLS_COMMENTS}{POLLS_COMMENTS_DISPLAY}
</div>
<!-- END: POLLS_VIEW -->

<!-- BEGIN: POLLS_VIEWALL -->
<div class="mboxHD">{PHP.skinlang.polls.Allpolls}</div>
<div class="mboxBody">
	<table class="cells">
		<!-- BEGIN: POLL_ROW -->
		<tr>
			<td style="width:128px;">{POLL_DATE}</td>
			<td><a href="{POLL_HREF}"> {POLL_TEXT} </a></td>
			<td>{POLLS_COMMENTS}</td>
		</tr>
		<!-- END: POLL_ROW -->
		<!-- BEGIN: POLL_NONE -->
		<tr><td>{PHP.L.None}</td></tr>
		<!-- END: POLL_NONE -->
	</table>
</div>
<!-- END: POLLS_VIEWALL -->

<!-- BEGIN: POLLS_EXTRA -->
<div class="block">{POLLS_EXTRATEXT}<br />{POLLS_VIEWALL}</div>
<!-- END: POLLS_EXTRA -->
<!-- END: MAIN -->