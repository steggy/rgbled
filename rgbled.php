#! /usr/bin/php

<?
//Hey you have to turn on pi-blaster
///root/bin/pi-blast/pi-blaster exec it

//include pin settings, fade factor radom wait time - maybe strobe time

global $inifile;
global $ini_array;
global $cmdini_array;
global $cmdinifile;
global $redpin;
global $greenpin;
global $bluepin;
global $whitepin;
global $strobedelay;
global $cmd;
global $count;
global $count2;
global $randcolorpause;
global $fadepause;
global $debugmode;

$GLOBALS['cmd'] = '';
$GLOBALS['count'] = 0;
$GLOBALS['count2'] = 0;

$GLOBALS['inifile']= "/var/www/rgbled/rgbled.ini";
$GLOBALS['cmdinifile'] = "/var/www/rgbled/rgbledcmd.ini";

readini($GLOBALS['inifile']);

//red is pin 22
//green is pin 23
//blue is pin 24
//$red = "22";
//$green = "23";
//$blue = "24";

$rl = '0';
$gl = '0';
$bl = '0';
$orl = '99';
$ogl = '99';
$obl = '99';

if(isset($argv[1]))
{
	switch($argv[1])
	{
		case '-x':
			changecolor(0,0,0);		
			exit;
			break;
		case '-s':
			if(sizeof($argv) >= 6)
			{
				strobe($argv[2],$argv[3],$argv[4],$argv[5],$argv[6]);
				//changecolor(0,0,0);		
				die;
			}else{
				showusage();
				die;
			}
			break;
		case '-r':
			randomlight();	
			break;
		case '-c':
			if(sizeof($argv) >= 4)
			{
				changecolor($argv[2],$argv[3],$argv[4]);
			}else{
				showusage();
				die;
			}
			break;
		case '-D':
			//daemon mode
			maindaemon();
			break;
		case '-B':
			//debug mode
			$GLOBALS['debugmode'] = '1';
			main();
			break;	
		default;
			showusage();
			die;

	}

	
}else{
	showusage();
	die;
}

//'*******************************************************************************
//main is for debug mode
function main()
{
$lcount = 0;
echo date('Y-m-d H:i:s') ."- Started Debug\n";

	//the main process
	while(true)
	{
		if($lcount ==20)
		{
			echo date('Y-m-d H:i:s') ." Debug\n";
			$lcount = 0;
		}
		$lcount++;
		readcmdini($GLOBALS['cmdinifile']);
		//echo $GLOBALS['cmdini_array']['command']['cmd'] ."\n";
		switch(strtolower($GLOBALS['cmdini_array']['command']['cmd']))
		{
			case 'fade':
				echo "In fade\n";
				randomlight();
				break;
			case 'color':
				echo "IN COLOR\n";
				changecolor($GLOBALS['cmdini_array']['color']['r'],$GLOBALS['cmdini_array']['color']['g'],$GLOBALS['cmdini_array']['color']['b']);
				$GLOBALS['cmdini_array']['command']['cmd'] = 'z';
				write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
				break;
			case 'stop':
				changecolor(0,0,0);
				$GLOBALS['cmdini_array']['command']['cmd'] = 'z';
				write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
				break;
			case 'strobe':
				strobeII($GLOBALS['cmdini_array']['color']['r'],$GLOBALS['cmdini_array']['color']['g'],$GLOBALS['cmdini_array']['color']['b']);
				$GLOBALS['cmdini_array']['command']['cmd'] = 'z';
				write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
				break;
			case 'white':
				echo "White\n";
				yardlight();
				break;
			default;
				//echo "THROUGH SWITCH\n";

		}
		sleep(1);
	}
}
//'*******************************************************************************

