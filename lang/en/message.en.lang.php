<?php
/**
 * English Language File for the Message Module (message.en.lang.php)
 *
 * @package Cotonti
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL.');

$L['msg_Message'] = 'Message';
$L['msg_Error'] = 'Error';
$L['msg_Warning'] = 'Warning';
$L['msg_Security'] = 'Security';
$L['msg_System'] = 'System';

$L['msgredir'] = 'Redirecting...';

/**
 * Account-Related Messages
 */

$L['msg100_title'] = 'User not logged, access to profile denied';
$L['msg100_body'] = 'Only registered and logged users can display their profile!';

$L['msg101_title'] = 'User not logged';
$L['msg101_body'] = 'No need to, you\'re not logged.';

$L['msg102_title'] = 'User logged out';
$L['msg102_body'] = 'Done, you\'re logged out.';

$L['msg105_title'] = 'Registration done (1st step)';
$L['msg105_body'] = 'Please check your mailbox in a few minutes.<br />Confirm the registration process<br />by clicking the URL in the body of the message.<br />Until this, your account is marked as &quot;inactive&quot; in the user list.';

$L['msg106_title'] = 'Registration completed';
$L['msg106_body'] = 'Welcome, your account is now valid and activated.<br />You\'re now able to login with your password.';

$L['msg109_title'] = 'User deleted';
$L['msg109_body'] = 'Done, user deleted.';

$L['msg117_title'] = 'Registration disabled';
$L['msg117_body'] = 'Registration for new users is disabled.';

$L['msg118_title'] = 'Registration done (1st step)';
$L['msg118_body'] = 'Your account is currently inactive,<br />the site administrator will need to activate it before you can log in.<br />You will receive another email when this has been completed.';

$L['msg151_title'] = 'Login failed (wrong name or password)';
$L['msg151_body'] = 'Error, the user name you provided isn\'t in the database or the password does not match!';

$L['msg152_title'] = 'Login failed (account isn\'t activated)';
$L['msg152_body'] = 'Error, your account is registered but not yet activated.';

$L['msg153_title'] = 'Login failed (user banned)';
$L['msg153_body'] = 'Error, your account is banned.';

$L['msg154_title'] = 'Password recovery failed (email not found)';
$L['msg154_body'] = 'Error, the email you provided isn\'t in the database!';

$L['msg157_title'] = 'Wrong validation URL';
$L['msg157_body'] = 'This validation URL isn\'t valid.';

/**
 * Redirection Messages
 */

$L['msg300_title'] = 'New submission';
$L['msg300_body'] = 'Ok, this item is now recorded in the database.<br />A moderator will check it as soon as possible.<br />Thanks!';

/**
 * Client Error Messages
 */

$L['msg400_title'] = '400 - Bad File';
$L['msg400_body'] = 'Your browser (or proxy) sent a request that this server could not understand.';

$L['msg401_title'] = '401 - Authorization Required';
$L['msg401_body'] = 'This server could not verify that you are authorized to access the specified URL <br />You either supplied the wrong credentials (e.g., bad password), or your browser doesn\'t understand how to supply the credentials required.';

$L['msg403_title'] = '403 - Forbidden';
$L['msg403_body'] = 'You don\'t have permission to access the requested directory or URL that you requested.<br />Please inform the administrator of the referring page, if you think this was a mistake.';

$L['msg404_title'] = '404 - Not Found';
$L['msg404_body'] = 'The requested object or URL was not found on this server. <br />The link you followed is either outdated, inaccurate, or the server has been instructed not to let you access the page.';

/**
 * Server Error Messages
 */

$L['msg500_title'] = '500 Internal Server Error';
$L['msg500_body'] = 'The server encountered an internal error or misconfiguration and was unable to complete your request. <br />Please contact the administrator and inform them of the time the error occurred, and anything you might have done that may have caused the error.';
$L['msg503_title'] = '503 Service Temporarily Unavailable';
$L['msg503_body'] = 'The page you requested is temporarily unavailable for technical reasons.<br />Please try again later or contact the site administrator.';

/**
 * Forum Messages
 */

$L['msg602_title'] = 'Section locked';
$L['msg602_body'] = 'This section is locked';

$L['msg603_title'] = 'Topic locked';
$L['msg603_body'] = 'This topic is locked';

/**
 * System Messages
 */

$L['msg900_title'] = 'Under construction';
$L['msg900_body'] = 'Page not yet done, come back later please.';

$L['msg904_title'] = 'System pages for administrator eyes only';
$L['msg904_body'] = 'You cannot list system pages with your level.';

$L['msg907_title'] = 'Plug-in not loaded';
$L['msg907_body'] = 'An error occured while attempting to load this plug-in, file(s) missing?';

$L['msg911_title'] = 'Language file missing';
$L['msg911_body'] = 'An error occured while attempting to check this language pack.';

$L['msg915_title'] = 'Error!';
$L['msg915_body'] = 'At least 1 field is empty.';

$L['msg916_title'] = 'Database updated';
$L['msg916_body'] = 'Done, database successfully updated.<br />Affected entries : $num';

$L['msg920_title'] = 'Confirmation required';
$L['msg920_body'] = 'Are you sure you want to perform this action?';

$L['msg930_title'] = 'Access denied';
$L['msg930_body'] = 'You\'re not allowed to do this.';

$L['msg940_title'] = 'Section disabled';
$L['msg940_body'] = 'This section of the website is disabled.';

$L['msg950_title'] = 'Request parameters error';
$L['msg950_body'] = 'One of the request parameters is invalid or has expired. Please go back and try submitting the form again.';

$L['msg951_title'] = 'Session expired';
$L['msg951_body'] = 'Your session is no longer valid. Please try again.';
