<!-- BEGIN: MAIN -->

	<div class="col3-2 first">
		<div class="block">
			<h2 class="message"><a href="plug.php?e=contact">{PHP.L.contact_title}</a></h2>

			{FILE ./themes/nemesis/warnings.tpl}

<!-- BEGIN: FORM -->
			<form action="{CONTACT_FORM_SEND}" method="post" name="contact_form">
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

	<div class="col3-1">
		<div class="block">
			<h2 class="message">{PHP.L.contact_subtitle}</h2>
			<p><strong>Phone:</strong> +375 (29) 774 3589</p>
			<p><strong>E-mail:</strong> <a href="mailto:mail@seditio.by">mail@seditio.by</a></p>
		</div>
	</div>

<!-- END: MAIN -->