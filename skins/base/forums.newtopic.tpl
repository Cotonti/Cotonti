<!-- BEGIN: MAIN -->

<div id="title">

	{FORUMS_NEWTOPIC_PAGETITLE}

</div>

<div id="subtitle">

	{FORUMS_NEWTOPIC_SUBTITLE}

</div>

<div id="main">

<form action="{FORUMS_NEWTOPIC_SEND}" method="post" name="newtopic">

<table class="cells">

	<tr>
		<td>{PHP.skinlang.forumsnewtopic.Title} {FORUMS_NEWTOPIC_TITLE}</td>
	</tr>

	<tr>
		<td>{PHP.skinlang.forumsnewtopic.Desc} {FORUMS_NEWTOPIC_DESC}</td>
	</tr>

	<!-- BEGIN: PRIVATE -->

	<tr>
		<td>
		{PHP.skinlang.forumsnewtopic.privatetopic} {FORUMS_NEWTOPIC_ISPRIVATE}<br />
		{PHP.skinlang.forumsnewtopic.privatetopic2}
		</td>
	</tr>

	<!-- END: PRIVATE -->

	<tr>
		<td>
		<div style="width:96%;">{FORUMS_NEWTOPIC_TEXTBOXER}</div>
		</td>
	</tr>

	<tr>
		<td style="text-align:center;">
		<input type="submit" value="{PHP.skinlang.forumsnewtopic.Submit}">
		</td>
	</tr>

</table>

</form>

</div>

<!-- END: MAIN -->
