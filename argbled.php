<?
global $cmdinifile;
global $ini_array;	

$GLOBALS['cmdinifile']= "/var/www/rgbled/rgbledcmd.ini";

if(isset($_POST['ss']))
{

switch(strtolower($_POST['ss']))
{
	case "status":
		$result = shell_exec('sudo /var/www/rgbled/rgbledd status');
		echo $result;
		break;
	case "start":
		if(isset($_POST['vid']))
		{
			$ex = "sudo /var/www/rgbled/rgbledd start " .$_POST['vid'];
			$result = shell_exec($ex);
		}else{
			$result = shell_exec('sudo /var/www/rgbled/rgbledd start');
		}
		$result = shell_exec('sudo /var/www/rgbled/rgbledd status');
		echo $result;
		break;
	case "stop":
		$result = shell_exec('sudo /var/www/rgbled/rgbledd stop');
		$result = shell_exec('sudo /var/www/rgbled/rgbledd status');
		echo $result;
		break;
	case "restart":
		$result = shell_exec('sudo /var/www/rgbled/rgbledd restart');
		$result = shell_exec('sudo /var/www/rgbled/rgbledd status');
		echo $result;
		break;	
	case "fade":
		readcmdini($GLOBALS['cmdinifile']);
		$GLOBALS['cmdini_array']['command']['cmd'] = "fade";
		echo $GLOBALS['cmdini_array']['command']['cmd'];
		write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
		break;
	case "off":
		readcmdini($GLOBALS['cmdinifile']);
		$GLOBALS['cmdini_array']['command']['cmd'] = "stop";
		echo $GLOBALS['cmdini_array']['command']['cmd'];
		write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
		break;
	case "color":
		readcmdini($GLOBALS['cmdinifile']);
		$GLOBALS['cmdini_array']['command']['cmd'] = "color";
		$GLOBALS['cmdini_array']['color']['r'] = $_POST['red'];
		$GLOBALS['cmdini_array']['color']['g'] = $_POST['green'];
		$GLOBALS['cmdini_array']['color']['b'] = $_POST['blue'];
		write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
		echo "COLOR ";
		break;
	case "strobe":
		readcmdini($GLOBALS['cmdinifile']);
		$GLOBALS['cmdini_array']['command']['cmd'] = "strobe";
		$GLOBALS['cmdini_array']['color']['r'] = $_POST['red'];
		$GLOBALS['cmdini_array']['color']['g'] = $_POST['green'];
		$GLOBALS['cmdini_array']['color']['b'] = $_POST['blue'];
		write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
		echo "STROBE ";
		break;

	case "red":
		readcmdini($GLOBALS['cmdinifile']);
		$GLOBALS['cmdini_array']['command']['cmd'] = "color";
		$GLOBALS['cmdini_array']['color']['r'] = "10";
		$GLOBALS['cmdini_array']['color']['g'] = "0";
		$GLOBALS['cmdini_array']['color']['b'] = "0";
		write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
		echo "RED ";
		break;
	case "green":
		readcmdini($GLOBALS['cmdinifile']);
		$GLOBALS['cmdini_array']['command']['cmd'] = "color";
		$GLOBALS['cmdini_array']['color']['r'] = "0";
		$GLOBALS['cmdini_array']['color']['g'] = "10";
		$GLOBALS['cmdini_array']['color']['b'] = "0";
		write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
		echo "GREEN ";
		break;
	case "blue":
		readcmdini($GLOBALS['cmdinifile']);
		$GLOBALS['cmdini_array']['command']['cmd'] = "color";
		$GLOBALS['cmdini_array']['color']['r'] = "0";
		$GLOBALS['cmdini_array']['color']['g'] = "0";
		$GLOBALS['cmdini_array']['color']['b'] = "10";
		write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
		echo "BLUE ";
		break;
	case "peri":
		readcmdini($GLOBALS['cmdinifile']);
		$GLOBALS['cmdini_array']['command']['cmd'] = "color";
		$GLOBALS['cmdini_array']['color']['r'] = "2";
		$GLOBALS['cmdini_array']['color']['g'] = "3";
		$GLOBALS['cmdini_array']['color']['b'] = "9";
		write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
		echo "PERI";
		break;
	case "white":
		readcmdini($GLOBALS['cmdinifile']);
		$GLOBALS['cmdini_array']['command']['cmd'] = "white";
		$GLOBALS['cmdini_array']['white']['onoff'] = '1';
		$GLOBALS['cmdini_array']['white']['pwr'] = $_POST['pwr'] / 10;
		write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
		echo "WHITE " .$GLOBALS['cmdini_array']['white']['pwr'];
		break;

	case "whiteoff":
		readcmdini($GLOBALS['cmdinifile']);
		$GLOBALS['cmdini_array']['command']['cmd'] = "white";
		$GLOBALS['cmdini_array']['white']['onoff'] = '1';
		$GLOBALS['cmdini_array']['white']['pwr'] = '0';
		write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
		echo "WHITE " .$GLOBALS['cmdini_array']['white']['pwr'];
		break;

	case "purple":
		readcmdini($GLOBALS['cmdinifile']);
		$GLOBALS['cmdini_array']['command']['cmd'] = "color";
		$GLOBALS['cmdini_array']['color']['r'] = "8";
		$GLOBALS['cmdini_array']['color']['g'] = "0";
		$GLOBALS['cmdini_array']['color']['b'] = "10";
		write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
		echo "PURPLE ";
		break;



}

}else{
echo "NOTHING";
}

/*
Remember to change the visudo to allow www-data to run commands as root


# Allow members of the www-data group to execute commands in a certian director$
%www-data ALL=NOPASSWD: /home/www/cgi-bin/temp1d
%www-data ALL=NOPASSWD: /root/bin/templogger/tempset.ini
%www-data ALL=NOPASSWD: /usr/bin/motion
%www-data ALL=NOPASSWD: /root/bin/motiond

*/

//'*******************************************************************************
function readcmdini($file)
{
$GLOBALS['cmdini_array'] = parse_ini_file($file,true);
}
//'*******************************************************************************


//'*******************************************************************************
function write_ini_file($assoc_arr, $path, $has_sections=TRUE) { 
    $content = ""; 
    if ($has_sections) { 
        foreach ($assoc_arr as $key=>$elem) { 
            $content .= "[".$key."]\n"; 
            foreach ($elem as $key2=>$elem2) { 
                if(is_array($elem2)) 
                { 
                    for($i=0;$i<count($elem2);$i++) 
                    { 
                        $content .= $key2."[] = \"".$elem2[$i]."\"\n"; 
                    } 
                } 
                else if($elem2=="") $content .= $key2." = \n"; 
                else $content .= $key2." = \"".$elem2."\"\n"; 
            } 
        } 
    } 
    else { 
        foreach ($assoc_arr as $key=>$elem) { 
            if(is_array($elem)) 
            { 
                for($i=0;$i<count($elem);$i++) 
                { 
                    $content .= $key2."[] = \"".$elem[$i]."\"\n"; 
                } 
            } 
            else if($elem=="") $content .= $key2." = \n"; 
            else $content .= $key2." = \"".$elem."\"\n"; 
        } 
    } 

    if (!$handle = fopen($path, 'w')) { 
	fclose($handle); 
        return false; 
    } 
    if (!fwrite($handle, $content)) { 
	fclose($handle);         
	return false; 
    } 
	fclose($handle); 
	return true; 
}
//'*******************************************************************************

?>



