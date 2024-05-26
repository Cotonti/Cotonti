<!-- BEGIN: MAIN -->
<div class="block button-toolbar">
	<a href="{PHP|cot_url('admin', 'm=config&n=edit&o=plug&p=contact')}" class="button">{PHP.L.Configuration}</a>
</div>

{FILE "{PHP.cfg.system_dir}/admin/tpl/warnings.tpl"}

<div class="block">
	<table class="cells">
		<tr>
			<td class="coltop w-10">{PHP.L.Date}</td>
			<td class="coltop w-15">{PHP.L.User}</td>
			<td class="coltop w-50">{PHP.L.Message}</td>
			<td class="coltop w-25">{PHP.L.Action}</td>
		</tr>
		<!-- BEGIN: DATA -->
		<tr>
			<td class="textcenter {CONTACT_ODDEVEN}">{CONTACT_DATE}
				<!-- IF {CONTACT_VAL} == val -->
				<br/><span style="color:#900">[ {PHP.L.contact_shortnew} ]</span>
				<!-- ENDIF -->
			</td>
			<td class="{CONTACT_ODDEVEN}">{CONTACT_USER}<br/>{CONTACT_EMAIL}</td>
			<td class="{CONTACT_ODDEVEN}">{CONTACT_TEXTSHORT}</td>
			<td class="centerall {CONTACT_ODDEVEN}">
				<a href="{CONTACT_VIEWLINK}" class="button special" title="{PHP.L.View}">{PHP.L.Open}</a>
				<!-- IF {CONTACT_VAL} == val -->
				<a href="{CONTACT_READLINK}" class="button confirm" title="{PHP.L.contact_markread}">{PHP.L.contact_read}</a>
				<!-- ELSE -->
				<a href="{CONTACT_UNREADLINK}" class="button" title="{PHP.L.contact_markunread}">{PHP.L.contact_unread}</a>
				<!-- ENDIF -->
				<a href="{CONTACT_DELLINK}" class="button">{PHP.L.Delete}</a>
			</td>
		</tr>
		<!-- END: DATA -->
		<!-- IF !{TOTAL_ENTRIES} -->
		<tr>
			<td class="centerall" colspan="4">{PHP.L.None}</td>
		</tr>
		<!-- ENDIF -->
	</table>
	<!-- IF {TOTAL_ENTRIES} -->
	<p class="paging">
		{PREVIOUS_PAGE}{PAGINATION}{NEXT_PAGE}
		<span>{PHP.L.Total}: {TOTAL_ENTRIES}, {PHP.L.Onpage}: {ENTRIES_ON_CURRENT_PAGE}</span>
	</p>
	<!-- ENDIF -->
</div>

<!-- BEGIN: VIEW -->
<div class="block">
	<h2>
		{PHP.L.contact_view} #{CONTACT_ID}
		(<!-- IF {CONTACT_SUBJECT} -->{CONTACT_SUBJECT}<!-- ELSE -->{PHP.L.contact_nosubject}<!-- ENDIF -->)
	</h2>
	<a name="view"></a>
	<div class="wrapper">
		<form action="{CONTACT_FORM_SEND}" method="post" name="contact_form">
			<table class="cells">
				<tr>
					<td class="w-15">{PHP.L.Username}:</td>
					<td class="w-85">{CONTACT_USER}</td>
				</tr>
				<tr>
					<td>{PHP.L.Date}:</td>
					<td>{CONTACT_DATE}</td>
				</tr>
				<tr>
					<td>{PHP.L.Email}:</td>
					<td>{CONTACT_EMAIL} </td>
				</tr>
				<tr>
					<td>{PHP.L.Subject}:</td>
					<td><!-- IF {CONTACT_SUBJECT} -->{CONTACT_SUBJECT}<!-- ELSE -->{PHP.L.contact_nosubject}<!-- ENDIF --></td>
				</tr>
				<tr>
					<td>{PHP.L.Message}:</td>
					<td>{CONTACT_TEXT}</td>
				</tr>
				<!-- BEGIN: EXTRAFLD -->
				<tr>
					<td>{CONTACT_EXTRAFLD_TITLE}</td>
					<td>{CONTACT_EXTRAFLD}</td>
				</tr>
				<!-- END: EXTRAFLD -->
				<!-- IF {CONTACT_REPLY} -->
				<tr style="color:#900;">
					<td>{PHP.L.contact_sent}:</td>
					<td>{CONTACT_REPLY}</td>
				</tr><!-- ENDIF -->
				<tr>
					<td>{PHP.L.Reply}:</td>
					<td>{CONTACT_FORM_TEXT}</td>
				</tr>
				<tr>
					<td colspan="2" class="valid">
						<button type="submit">{PHP.L.Submit}</button>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<!-- END: VIEW -->
<!-- END: MAIN -->