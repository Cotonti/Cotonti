<!-- BEGIN: MAIN -->

{POLLS_HEADER1}

<link href="skins/{PHP.skin}/{PHP.skin}.css" type="text/css" rel="stylesheet">

{POLLS_HEADER2}

<div class="block">

<!-- BEGIN: POLLS_VIEW -->

<div id="title">

	{POLLS_TITLE}

</div>

<div id="main">

	{POLLS_RESULTS}

	<p>
		{POLLS_VOTERS} {PHP.skinlang.polls.voterssince} {POLLS_SINCE}<br />
		{PHP.skinlang.polls.Comments} {POLLS_COMMENTS}{POLLS_COMMENTS_DISPLAY}
	</p>

</div>

<!-- END: POLLS_VIEW -->

<!-- BEGIN: POLLS_VIEWALL -->

<div id="title">

	{PHP.skinlang.poll.Allpolls}

</div>

<div id="main">

	{POLLS_LIST}

</div>

<!-- END: POLLS_VIEWALL -->

<!-- BEGIN: POLLS_EXTRA -->

<div class="block">

	{POLLS_EXTRATEXT}<br />{POLLS_VIEWALL}

</div>

<!-- END: POLLS_EXTRA -->

</div>

{POLLS_FOOTER}

<!-- END: MAIN -->