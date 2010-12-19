<!-- BEGIN: POLL_VIEW -->
{POLL_FORM_BEGIN}
<!-- BEGIN: POLLTABLE -->
<p><label>{POLL_INPUT}{POLL_OPTIONS}</label></p>
<!-- END: POLLTABLE -->
<p>{POLL_FORM_BUTTON}</p>
{POLL_FORM_END}
<!-- END: POLL_VIEW -->

<!-- BEGIN: POLL_VIEW_VOTED -->
<!-- BEGIN: POLLTABLE -->
<strong>{POLL_OPTIONS}</strong> {POLL_PER}% <span class="lightgray">({POLL_COUNT})</span>
<div class="bar_back"><div class="bar_front" style="width:{POLL_PER}%"></div></div>
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
<p class="centerall" style="margin-top:10px"><strong>{POLL_VOTERS} {PHP.themelang.ratings.Votes}</strong> {PHP.L.polls_since} {POLL_SINCE_SHORT}</p>
<!-- END: POLL_VIEW_VOTED -->

<!-- BEGIN: POLL_VIEW_DISABLED -->
<!-- BEGIN: POLLTABLE -->
{POLL_OPTIONS}
<!-- END: POLLTABLE -->
<p class="red">{PHP.L.rat_registeredonly}</p>
<!-- END: POLL_VIEW_DISABLED -->

<!-- BEGIN: POLL_VIEW_LOCKED -->
<!-- BEGIN: POLLTABLE -->
<strong>{POLL_OPTIONS}</strong> {POLL_PER}% <span class="lightgray">({POLL_COUNT})</span>
<div class="bar_back"><div class="bar_front" style="width:{POLL_PER}%"></div></div>
<!-- END: POLLTABLE -->
<p class="centerall" style="margin-top:10px"><strong>{POLL_VOTERS} {PHP.themelang.ratings.Votes}</strong> {PHP.L.polls_since} {POLL_SINCE_SHORT}</p>
<!-- END: POLL_VIEW_LOCKED -->

<!-- BEGIN: INDEXPOLLS -->
<!-- BEGIN: POLL -->
<h5><a href="{IPOLLS_URL}">{IPOLLS_TITLE}</a></h5>
{IPOLLS_FORM}
<p>&nbsp;<br />&nbsp;</p>
<span class="comb">
<a href="polls.php?id={IPOLLS_ID}#com" class="commv"><span>{PHP.themelang.comments.comments}</span></a>
</span>
<!-- END: POLL -->
<!-- END: INDEXPOLLS -->