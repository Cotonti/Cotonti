<?php
/*
Copyright (c) 2009 Ronnie Garcia, Travis Nickels

This file is part of Uploadify v1.6.2

Permission is hereby granted, free of charge, to any person obtaining a copy
of Uploadify and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

UPLOADIFY IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
define('SED_CODE', TRUE);
require_once('../../datas/config.php');
mysql_connect($cfg['mysqlhost'], $cfg['mysqluser'], $cfg['mysqlpassword']);
mysql_select_db($cfg['mysqldb']);
$userid = (isset($_POST['userid'])) ? $_POST['userid'] : '999';

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_GET['folder'] . '/';
	$targetFile = str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];
	$fileParts  = pathinfo($_FILES['Filedata']['name']);
	$fileExt = $fileParts['extension'];
	
	mkdir(str_replace('//','/',$targetPath), 0755, true);
	move_uploaded_file($tempFile,$targetFile);
	
	mysql_query("INSERT INTO $db_pfs (pfs_userid, pfs_date, pfs_file, pfs_extension, pfs_folderid, pfs_size) VALUES ('$userid', '".time()."', '".$_FILES['Filedata']['name']."', '$fileExt', '0', '0')");

}

?>