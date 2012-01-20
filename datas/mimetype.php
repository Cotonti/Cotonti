<?php
/**
 * MIMETYPE
 *
 * @package Cotonti
 * @version 0.9.0
 * @author Cotonti Team
 * @copyright Copyright (c) Cotonti Team 2008-2012
 * @license BSD
 */

//Format is as follows
//$mime_type[1][2] = array(3, 4, 5, 6, 7, 8)
//1 - Extension - File extension
//2 - Order - If more than 1 of the same ext, the order to be executed in  (if no need for a specific order leave empty)
//3 - Mime Type - Associated mime type
//4 - Search Pattern - Pattern to search for
//5 - Is a hex pattern? - True or False (0 or 1)
//6 - Starting Byte - Byte to start the check
//7 - Byte length - How many bytes to check from the start
//8 - Is disabled? - True or False (0 or 1)
$mime_type['rar'][]		= array('application/x-rar', 'Rar!', '0', '0', '4', '0');
$mime_type['zip'][0]	= array('application/zip', '504B03041400', '1', '0', '6', '0');
$mime_type['zip'][1]	= array('application/zip', '504B03040A00', '1', '0', '6', '0');
$mime_type['gz'][]		= array('application/x-gzip', '1F8B0800', '1', '0', '4', '0');
$mime_type['tar.gz'][]	= array('application/x-gzip', '1F8B0808', '1', '0', '4', '0');
$mime_type['pdf'][1]	= array('application/pdf', '!<PDF>!', '0', '0', '7', '0');
$mime_type['pdf'][2]	= array('application/pdf', 'PDF', '0', '1', '3', '0');
$mime_type['avi'][0]	= array('video/avi', 'AVI', '0', '8', '3', '0');
$mime_type['avi'][1]	= array('video/avi', 'RIFF', '0', '0', '4', '0');
$mime_type['qt'][0]		= array('video/quicktime', 'ftypqt', '0', '4', '6', '0');
$mime_type['qt'][1]		= array('video/quicktime', 'moov', '0', '24', '4', '0');
$mime_type['mov'][0]	= array('video/quicktime', 'ftypqt', '0', '4', '6', '0');
$mime_type['mov'][1]	= array('video/quicktime', 'moov', '0', '24', '4', '0');
$mime_type['mpg'][0]	= array('video/mpeg', '000001BA', '1', '0', '4', '0');
$mime_type['mpg'][1]	= array('video/mpeg', '000001B3', '1', '0', '4', '0');
$mime_type['mpeg'][0]	= array('video/mpeg', '000001BA', '1', '0', '4', '0');
$mime_type['mpeg'][1]	= array('video/mpeg', '000001B3', '1', '0', '4', '0');
$mime_type['ogg'][]		= array('application/ogg', 'OggS', '0', '0', '4', '0');
$mime_type['mp3'][]		= array('audio/mpeg', 'ID3', '0', '0', '3', '0');
$mime_type['wav'][0]	= array('audio/x-wav', 'WAVEfmt', '0', '8', '7', '0');
$mime_type['wav'][1]	= array('audio/x-wav', 'RIFF', '0', '0', '4', '0');
$mime_type['wmv'][]		= array('video/x-ms-wmv', '3026B2758E66CF11A6D900AA0062CE6C', '1', '0', '16', '0');

?>