//'*******************************************************************************
//maindaemon is for deamonized mode
function maindaemon()
{
//redirecting standard out
//Make sure the user running the app has rw on the log file
fclose(STDIN);
fclose(STDOUT);
fclose(STDERR);
$STDIN = fopen('/dev/null', 'r');
$STDOUT = fopen('/var/log/rgbled.log', 'wb');
$STDERR = fopen('/var/log/rgblederror.log', 'wb');

$lcount = 0;
echo date('Y-m-d H:i:s') ."- Started Daemon\n";
//fork the process to work in a daemonized environment
file_put_contents($log, "Status: starting up. \n", FILE_APPEND);
$pid = pcntl_fork();
if($pid == -1){
	file_put_contents($log, "Error: could not daemonize process.n", FILE_APPEND);
	return 1; //error
}
else if($pid){
	return 0; //success
}else{

	//the main process
	while(true)
	{
		if($lcount ==40)
		{
			echo date('Y-m-d H:i:s') ." Daemonized\n";
			$lcount = 0;
		}
		$lcount++;
		readcmdini($GLOBALS['cmdinifile']);
		//echo $GLOBALS['cmdini_array']['command']['cmd'] ."\n";
		switch(strtolower($GLOBALS['cmdini_array']['command']['cmd']))
		{
			case 'fade':
				//echo "In fade\n";
				randomlight();
				break;
			case 'color':
				//echo "IN COLOR\n";
				changecolor($GLOBALS['cmdini_array']['color']['r'],$GLOBALS['cmdini_array']['color']['g'],$GLOBALS['cmdini_array']['color']['b']);
				$GLOBALS['cmdini_array']['command']['cmd'] = 'z';
				write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
				break;
			case 'stop':
				changecolor(0,0,0);
				$GLOBALS['cmdini_array']['command']['cmd'] = 'z';
				write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
				break;
			case 'strobe':
				strobeII($GLOBALS['cmdini_array']['color']['r'],$GLOBALS['cmdini_array']['color']['g'],$GLOBALS['cmdini_array']['color']['b']);
				$GLOBALS['cmdini_array']['command']['cmd'] = 'z';
				write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
				break;
			case 'white':
				yardlight();
				break;
			default;
				//echo "THROUGH SWITCH\n";

		}
		sleep(1);
	}
} //end of fork
}
//'*******************************************************************************

//'*******************************************************************************
function showusage()
{
	echo "Usage: rgbled [option]...\n Using the Raspberry pi as an RGB LED controllor\n";
	echo "Mandatory arguments\n";
	echo "  -h, \t This help\n";
	echo "  -x, \t Turn off all leds\n";
	echo "  -D, \t Daemon mode\n";
	echo "  -B, \t Debug mode\n";
	echo "  -c [.001-10] [.001-10] [.001-10], \t Set and turn on LED - Color values seperated by space.\n";
	echo "  -s [.001-10] [.001-10] [.001-10] [x-duration] [y-count], \t Strobe LED - Color values seperated by space. \n";
	echo "  -r, \t Generate Random colors every x seconds and fade to the next color\n";
	echo "Delay and fade factor are set in the rgbled.ini file\n";		
	echo "Example useage:\n";
	echo "\t rgbled.php -c 10 0 0 (all red)\n";
	echo "\t rgbled.php -c 0 0 .03 (very low blue)\n";		
	echo "\t rgbled.php -c .03 0 .03 (low purple)\n";		
	echo "\t rgbled.php -x (All off)\n";		
	echo "\n\n";
}
//'*******************************************************************************




//'*******************************************************************************
function randomlight()
{
	while(true)
	{		
		//echo(rand(0,10) / 100);
		//echo "\n";

		if($GLOBALS['count'] == 10)
		{
			$GLOBALS['count'] = 0;
			$GLOBALS['count2']++;
		}
		switch($GLOBALS['count2'])
		{
			case 0:
				$rl = rand(0,10);
				$gl = '0';
				$bl = rand(0,10);
				break;
			case 1:
				$rl = rand(0,10);
				$gl = rand(0,10);
				$bl = '0';
				break;
			case 2:
				$rl = '0';
				$gl = rand(0,10);
				$bl = rand(0,10);
				break;
			default;
				$GLOBALS['count2'] = 0;
		}

		/*if(!$orl == '0' && !$ogl == '0' && !$obl == '0')
		{
			//if orl is less then rl count down else count up
		}else{*/
		if ($GLOBALS['debugmode'] == '1') 
		{
			echo "Red = " .$rl ." green = " .$gl ." Blue = " .$bl ."\n\n";
			echo "FADED\n";
		}
		
		fade($rl,$gl,$bl);
		$GLOBALS['count']++;
		sleep($GLOBALS['randcolorpause']);
		readcmdini($GLOBALS['cmdinifile']);
		switch (strtolower($GLOBALS['cmdini_array']['command']['cmd'])) 
		{
			case 'white':
				yardlight();
				break;
			case 'stop':
			case 'color':
				return;
				break;
			default:
				# code...
				break;
		}
		//if(strtolower($GLOBALS['cmdini_array']['command']['cmd']) == 'stop'){return;}
		/*}*/
	}
}
//'*******************************************************************************


