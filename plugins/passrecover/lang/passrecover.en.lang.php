<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
[BEGIN_SED]
File=plugins/passrecover/lang/passrecover.en.lang.php
Version=110
Updated=2006-jun-05
Type=Plugin.standalone
Author=Neocrome
Description=
[END_SED]
==================== */


$L['plu_title'] = "Password recovery";

$L['plu_explain1'] = "1. Enter your email below";
//$L['plu_explain2'] = "2. You will receive a message with an emergency link, click it to log in";
//$L['plu_explain3'] = "3. Then go in your profile, and set yourself a new password";

$L['plu_explain2'] = "2. You will receive a message with an emergency link, click it to reset your password"; // N-0.0.2
$L['plu_explain3'] = "3. After confirming twice your password reset demand, system will create a random password and send it to your inbox."; // N-0.0.2

$L['plu_explain4'] = "If you emptied the email field in your profile, you won't be able to recover your password.<br />In this case, please contact the webmaster by email.";
$L['plu_mailsent'] = "Done, please check your mailbox in few minutes, and click the emergency link.<br />Then follow instructions.";

$L['plu_mailsent2'] = "Password reset. Please check your mailbox in few minutes to gather your new password."; // N-0.0.2

$L['plu_youremail'] = "Your email : ";
$L['plu_request'] = "Request";

$L['plu_email1'] = "You are receiving this email because you have (or someone pretending to be you has) requested an emergency link to log in at a site powered by the Seditio engine. If you did not request this email then please ignore it, if you keep receiving it please contact the site administrator.\n\nYou may now reset your password with the link below :"; // N-0.0.2
$L['plu_email2'] = "Your password changed as you demanded, please change your password as soon as possible and delete this email.\n\nYour new password :"; // N-0.0.2


?>
