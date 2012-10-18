<!-- BEGIN: MAIN -->

	<h2 class="message"><a href="{PHP|cot_url('admin','m=other&amp;p=contact')}">{PHP.L.contact_title}</a></h2>

	{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}

	<table class="cells">
		<tr>
			<td class="coltop width10">{PHP.L.Date}</td>
			<td class="coltop width15">{PHP.L.User}</td>
			<td class="coltop width50">{PHP.L.Message}</td>
			<td class="coltop width25">{PHP.L.Action}</td>
		 </tr>
<!-- BEGIN: DATA -->
		 <tr>
			<td class="textcenter {CONTACT_ODDEVEN}">{CONTACT_DATE}<!-- IF {CONTACT_VAL} == val --><br /><span style="color:#900;">[ {PHP.L.contact_shortnew} ]</span><!-- ENDIF --></td>
			<td class="{CONTACT_ODDEVEN}">{CONTACT_USER}<br />{CONTACT_EMAIL}</td>
			<td class="{CONTACT_ODDEVEN}">{CONTACT_TEXTSHORT}</td>
			<td class="centerall {CONTACT_ODDEVEN}">
<a href="{CONTACT_VIEWLINK}" title="{PHP.L.View}" class="button special">{PHP.L.short_open}</a>
<!-- IF {CONTACT_VAL} == val --><a href="{CONTACT_READLINK}" title="{PHP.L.contact_markread}" class="button confirm">{PHP.L.contact_read}</a>
<!-- ELSE --><a href="{CONTACT_UNREADLINK}" title="{PHP.L.contact_markunread}" class="button">{PHP.L.contact_unread}</a><!-- ENDIF --><a href="{CONTACT_DELLINK}" title="{PHP.L.Delete}" class="button">{PHP.L.Delete}</a>
			</td>
		 </tr>
<!-- END: DATA -->
	</table>
	<p class="paging">{CONTACT_PREV}{CONTACT_PAGINATION}{CONTACT_NEXT}</p>

<!-- BEGIN: VIEW -->
	<h2 class="users">{PHP.L.contact_view} #{CONTACT_ID} (<!-- IF {CONTACT_SUBJECT} -->{CONTACT_SUBJECT}<!-- ELSE -->{PHP.L.contact_nosubject}<!-- ENDIF -->)</h2>
	<form action="{CONTACT_FORM_SEND}" method="post" name="contact_form">
		<table class="cells">
			<tr>
				<td class="width15">{PHP.L.Username}:</td>
				<td class="width85">{CONTACT_USER}</td>
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
<!-- IF {CONTACT_REPLY} --><tr style="color:#900;">
				<td>{PHP.L.contact_sent}:</td>
				<td>{CONTACT_REPLY}</td>
			</tr><!-- ENDIF -->
			<tr>
				<td>{PHP.L.Reply}:</td>
				<td>{CONTACT_FORM_TEXT}</td>
			</tr>
			<tr>
				<td colspan="2" class="valid"><button type="submit">{PHP.L.Submit}</button></td>
			</tr>
		</table>
	 </form>
<!-- END: VIEW -->

<!-- END: MAIN -->