//'*******************************************************************************
function fade($r,$g,$b)
{
$stopfade = 0;
$rstop = 0;
$gstop = 0;
$bstop = 0;
$step = .1;
if($r == 0){$rstop = 1;}
if($g == 0){$gstop = 1;}
if($b == 0){$bstop = 1;}

	if($GLOBALS['orl'] == '99')
	{
		$GLOBALS['orl'] = $r;
		$GLOBALS['ogl'] = $g;
		$GLOBALS['obl'] = $b;
		changecolor($r,$g,$b);
	}else{
		//echo updown($GLOBALS['orl'],$r) ."\n";
		$rd = updown($GLOBALS['orl'],$r);
		$gd = updown($GLOBALS['ogl'],$g);
		$bd = updown($GLOBALS['obl'],$b);
		while($stopfade == 0)
		{
			if($GLOBALS['orl'] == $r && $GLOBALS['ogl'] == $g && $GLOBALS['obl'] == $b)
			{
				$stopfade = 1;
			}else{
				if($rd == 0)
				{
					if($GLOBALS['orl'] != $r)
					{
						$GLOBALS['orl'] = $GLOBALS['orl'] - 1;
					}
				}else{
					if($GLOBALS['orl'] != $r)
					{
						$GLOBALS['orl'] = $GLOBALS['orl'] + 1;
					}
					
				}
				if($gd == 0)
				{
					if($GLOBALS['ogl'] != $g)
					{
						$GLOBALS['ogl'] = $GLOBALS['ogl'] - 1;
					}
				}else{
					if($GLOBALS['ogl'] != $g)
					{
						$GLOBALS['ogl'] = $GLOBALS['ogl'] + 1;
					}
					
				}
				if($bd == 0)
				{
					if($GLOBALS['obl'] != $b)
					{
						$GLOBALS['obl'] = $GLOBALS['obl'] - 1;
					}
				}else{
					if($GLOBALS['obl'] != $b)
					{
						$GLOBALS['obl'] = $GLOBALS['obl'] + 1;
					}
					
				}

				//echo "FDADE R = " .$r ." OR = " .$GLOBALS['orl'] ." ";
				//echo "G = " .$g ." OG = " .$GLOBALS['ogl'] ." ";
				//echo "B = " .$b ." OB = " .$GLOBALS['obl'] ."\n";
				changecolor($GLOBALS['orl'],$GLOBALS['ogl'],$GLOBALS['obl']);
			}
		usleep($GLOBALS['fadepause']); //2000000 = 2 sec
		}
		$GLOBALS['orl'] = $r;
		$GLOBALS['ogl'] = $g;
		$GLOBALS['obl'] = $b;

		
	}
}
//'*******************************************************************************

//'*******************************************************************************
function strobe($r,$g,$b,$d,$t)
{
	echo "THIS IS T " .$t ."\n";
	for($i =0; $i < $t; $i++)
	{
		changecolor($r,$g,$b);
		usleep($d/2);
		changecolor(0,0,0);
		usleep($d/2);
	
	}
}
//'*******************************************************************************

//'*******************************************************************************
function strobeII($r,$g,$b)
{
$d=$GLOBALS['ini_array']['strobe']['delay'];	
	while(true)
	{
		changecolor($r,$g,$b);
		usleep($d/2);
		changecolor(0,0,0);
		usleep($d/2);
		readcmdini($GLOBALS['cmdinifile']);
		switch(strtolower($GLOBALS['cmdini_array']['command']['cmd']))
		{
			
			case 'white':
				yardlight();
				break;
			case 'stop':
			case 'color':
				return;
				break;	
		}
		
	
	}
}
//'*******************************************************************************

//'*******************************************************************************
function updown($o,$n)
{
	if($o > $n)
	{
		return 0;
	}else{
		return 1;
	}
}
//'*******************************************************************************

