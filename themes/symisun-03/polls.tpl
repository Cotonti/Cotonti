<!-- BEGIN: POLL_VIEW -->
	<div id = "poll_{POLL_ID}">
		<form action="{POLL_FORM_URL}" method="post" id="poll_form_{POLL_ID}" class="ajax post-poll_{POLL_ID};{PHP|cot_url('polls')};mode=ajax">
			<input type="hidden" name="poll_id" value="{POLL_ID}" />
			<table class="cells">
				<!-- BEGIN: POLLTABLE -->
				<tr>
					<td class="width40"><label>{POLL_INPUT}{POLL_OPTIONS}</label></td>
				</tr>
				<!-- END: POLLTABLE -->
				<tr>
					<td class="valid" colspan="4">
						<button type="submit" title="{PHP.L.polls_Vote}">{PHP.L.polls_Vote}</button>
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
			<p>{POLL_VOTERS} {PHP.L.polls_votes}<br /> {PHP.L.Date} {POLL_SINCE}</p>
		</form>
	</div>
<!-- END: POLL_VIEW -->

<!-- BEGIN: POLL_VIEW_VOTED -->
<!-- BEGIN: POLLTABLE -->
<strong>{POLL_OPTIONS}</strong> {POLL_PER}% <span class="lightgray">({POLL_COUNT})</span>
<div class="bar_back">
  <div class="bar_front" style="width:{POLL_PER}%"></div>
</div>
<!-- END: POLLTABLE -->
<script type="text/javascript">
	function anim(){
		$(".bar_front").each(function(){
			var percentage = $(this).width();
			if (percentage!=""){$(this).width(0).animate({width: percentage}, "slow");}
		});
	}
	anim();
</script>
<p style="margin-top:10px"><strong>{POLL_VOTERS} {PHP.L.Votes}</strong> {PHP.L.polls_since} {POLL_SINCE_SHORT}</p>
<p class="red">{PHP.L.polls_alreadyvoted}</p>
<!-- END: POLL_VIEW_VOTED -->
<!-- BEGIN: POLL_VIEW_LOCKED -->
<p>&nbsp;</p>
<!-- BEGIN: POLLTABLE -->
<strong>{POLL_OPTIONS}</strong> {POLL_PER}% <span class="lightgray">({POLL_COUNT})</span>
<div class="bar_back">
  <div class="bar_front" style="width:{POLL_PER}%"></div>
</div>
<!-- END: POLLTABLE -->
<p style="margin-top:10px"><strong>{POLL_VOTERS} {PHP.L.Votes}</strong> {PHP.L.polls_since} {POLL_SINCE_SHORT}</p>
<p class="red">{PHP.L.polls_alreadyvoted}</p>
<!-- END: POLL_VIEW_LOCKED -->
<!-- BEGIN: POLL_VIEW_DISABLED -->
<!-- BEGIN: POLLTABLE -->
{POLL_OPTIONS}
<!-- END: POLLTABLE -->
<p class="red">{PHP.L.rat_registeredonly}</p>
<!-- END: POLL_VIEW_DISABLED -->







<!-- BEGIN: MAIN -->

		
<!-- BEGIN: POLLS_VIEW -->
	<div id="content">
    	<div class="padding20">	
<div id="left">
	<h1>{POLLS_TITLE}</h1>
	<p class="breadcrumb">{PHP.themelang.list.bread}: <a href="{PHP|cot_url('index')}">{PHP.L.Home}</a> {PHP.cfg.separator} <a href="{PHP|cot_url('polls')}">{PHP.L.Polls}</a> {PHP.cfg.separator} {PHP.L.Poll} #{PHP.id}</p>
	{POLLS_FORM}	
</div>
<div id="right">
	<h3><a href="{PHP.id|cot_url('polls','id=$this')}#com">{PHP.L.comments_comments}</a></h3>
	<h3><a href="{PHP|cot_url('polls','id=viewall')}">{PHP.L.polls_viewarchives}</a></h3>
	&nbsp;
</div>
            <br class="clear" />
            <hr />
	{POLLS_COMMENTS_DISPLAY}
</div>

</div>
<!-- END: POLLS_VIEW -->


<!-- BEGIN: POLLS_VIEWALL -->
	<div id="content">
    	<div class="padding20">	
<div id="left" style="margin-right:25px" class="noimg">
	<h1>{PHP.L.polls_viewarchives}</h1>
	<p class="breadcrumb">{PHP.themelang.list.bread}: <a href="{PHP|cot_url('index')}">{PHP.L.Home}</a> {PHP.cfg.separator} <a href="{PHP|cot_url('polls')}">{PHP.L.Polls}</a></p>
	<!-- BEGIN: POLL_ROW -->
	{POLL_DATE|cot_date('date_full', $this)} &nbsp; <a href="{POLL_HREF}">{POLL_TEXT}</a> <br />
	<!-- END: POLL_ROW -->
	<!-- BEGIN: POLL_NONE -->
		<td class="centerall">{PHP.L.None}
	<!-- END: POLL_NONE -->
</div>
</div></div>
<!-- END: POLLS_VIEWALL -->

<br class="clear" />

<!-- END: MAIN -->