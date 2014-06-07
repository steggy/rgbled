<?
//Remove 4 bottom lines before launch - Steggy
ini_set('display_errors', 1); 
ini_set('log_errors', 1); 
ini_set('error_log', dirname(__FILE__) . '/itrequest/error_log.txt'); 
error_reporting(E_ALL);

global $cmdinifile;
global $ini_array;
	

if(isset($_GET['ss']))
{
	$result = shell_exec('sudo /home/steggy/bin/RGBled/rgbledclient.php -stop');
	echo $result;
}

if(isset($_POST['ss']))
{
//echo $_POST['ss']; 
switch(strtolower($_POST['ss']))
{
	case "stop":
		$result = shell_exec('sudo /home/steggy/bin/RGBled/rgbledclient.php -stop');
		echo $result;
		break;
	case "-help":
		$result = shell_exec('sudo /home/steggy/bin/RGBled/rgbledclient.php -h');
		echo $result;
		break;
	case "-red":
			$result = shell_exec('sudo /home/steggy/bin/RGBled/rgbledclient.php -c 10,0,0');
			echo $result;	
			break;
	case "-color":
	case "-c":
		//echo "in case";
		$cstring = "sudo /home/steggy/bin/RGBled/rgbledclient.php -c " .$_POST['red'] ."," .$_POST['green'] ."," .$_POST['blue'];
		//echo $cstring;
		$result = shell_exec($cstring);
		echo $result;
		break;	
}		
}

?>



