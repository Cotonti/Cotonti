<?PHP
/*
|****		Cotonti Engine					****|
|****		Copyright Cotonti 2008				****|
|****		http://www.cotonti.com/			****|
*/
/*
|****		Security Defines  Check			****|
*/
if (!defined('COTONTI_CORE')) { header("Location: /"); }
/*
|****		File Information					****|
*/
$file['name'] 		= "Functions";
$file['path']		= "/system/";
$file['filename']	= "functions.php";
$file['version']	= "0.0.1";
$file['updated']	= "04-08-08";
$file['type']		= "core";
/*
|****		Common Vars					****|
*/
unset($common);
$common['uri'] 		= $_SERVER['REQUEST_URI'];
$common['ip'] 		= $_SERVER['REMOTE_ADDR'];
$common['usrid']	= $usr['id'];
$common['usrname']	= $usr['name'];
$common['time'] 	= $sys['time']

/*
|****		Functions						****|
*/
/*
|****		cot_die						****|
|****		Interrupts any action giving an error		****|
|****		$message details of why it died			****|
|****		$type type to die as				****|
*/
function cot_die($message="Unkown Error", $type)
	{
		switch(strtolower($type))
			{
				case "fatal":
					$txt_out = "<strong><a href=\"".$cfg['mainurl']."\">".$cfg['maintitle']."</a></strong><br />";
					$txt_out .= @date('m-d-Y H:i').'<br />'.$message;
					cot_log($message, "security");
					die($txt_out);
				break;
				default:
					cot_log($message, "security")
					cot_message($message, "die");
				break;
			}
		return(FALSE);
	}
/*
|****		cot_filter						****|
|****		Filter/Validate data type			****|
|****		$type type to filter as				****|
|****		$data the data to be filtered			****|
|****		$length to limit to (optional)			****|
*/
function cot_filter($type, $data, $length=0)
	{
		$error = FALSE;
		$data = trim($data);
		if($length > 0)
			{ $data = substr($data, 0, $length); }
		switch($type)
			{
				case "ALP":
					$data_test = preg_replace('/[^a-zA-Z0-9]/', '', $data);
					if($data == $data_test)
						{ $data_out = $data_test; }
					else{ $error = TRUE; }
				break;
				case "ARR":
					$data_out = $data;
				break;
				case "EMAIL":
					$data_test = (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$",$data)) ? FALSE : TRUE;
					if($data_tes)
						{ $data_out = $data; }
					else{ $error = TRUE; }
				break;
				case "HTML":
					$data_out = $data;
				break;
				case "INT":
					if(is_numeric($data) == TRUE && floor($data) == $data)
						{ $data_out = $data; }
					else{ $error = TRUE; }
				break;
				case "NUM":
					if(is_numeric($data) == TRUE)
						{ $data_out = $data; }
					else{ $error = TRUE; }
				break;
				case "OUT":
					$data_out = preg_replace('/&#([0-9]{2,4});/is','&&#35$1;',$data);
					$data_out = str_replace(
					array("{",		"<",	">" ,	"$",		"'",		"\"",		"\\",		"&amp;",		"&nbsp;"),
					array("&#123;",	"&lt;",	"&gt;",	"&#036;",	"&#039;",	"&quot;",	"&#92;",	"&amp;amp;",	"&amp;nbsp;"), $data_out);
				break;
				case "PSW":
					$data_test = preg_replace('/[^a-zA-Z0-9]/', '', $data);
					$data_test = substr($data_test, 0, 32);
					if($data == $data_test)
						{ $data_out = $data_test; }
					else{ $error = TRUE; }
				break;
				case "TXT":
					$data_out = str_replace("<", "&lt;", $data); // Needs Improvment
				break;
				default:
					cot_log(sprintf($L['cot_filter_error_type'], $method), "error");
					cot_die(sprintf($L['cot_filter_error_type'], $method), "error");
				break;
			}
		if($error)
			{
				return array("", cot_filter("OUT", $data));
			}
		else{ return array($data_out, FALSE); }
	}
/*
|****		cot_import						****|
|****		Import  and filter data				****|
|****		$name  field to import				****|
|****		$method post/get					****|
|****		$type data type to import as			****|
|****		$length to limit to (optional)			****|
*/
function cot_import($name, $method, $type, $length=0, $die=FALSE)
	{
		switch($method)
			{
				case "G":
					$data = $_GET[$name];
				break;
					case "P":
					$data = $_POST[$name];
				break;
					case "C":
					$data = $_COOKIE[$name];
				break;
				default:
					cot_log(sprintf($L['cot_import_error_method'], $method), "error");
					cot_die(sprintf($L['cot_import_error_method'], $method), "error");
				break;
			}
		list($data, $error) = cot_filter_text($type, $data, $length);
		
		if($error)
			{
				cot_log(sprintf($L['cot_import_error_filter'], $method, $error), "error");
				if($die)
					{ cot_die(sprintf($L['cot_import_error_filter'], $method, $error), "error"); }
			}
		else{ return $data; }
	}
/*
|****		cot_log						****|
|****		Log an admin message				****|
|****		$message message to be logged		****|
|****		$type type of log					****|
*/
function cot_log($message, $type)
	{
		global $common, $db;
		cot_query("INSERT into `".$db['logs']."` (`log_date`, `log_ip`, `log_usrid`, `log_usrname`, `log_type`, `log_message`, `log_uri`) VALUES ('".$common['time']."', '".$common['ip']."', '".$common['usrid']."', '".$common['usrname']."', '".cot_sql_prep($type)."', '".cot_sql_prep($message)."', '".cot_sql_prep($common['request_uri'])."')");
		return;
	}
/*
|****		cot_message					****|
|****		Redirects to an message page			****|
|****		$message message to be shown			****|
|****			for additional info			****|
|****		$type type of error message			****|
*/
function cot_message($message, $type)
	{
		// Improve Me later
		header("Location: message.php?id=".$type."&e=".base64_encode($message));
		exit;
	}
/*
|****		cot_urlmask					****|
|****		Generates a Friendly URL based on a mask	****|
|****		$data various data to be used generating	****|
|****			the url					****|
|****		$mask the mask of the url to be used		****|
|****		$url the url neded to be generated?		****|
*/
function cot_urlmask($data, $mask, $url)
	{
		global $cfg;
		// Improve Me Later
	}
?>