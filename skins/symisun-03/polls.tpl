<!-- BEGIN: POLL_VIEW -->

{POLL_FORM_BEGIN}
<!-- BEGIN: POLLTABLE -->
<p><label class="remember radio">{POLL_INPUT}<strong>{POLL_OPTIONS}</strong></label></p>
<p>{POLL_PER}% <span class="lightgray">({POLL_COUNT})</span></p>
<div class="bar_back">
  <div class="bar_front" style="width:{POLL_PER}%"></div>
</div>
<!-- END: POLLTABLE -->
<p style="margin-top:10px"><strong>{POLL_VOTERS} {PHP.skinlang.ratings.Votes}</strong> {PHP.L.polls_since} {POLL_SINCE_SHORT}</p>
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
<p style="margin-top:10px"><strong>{POLL_VOTERS} {PHP.skinlang.ratings.Votes}</strong> {PHP.L.polls_since} {POLL_SINCE_SHORT}</p>
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
<p style="margin-top:10px"><strong>{POLL_VOTERS} {PHP.skinlang.ratings.Votes}</strong> {PHP.L.polls_since} {POLL_SINCE_SHORT}</p>
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
  <div class="breadcrumb">{PHP.skinlang.list.bread}: <a href="index.php">{PHP.L.Home}</a><a href="polls.php">{PHP.L.Polls}</a> {PHP.L.Poll} #{PHP.id}</div>
  <h2>{POLLS_TITLE}</h2>
  {POLLS_FORM}</div>
<div id="right">
  <h3><a href="polls.php?id={PHP.id}#com">{PHP.L.Comments}</a></h3>
  <!-- BEGIN: POLLS_EXTRA -->
  <h3>{POLLS_VIEWALL}</h3>
  <!-- END: POLLS_EXTRA -->
  &nbsp; </div>
<!-- END: POLLS_VIEW -->
<!-- BEGIN: POLLS_VIEWALL -->
<div id="content">
  <div class="padding20 noimg admin">
    <div class="breadcrumb">{PHP.skinlang.list.bread}: <a href="index.php">{PHP.L.Home}</a><a href="polls.php">{PHP.L.Polls}</a></div>
    <h1>{PHP.skinlang.polls.Allpolls}</h1>
		<!-- BEGIN: POLL_ROW -->
		{POLL_DATE} <a href="{POLL_HREF}">{POLL_TEXT}</a> {POLLS_COMMENTS}<br />
		<!-- END: POLL_ROW -->
<!-- END: POLLS_VIEWALL -->
    <br class="clear" />
    <hr />
    {POLLS_COMMENTS_DISPLAY} </div>
</div>
<br class="clear" />

<!-- END: MAIN -->