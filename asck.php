<?
//Remove 4 bottom lines before launch - Steggy
ini_set('display_errors', 1); 
ini_set('log_errors', 1); 
ini_set('error_log', dirname(__FILE__) . '/itrequest/error_log.txt'); 
error_reporting(E_ALL);

global $cmdinifile;
global $ini_array;
$app = "sudo /var/www/rgbled/rgbledclient.php";	

if(isset($_GET['ss']))
{
	$result = shell_exec($app ." -stop");
	echo $result;
}

if(isset($_POST['ss']))
{
//echo $_POST['ss']; 
switch(strtolower($_POST['ss']))
{
	case "status":
		$result = shell_exec('pgrep rgbledsck.php');
		if($result != 0 )
		{
			echo "Running";
		}else{
			echo "Not Running";
		}
		break;
	case "-stop":
	case "-help":
	case "-f":
	case "-fade":
	case "-strobe":
	case "-wigwag":
	case "-temp":
	case "-xmas":
		$result = shell_exec($app ." " .strtolower($_POST['ss']));
		echo $result;
		break;
	case "-red":
			$result = shell_exec($app ." -c 10,0,0");
			echo $result;	
			break;
	case "-green":
			$result = shell_exec($app ." -c 0,10,0");
			echo $result;	
			break;
	case "-blue":
			$result = shell_exec($app ." -c 0,0,10");
			echo $result;	
			break;	
	case "-purple":
			$result = shell_exec($app ." -c 10,0,3");
			echo $result;	
			break;
	case "-peri":
			$result = shell_exec($app ." -c 5,5,10");
			echo $result;	
			break;
	case "-y":
			$result = shell_exec($app ." -y " .$_POST['pwr'] / 10);
			break;							
	case "-color":
	case "-c":
		//echo "in case";
		$cstring = $app ." -c " .$_POST['red'] ."," .$_POST['green'] ."," .$_POST['blue'];
		//echo $cstring;
		$result = shell_exec($cstring);
		echo $result;
		break;	
}		
}

?>



