<?php

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net
-----------------------
Seditio language pack
Language : English (code:en)
Localization done by : Neocrome
-----------------------
[BEGIN_SED]
File=system/lang/en/message.lang.php
Version=110
Updated=2006-aug-16
Type=Lang
Author=Neocrome
Description=Language messages
[END_SED]
==================== */

$L['msg_Message'] = "Message";
$L['msg_Error'] = "Error";
$L['msg_Warning'] = "Warning";
$L['msg_Security'] = "Security";
$L['msg_System'] = "System";

/* ======== Users ======== */

$L['msg100_0'] = "User not logged, access to profile denied";
$L['msg100_1'] = "Only registered and logged users can display their profile!";
$L['msg101_0'] = "User not logged";
$L['msg101_1'] = "No need, you're not logged.";
$L['msg102_0'] = "User logged out";
$L['msg102_1'] = "Done, you're logged out.";
$L['msg104_0'] = "User logged";
$L['msg104_1'] = "Welcome back ".$usr['name'].", you're now logged in.";
$L['msg105_0'] = "Registration done (1st step)";
$L['msg105_1'] = "Please check your mailbox in few minutes,<br />and please confirm the registration process<br />by clicking the URL in the body of the message ...<br />Until this, your account is marked as 'Inactive' in the user list.";
$L['msg106_0'] = "Registration completed";
$L['msg106_1'] = "Welcome, your account is now valid and activated.<br />You're now able to login with your password.";
$L['msg109_0'] = "User deleted";
$L['msg109_1'] = "Done, user deleted.";
$L['msg113_0'] = "Profile updated";
$L['msg113_1'] = "Done, changes applied to your account.";
$L['msg117_0'] = "Registration disabled";
$L['msg117_1'] = "Registration for new users is disabled.";
$L['msg118_0'] = "Registration done (1st step)";
$L['msg118_1'] = "Your account is currently inactive,<br />the site administrator will need to activate it before you can log in.<br />You will receive another email when this has occured.";
$L['msg151_0'] = "Login failed (wrong name or password)";
$L['msg151_1'] = "Error, the user name you provided isn't in the database or the password do not match !";
$L['msg152_0'] = "Login failed (account isn't activated)";
$L['msg152_1'] = "Error, your account is registered but not activated yet.";
$L['msg153_0'] = "Login failed (user banned)";
$L['msg153_1'] = "Error, your account is banned.";
$L['msg157_0'] = "Wrong validation URL";
$L['msg157_1'] = "This validation URL isn't valid.";

/* ======== General ======== */

$L['msg300_0'] = "New submission";
$L['msg300_1'] = "Ok, this item is now recorded in the database.<br />A moderator will check it as soon as possible,<br />Thanks !";

/* ======== Error Pages ======== */

$L['msg400_0'] = "Bad Syntax";
$L['msg400_1'] = "There is a bad syntax in your request";
$L['msg401_0'] = "Unauthorized";
$L['msg401_1'] = "The URL you've requested requires a correct username and password.<br />Either you entered an incorrect username/password, or your browser doesn't support this feature.";
$L['msg403_0'] = "Forbidden";
$L['msg403_1'] = "You do not have permission to retrieve the URL or link you requested.<br />Please inform the administrator of the referring page, if you think this was a mistake.";
$L['msg404_0'] = "Not Found";
$L['msg404_1'] = "The requested object or URL  was not found on this server. <br />The link you followed is either outdated, inaccurate, or the server has been instructed not to let you access the page.";
$L['msg500_0'] = "Internal Server Error";
$L['msg500_1'] = "The server encountered an internal error or misconfiguration and was unable to complete your request. <br />Please contact the administrator and inform them of the time the error occurred, and anything you might have done that may have caused the error.";

/* ======== Private messages ======== */

$L['msg502_0'] = "Private message sent";
$L['msg502_1'] = "Done, your private message was successfully sent.<br />Click ";
$L['msg502_2'] = "here";
$L['msg502_3'] = " to go back to private messages or to send a new PM.";

/* ======== Forums ======== */

$L['msg602_0'] = "Section locked";
$L['msg602_1'] = "This section is locked.";
$L['msg603_0'] = "Topic locked";
$L['msg603_1'] = "This topic is locked.";

/* ======== System ======== */

$L['msg900_0'] = "Under construction";
$L['msg900_1'] = "Page not yet done, come back later please.";
$L['msg904_0'] = "System pages for administrator eyes only";
$L['msg904_1'] = "You cannot list system pages with your level.";
$L['msg907_0'] = "Plug-in not loaded";
$L['msg907_1'] = "An error occured while attempting to load this plug-in, file(s) missing ?";
$L['msg911_0'] = "Language file missing";
$L['msg911_1'] = "An error occured while attempting to check this language pack.";
$L['msg915_0'] = "Error !";
$L['msg915_1'] = "At least 1 field is empty.";
$L['msg916_0'] = "Database updated";
$L['msg916_1'] = "Done, database successfully updated.<br />Affected entries : $num";
$L['msg930_0'] = "Access denied";
$L['msg930_1'] = "You're not allowed to do this.";
$L['msg940_0'] = "Section disabled";
$L['msg940_1'] = "This section of the website is disabled.";
$L['msg950_0'] = "Error";
$L['msg950_1'] = "An error occured, maybe a wrong URL ?";

/* ======== Overall  ======== */

$L['msgredir'] = "Redirecting...";

?>
