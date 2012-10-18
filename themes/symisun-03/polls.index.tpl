<!-- BEGIN: POLL_VIEW -->
<div id = "poll_{POLL_ID}">
	<form action="{POLL_FORM_URL}" method="post" id="poll_form_{POLL_ID}" class="ajax post-poll_{POLL_ID};{PHP|cot_url('polls')};mode=ajax&amp;poll_theme=index">
		<input type="hidden" name="poll_id" value="{POLL_ID}" />
		<ul>
			<!-- BEGIN: POLLTABLE -->
			<li><label>{POLL_INPUT}{POLL_OPTIONS}</label></li>
			<!-- END: POLLTABLE -->
			<li><button type="submit" title="{PHP.L.polls_Vote}">{PHP.L.polls_Vote}</button></li>
		</ul>
	</form>
</div>
<!-- END: POLL_VIEW -->

<!-- BEGIN: POLL_VIEW_VOTED -->
<table class="main">
	<!-- BEGIN: POLLTABLE -->
	<tr class="small">
		<td>{POLL_OPTIONS}</td>
		<td class="textright">{POLL_PER}% ({POLL_COUNT})</td>
	</tr>
	<tr>
		<td colspan="2">
			<div class="bar_back">
				<div class="bar_front" style="width:{POLL_PER}%;"></div>
			</div>
		</td>
	</tr>
	<!-- END: POLLTABLE -->
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
<p class="small textcenter">{PHP.L.Votes}: {POLL_VOTERS}</p>
<!-- END: POLL_VIEW_VOTED -->

<!-- BEGIN: POLL_VIEW_DISABLED -->
<table>
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

<!-- BEGIN: POLL_VIEW_LOCKED -->
<table>
	<!-- BEGIN: POLLTABLE -->
	<tr>
		<td>{POLL_OPTIONS}</td>
		<td class="textright">{POLL_PER}% ({POLL_COUNT})</td>
	</tr>
	<tr>
		<td colspan="2" class="textright">
			<div class="bar_back">
				<div class="bar_front" style="width:{POLL_PER}%;"></div>
			</div>
		</td>
	</tr>
	<!-- END: POLLTABLE -->
</table>
<p>{PHP.L.Date} {POLL_SINCE_SHORT} {PHP.L.Votes} {POLL_VOTERS} </p>
<!-- END: POLL_VIEW_LOCKED -->

<!-- BEGIN: INDEXPOLLS -->
<!-- BEGIN: POLL -->
<a class="strong" href="{IPOLLS_URL}">{IPOLLS_TITLE}</a>
	{IPOLLS_FORM}
<!-- END: POLL -->
<!-- BEGIN: ERROR -->
<p class="small strong textcenter">{IPOLLS_ERROR}</p>
<!-- END: ERROR -->
<p class="small textcenter"><a href="{IPOLLS_ALL}">{PHP.L.polls_viewarchives}</a></p>
<!-- END: INDEXPOLLS -->