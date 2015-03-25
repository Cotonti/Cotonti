<!-- BEGIN: MAIN -->

		<div class="col">
			<div class="block">
				<h2 class="message"><a href="{PHP|cot_url('plug','e=contact')}">{PHP.L.contact_title}</a></h2>
				<!-- IF {PHP.cfg.plugin.contact.about} -->
				<p>{PHP.cfg.plugin.contact.about}</p>
				<!-- ENDIF -->
				<!-- IF {PHP.cfg.plugin.contact.map} -->
				<p>{PHP.cfg.plugin.contact.map}</p>
				<!-- ENDIF -->
					{FILE "{PHP.cfg.themes_dir}/{PHP.usr.theme}/warnings.tpl"}
<!-- BEGIN: FORM -->
				<form action="{CONTACT_FORM_SEND}" method="post" name="contact_form" enctype="multipart/form-data">
					<table class="flat">
						<tr>
							<td class="width25">{PHP.L.Username}:</td>
							<td class="width75">{CONTACT_FORM_AUTHOR}</td>
						</tr>
						<tr>
							<td>{PHP.L.Email}:</td>
							<td>{CONTACT_FORM_EMAIL} </td>
						</tr>
						<tr>
							<td>{PHP.L.Subject}:</td>
							<td>{CONTACT_FORM_SUBJECT}</td>
						</tr>
						<tr>
							<td>{PHP.L.Message}:</td>
							<td>{CONTACT_FORM_TEXT}</td>
						</tr>
<!-- BEGIN: EXTRAFLD -->
						<tr>
							<td>{CONTACT_FORM_EXTRAFLD_TITLE}:</td>
							<td>{CONTACT_FORM_EXTRAFLD}</td>
						</tr>
<!-- END: EXTRAFLD -->
<!-- BEGIN: CAPTCHA -->
						<tr>
							<td>{CONTACT_FORM_VERIFY_IMG}</td>
							<td>{CONTACT_FORM_VERIFY}</td>
						</tr>
<!-- END: CAPTCHA -->
						<tr>
							<td>&nbsp;</td>
							<td><button type="submit">{PHP.L.Submit}</button></td>
						</tr>
					</table>
				</form>
<!-- END: FORM -->
			</div>
		</div>

<!-- END: MAIN -->