//'*******************************************************************************
function yardlight()
{
	//When debuging without pi-blaster use this function
	//change this function's name to changecolor then change the next function down to changecolorI
	//You will need to reverse this when using pi-blaster
	readcmdini($GLOBALS['cmdinifile']);
	$p = $GLOBALS['cmdini_array']['white']['pwr'];
	$GLOBALS['cmdini_array']['command']['cmd'] = 'z';
	write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
	$outy = "echo \"" .$GLOBALS['whitepin'] ."=" .$p ."\" > /dev/pi-blaster";
	if (!$GLOBALS['debugmode'] == '1') 
	{
		$result = shell_exec($outy);
	}else{
		echo "yardlight\n";
		echo $outy ."\n";
	}
	
	
}
//'*******************************************************************************

//'*******************************************************************************
function debugcolor($r,$g,$b)
{
	//When debuging without pi-blaster use this function
	//change this function's name to changecolor then change the next function down to changecolorI
	//You will need to reverse this when using pi-blaster
	$outr = "echo \"" .$GLOBALS['redpin'] ."=" .$r / 10 ."\" > /dev/pi-blaster";
	$outg = "echo \"" .$GLOBALS['greenpin'] ."=" .$g / 10 ."\" > /dev/pi-blaster";
	$outb = "echo \"" .$GLOBALS['bluepin'] ."=" .$b / 10 ."\" > /dev/pi-blaster";
	//debug without pi-blaster
	echo "\"" .$GLOBALS['redpin'] ."=" .$r / 10 ."\"";
	echo "\"" .$GLOBALS['greenpin'] ."=" .$g / 10 ."\"";
	echo "\"" .$GLOBALS['bluepin'] ."=" .$b / 10 ."\"";
	echo "\n\n";
	
}
//'*******************************************************************************

//'*******************************************************************************
function changecolor($r,$g,$b)
{
	if (!$GLOBALS['debugmode'] == '1') 
	{
		daemoncolor($r,$g,$b);
	}else{
		debugcolor($r,$g,$b);

	}
}
//'*******************************************************************************

//'*******************************************************************************
function daemoncolor($r,$g,$b)
{
	$outr = "echo \"" .$GLOBALS['redpin'] ."=" .$r / 10 ."\" > /dev/pi-blaster";
	$outg = "echo \"" .$GLOBALS['greenpin'] ."=" .$g / 10 ."\" > /dev/pi-blaster";
	$outb = "echo \"" .$GLOBALS['bluepin'] ."=" .$b / 10 ."\" > /dev/pi-blaster";
	$result = shell_exec($outr);
	$result = shell_exec($outg);
	$result = shell_exec($outb);
}
//'*******************************************************************************

//'*******************************************************************************
function readini($file)
{
if (!file_exists($file)) {
	echo "*********************************************\nrgbled.php\nFile not found: " .$file ."\n\n";
	die;
}
$GLOBALS['ini_array'] = parse_ini_file($file,true);
$GLOBALS['redpin'] = $GLOBALS['ini_array']['pins']['red'];
$GLOBALS['greenpin'] = $GLOBALS['ini_array']['pins']['green'];
$GLOBALS['bluepin'] = $GLOBALS['ini_array']['pins']['blue'];
$GLOBALS['whitepin'] = $GLOBALS['ini_array']['pins']['white'];
$GLOBALS['strobedelay'] = $GLOBALS['ini_array']['strobe']['delay'];
$GLOBALS['cmd'] = $GLOBALS['ini_array']['command']['stop'];
$GLOBALS['randcolorpause'] = $GLOBALS['ini_array']['randomcolor']['dur'];
$GLOBALS['fadepause'] = $GLOBALS['ini_array']['randomcolor']['fade'];
/*$GLOBALS['dbhost'] = $ini_array['database']['dbhost'];
$GLOBALS['sname'] = $ini_array['sensor']['name'];
$GLOBALS['samprate'] = $ini_array['sensor']['sample_rate'];*/
}
//'*******************************************************************************


//'*******************************************************************************
function readcmdini($file)
{
$GLOBALS['cmdini_array'] = parse_ini_file($file,true);
//echo "READ CMD INI\n";
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
