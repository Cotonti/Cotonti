<!-- BEGIN: POLL_VIEW -->

{POLL_FORM_BEGIN}
<!-- BEGIN: POLLTABLE -->
<p><label class="remember radio">{POLL_INPUT}<strong>{POLL_OPTIONS}</strong></label></p>
<p>{POLL_PER}% <span class="lightgray">({POLL_COUNT})</span></p>
<div class="bar_back">
  <div class="bar_front" style="width:{POLL_PER}%"></div>
</div>
<!-- END: POLLTABLE -->
<p style="margin-top:10px"><strong>{POLL_VOTERS} {PHP.themelang.ratings.Votes}</strong> {PHP.L.polls_since} {POLL_SINCE_SHORT}</p>
<p>{POLL_FORM_BUTTON}</p>
<script type="text/javascript">
	function anim(){
		$(".bar_front").each(function(){
			var percentage = $(this).width();
			if (percentage!=""){$(this).width(0).animate({width: percentage}, "slow");}
		});
	}
	anim();
</script>
{POLL_FORM_END}
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
<p style="margin-top:10px"><strong>{POLL_VOTERS} {PHP.themelang.ratings.Votes}</strong> {PHP.L.polls_since} {POLL_SINCE_SHORT}</p>
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
<p style="margin-top:10px"><strong>{POLL_VOTERS} {PHP.themelang.ratings.Votes}</strong> {PHP.L.polls_since} {POLL_SINCE_SHORT}</p>
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
<div id="left">
	<h1>{POLLS_TITLE}</h1>
	<p class="breadcrumb">{PHP.themelang.list.bread}: <a href="index.php">{PHP.L.Home}</a> {PHP.cfg.separator} <a href="polls.php">{PHP.L.Polls}</a> {PHP.cfg.separator} {PHP.L.Poll} #{PHP.id}</p>
	{POLLS_FORM}
   	<hr />
	{POLLS_COMMENTS_DISPLAY}
</div>
</div></div>
<div id="right">
	<h3><a href="polls.php?id={PHP.id}#com">{PHP.L.Comments}</a></h3>
	<!-- BEGIN: POLLS_EXTRA -->
	<h3>{POLLS_VIEWALL}</h3>
	<!-- END: POLLS_EXTRA -->
	&nbsp;
</div>
<!-- END: POLLS_VIEW -->
<!-- BEGIN: POLLS_VIEWALL -->
<div id="left" style="margin-right:25px" class="noimg">
	<h1>{PHP.themelang.polls.Allpolls}</h1>
	<p class="breadcrumb">{PHP.themelang.list.bread}: <a href="index.php">{PHP.L.Home}</a> {PHP.cfg.separator} <a href="polls.php">{PHP.L.Polls}</a></p>
	{POLLS_LIST}
</div>
</div></div>
<!-- END: POLLS_VIEWALL -->

<br class="clear" />

<!-- END: MAIN -->