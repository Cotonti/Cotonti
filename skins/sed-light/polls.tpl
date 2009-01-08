<!-- BEGIN: MAIN -->

	<!-- BEGIN: POLLS_VIEW -->
	<div class="mboxHD">{POLLS_TITLE}</div>
	<div class="mboxBody">

		{POLLS_RESULTS}

		<p>
			{POLLS_VOTERS} {PHP.skinlang.polls.voterssince} {POLLS_SINCE}<br />
			{PHP.skinlang.polls.Comments} {POLLS_COMMENTS}{POLLS_COMMENTS_DISPLAY}
		</p>

	</div>
	<!-- END: POLLS_VIEW -->

	<!-- BEGIN: POLLS_VIEWALL -->
	<div class="popupTitle">{PHP.skinlang.poll.Allpolls}</div>
	<div class="mboxBody">

		{POLLS_LIST}

	</div>
	<!-- END: POLLS_VIEWALL -->

	<!-- BEGIN: POLLS_EXTRA -->
	<div class="block">{POLLS_EXTRATEXT}<br />{POLLS_VIEWALL}</div>
	<!-- END: POLLS_EXTRA -->


<!-- END: MAIN -->