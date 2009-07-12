<!-- BEGIN: POLL_VIEW -->
{POLL_FORM_BEGIN}
<table>
<!-- BEGIN: POLLTABLE -->
<tr>
	<td><label>{POLL_INPUT}{POLL_OPTIONS}</label></td>
</tr>
<!-- END: POLLTABLE -->
<tr>
	<td>{POLL_FORM_BUTTON}</td>
</tr>
</table>
{POLL_FORM_END}
<!-- END: POLL_VIEW -->


<!-- BEGIN: POLL_VIEW_VOTED -->
<table style="width:100%;">
<!-- BEGIN: POLLTABLE -->
<tr>
	<td>{POLL_OPTIONS}</td>
	<td align="right">{POLL_PER}% ({POLL_COUNT})</td>
</tr>
<tr>
	<td colspan="2" align="right">
		<div style="width:180px; ">
			<div class="bar_back">
				<div class="bar_front" id="pr_{POLL_PER}"></div>
			</div>
		</div>
	</td>
</tr>
<!-- END: POLLTABLE -->
</table>
<script type="text/javascript">
	function anim(){
		$(".bar_front").each(function(){
			var percentage = Math.floor(($(this).attr("id").replace('pr_','')*180)/100)+'px';
			if ($(this).attr("id")!=""){$(this).css({width:"0"}).animate({width: percentage}, "slow");}
			$(this).attr("id","");
		});
	}
	anim();
</script>
<div><div id="pfsBack">{PHP.skinlang.page.Date} {POLL_SINCE_SHORT}</div> {PHP.skinlang.ratings.Votes} {POLL_VOTERS} </div>
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
<table style="width:100%;">
<!-- BEGIN: POLLTABLE -->
<tr>
	<td>{POLL_OPTIONS}</td>
	<td align="right">{POLL_PER}% ({POLL_COUNT})</td>
</tr>
<tr>
	<td colspan="2" align="right">
		<div style="width:180px; ">
			<div class="bar_back">
				<div class="bar_front" style="width:{POLL_PER}%;"></div>
			</div>
		</div>
	</td>
</tr>
<!-- END: POLLTABLE -->
</table>
<div><div id="pfsBack">{PHP.skinlang.page.Date} {POLL_SINCE_SHORT}</div> {PHP.skinlang.ratings.Votes} {POLL_VOTERS} </div>
<!-- END: POLL_VIEW_LOCKED -->


<!-- BEGIN: INDEXPOLLS -->
<!-- BEGIN: POLL -->
<h5><a href="{IPOLLS_URL}">{IPOLLS_TITLE}</a></h5>
{IPOLLS_FORM}
<div style="text-align: right;">{PHP.skinlang.comments.Comment} {IPOLLS_COMMENTS}</div>
<hr />
<!-- END: POLL -->

<!-- BEGIN: ERROR -->
<div class="error">{IPOLLS_ERROR}</div>
<!-- END: ERROR -->

<p style="text-align: center;">{IPOLLS_ALL}</p>
<!-- END: INDEXPOLLS -->
