<!-- BEGIN: POLL_VIEW -->
	<div id = "poll_{POLL_ID}">
		<form action="{POLL_FORM_URL}" method="post" id="poll_form_{POLL_ID}" class="ajax post-poll_{POLL_ID};polls.php;mode=ajax">
			<input type="hidden" name="poll_id" value="{POLL_ID}" />
			<table class="cells">
				<!-- BEGIN: POLLTABLE -->
				<tr>
					<td class="width40"><label>{POLL_INPUT}{POLL_OPTIONS}</label></td>
					<td class="centerall width40">
						<div class="bar_back">
							<div class="bar_front" style="width:{POLL_PER}%;"></div>
						</div>
					</td>
					<td class="centerall width10">{POLL_PER}%</td>
					<td class="centerall width10">{POLL_COUNT}</td>
				</tr>
				<!-- END: POLLTABLE -->
				<tr>
					<td class="valid" colspan="4">
						<input type="submit" class="submit" value="{PHP.L.polls_Vote}" title="{PHP.L.polls_Vote}" />
					</td>
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
			<p>{POLL_VOTERS} {PHP.L.Date} {POLL_SINCE}</p>
		</form>
	</div>
<!-- END: POLL_VIEW -->

<!-- BEGIN: POLL_VIEW_VOTED -->
	<table class="cells">
<!-- BEGIN: POLLTABLE -->
		<tr>
			<td class="width40">{POLL_OPTIONS}</td>
			<td class="centerall width40">
					<div class="bar_back">
						<div class="bar_front" style="width:{POLL_PER}%;"></div>
					</div>
			</td>
			<td class="centerall width10">{POLL_PER}%</td>
			<td class="centerall width10">{POLL_COUNT}</td>
		</tr>
<!-- END: POLLTABLE -->
		<tr>
			<td class="strong valid" colspan="4">{PHP.L.polls_alreadyvoted}</td>
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
	<p>{POLL_VOTERS} {PHP.L.Date} {POLL_SINCE}</p>
<!-- END: POLL_VIEW_VOTED -->

<!-- BEGIN: POLL_VIEW_LOCKED -->
	<table class="cells">
<!-- BEGIN: POLLTABLE -->
		<tr>
			<td class="width40">{POLL_OPTIONS}</td>
			<td class="centerall width40">
				<div class="bar_back">
					<div class="bar_front" style="width:{POLL_PER}%;"></div>
				</div>
			</td>
			<td class="centerall width10">{POLL_PER}%</td>
			<td class="centerall width10">{POLL_COUNT}</td>
		</tr>
<!-- END: POLLTABLE -->
		<tr>
			<td class="strong valid" colspan="4">{PHP.L.polls_locked}</td>
		</tr>
	</table>
	<p>{POLL_VOTERS} {PHP.L.Date} {POLL_SINCE}</p>
<!-- END: POLL_VIEW_LOCKED -->

<!-- BEGIN: POLL_VIEW_DISABLED -->
	<table class="cells">
<!-- BEGIN: POLLTABLE -->
		<tr><td>{POLL_OPTIONS}</td></tr>
<!-- END: POLLTABLE -->
		<tr><td>{PHP.L.rat_registeredonly}</td></tr>
	</table>
<!-- END: POLL_VIEW_DISABLED -->

<!-- BEGIN: MAIN -->

<!-- BEGIN: POLLS_VIEW -->
<h2 class="polls">{POLLS_TITLE}</h2>
	{POLLS_FORM}
<!--	{PHP.L.Comments}: {POLLS_COMMENTS}{POLLS_COMMENTS_DISPLAY}-->
<!-- END: POLLS_VIEW -->

<!-- BEGIN: POLLS_VIEWALL -->
	<h2 class="polls">{PHP.L.polls_viewarchives}</h2>
	<table class="cells">
<!-- BEGIN: POLL_ROW -->
		<tr>
			<td class="width15">{POLL_DATE}</td>
			<td class="width75"><a href="{POLL_HREF}">{POLL_TEXT}</a></td>
			<td class="width10">{POLLS_COMMENTS}</td>
		</tr>
<!-- END: POLL_ROW -->
<!-- BEGIN: POLL_NONE -->
		<tr>
			<td class="centerall">{PHP.L.None}</td>
		</tr>
<!-- END: POLL_NONE -->
	</table>
<!-- END: POLLS_VIEWALL -->

{FILE ./themes/nemesis/warnings.tpl}

<!-- END: MAIN -->