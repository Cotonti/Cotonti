<?php
/**
 * Sends mail with standard PHP mail()
 *
 * @global $cfg
 * @param string $fmail Recipient
 * @param string $subject Subject
 * @param string $body Message body
 * @param string $headers Message headers
 * @param string $additional_parameters Additional parameters passed to sendmail
 * @return bool
 */
function sed_mail($fmail, $subject, $body, $headers='', $additional_parameters = null)
{
	global $cfg;

	if (empty($fmail))
	{
		return(FALSE);
	}
	else
	{
		$sitemaintitle = mb_encode_mimeheader($cfg['maintitle'], 'UTF-8', 'B', "\n");

		$headers = (empty($headers)) ? "From: \"".$sitemaintitle."\" <".$cfg['adminemail'].">\n"."Reply-To: <".$cfg['adminemail'].">\n" : $headers;
		$headers .= "Message-ID: <".md5(uniqid(microtime()))."@".$_SERVER['SERVER_NAME'].">\n";

		$body .= "\n\n".$cfg['maintitle']." - ".$cfg['mainurl']."\n".$cfg['subtitle'];
		$headers .= "Content-Type: text/plain; charset=UTF-8\n";
		$headers .= "Content-Transfer-Encoding: 8bit\n";
		$subject = mb_encode_mimeheader($subject, 'UTF-8', 'B', "\n");
		if (ini_get('safe_mode'))
		{
			mail($fmail, $subject, $body, $headers);
		}
		else
		{
			mail($fmail, $subject, $body, $headers, $additional_parameters);
		}
		sed_stat_inc('totalmailsent');
		return(TRUE);
	}
}

?>