#! /usr/bin/php

<?php
//Hey you have to turn on pi-blaster
///root/bin/pi-blast/pi-blaster exec it

//Set globals
global $address;
global $port;
global $sock;
global $status;
global $debug;
global $stop;
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
global $pid;
global $hourtmr;
global $last;
global $hourct;
global $hourcolor;
global $basedir;
//$GLOBALS['basedir'] = getenv("RGB_LED_HOME");
$GLOBALS['basedir'] = "/var/www/rgbled";

$GLOBALS['cmd'] = '';
$GLOBALS['count'] = 0;
$GLOBALS['count2'] = 0;
$GLOBALS['pid'] = 0;
$GLOBALS['hourct'] = 0;
$GLOBALS['hourtmr'] = 0;
$GLOBALS['last'] = "";
$GLOBALS['hourcolor'] = "";

//$GLOBALS['inifile']= $GLOBALS['basedir'] ."/rgbled.ini";
//$GLOBALS['cmdinifile'] = $GLOBALS['basedir'] ."/rgbledcmd.ini";

$GLOBALS['inifile']= "/var/www/rgbled/rgbled.ini";
$GLOBALS['cmdinifile'] = "/var/www/rgbled/rgbledcmd.ini";

$GLOBALS['debug'] = true;
$GLOBALS['stop'] = false;

$rl = '0';
$gl = '0';
$bl = '0';
$orl = '99';
$ogl = '99';
$obl = '99';
require("/var/www/rgbled/cli_changecolor.php");
require("/var/www/rgbled/cli_readini.php");

register_shutdown_function( 'shutdown');

declare(ticks = 1);

pcntl_signal(SIGINT, function() {
    echo "\n\nCaught SIGINT\n";
    //socket_close($GLOBALS['sock']);
    die;
});


readini($GLOBALS['inifile']);
if (isset($argv[1])) 
{
switch ($argv[1]) {
    case 'r':
        setenvi();
        setsock();
        $GLOBALS['debug'] = true;
        maindebug();
        break;
    case 'D':
        setenvi();
        setsock();
        $GLOBALS['debug'] = false;
        main();
        break;
    default:
        showusage();
        break;
}
}else{
    showusage();
    exit;
}


/*************************************/
function setenvi()
{
    echo "Setting env\n";
    echo putenv("RGBLEDSTROBE=" .$GLOBALS['strobedelay']);
    echo "\nYou set\n";
    echo getenv("RGBLEDSTROBE");
    echo "\nRed Pin\n";
    echo getenv("RGB_LED_HOME");
}
/*************************************/

/*************************************/
function setsock()
{
// Set time limit to indefinite execution
set_time_limit (0);
// Set the ip and port we will listen on
$GLOBALS['address'] = '0.0.0.0';
$GLOBALS['port'] = 9000;
// Create a TCP Stream socket
$GLOBALS['sock'] = socket_create(AF_INET, SOCK_STREAM, 0);
socket_set_option($GLOBALS['sock'], SOL_SOCKET, SO_SNDBUF, 25000);
// Bind the socket to an address/port
socket_bind($GLOBALS['sock'], $GLOBALS['address'], $GLOBALS['port']) or die('Could not bind to address');
//socket_set_nonblock($GLOBALS['sock']);
// Start listening for connections
socket_listen($GLOBALS['sock']);
socket_set_nonblock($GLOBALS['sock']);
}
/*************************************/

/*************************************/
function main()
{
//redirecting output for daemon mode
//redirecting standard out
//Make sure the user running the app has rw on the log file
fclose(STDIN);
fclose(STDOUT);
fclose(STDERR);
$STDIN = fopen('/dev/null', 'r');
$STDOUT = fopen('/var/log/rgbled.log', 'wb');
$STDERR = fopen('/var/log/rgblederror.log', 'wb');
//dont't forget to create these log files
    while (true) 
    {    
        checksock(); 
        usleep(4000);
    }
// Close the master sockets
socket_close($GLOBALS['sock']);

}
/*************************************/

/*************************************/
function maindebug()
{
    $c = 0;
    while (true) 
    {    
        checksock(); 
        //echo "Check " .$c++ ."\n";
        usleep(4000);
    }
// Close the master sockets
socket_close($GLOBALS['sock']);

}
/*************************************/

function checksock()
{
    

    if($GLOBALS['hourtmr'] > 0)
    {
        //echo "Hour timer val " .$GLOBALS['hourtmr'] ."\n";
        flashhour();
    }

    /* Accept incoming requests and handle them as child processes */

    $client = @socket_accept($GLOBALS['sock']);
    //echo "Client " .$client ."\n";
    if (!$client === false) 
    {

    // Read the input from the client &#8211; 1024 bytes

    //$input = socket_read($client, 1024);
    $status = @socket_get_status($client);

    $input = @socket_read($client, 2048);

    // Strip all white spaces from input
    echo "RAW " .$input ."\n";
    

    if($input == '')
    {
        break;
    }
    
    $output = explode(" ", $input);
    
    
        switch (strtolower($output[0])) {
            case 'white':
                $response = "Turn on white\n\n";
                socket_write($client, $response);
                socket_close($client);
                break;
            case '-status':
                //$GLOBALS['status'];
                $response = $GLOBALS['status'] ."\n\n";
                socket_write($client, $response);
                socket_close($client);
                break;
            case '-config':
                $config = showconfig();
                socket_write($client, $config);
                socket_close($client);
                break;
            case '-setstrobe':
                if (isset($output[1])) 
                {
                    $GLOBALS['strobedelay'] = $output[1];
                    $GLOBALS['ini_array']['strobe']['delay'] = $GLOBALS['strobedelay'];
                    write_ini_file($GLOBALS['ini_array'],$GLOBALS['inifile']);
                    $response = "Strobe Duration " .$GLOBALS['strobedelay'] ."\n";
                    socket_write($client, $response,strlen($response));
                    socket_close($client);
                }
                break;
            case '-get':
                    if (isset($output[1])) 
                    {
                        switch(strtolower($output[1]))
                        {
                            case 'sd':
                                    $response = "Strobe Duration " .$GLOBALS['strobedelay'] ."\n";
                                    socket_write($client, $response,strlen($response));
                                    socket_close($client);
                                break;
                            case 'c':
                            case 'C':
                                $response = $GLOBALS['rl'] ."," .$GLOBALS['gl'] ."," .$GLOBALS['bl'];
                                /*$response .= "Red > " .$GLOBALS['rl'];
                                $response .= " Green > " .$GLOBALS['gl'];
                                $response .= " Blue > " .$GLOBALS['bl'] ."\n";*/
                                socket_write($client, $response,strlen($response));
                                socket_close($client);
                                break;
                        }
                    }else{
                        $response = shwhelp();
                        socket_write($client, $response,strlen($response));
                        socket_close($client);
                    }
                break;
            case '-yard':
            case '-y':
                if (isset($output[1])) 
                {
                    yardlight($output[1]);
                }else{
                    $response = "Missing power number 0-10\n";
                    socket_write($client, $response);
                    socket_close($client);
                }
                break; 
            case '-color';
            case '-c';
                echo "In Color Case\n";
                echo "OUTPUT 1 " .$output[1] ."\n";
                if (isset($output[1])) 
                {
                    echo "COLOR SET\n";
                    $colorv = explode(",", $output[1]);
                    echo "SIZE COLORV " .sizeof($colorv);
                    if (sizeof($colorv) == 3) 
                    {
                        $GLOBALS['rl'] = $colorv[0];
                        $GLOBALS['gl'] = $colorv[1];
                        $GLOBALS['bl'] = $colorv[2];
                        $GLOBALS['ini_array']['color']['r']=$GLOBALS['rl'];
                        $GLOBALS['ini_array']['color']['g']=$GLOBALS['gl'];
                        $GLOBALS['ini_array']['color']['b']=$GLOBALS['bl'];
                        $result = write_ini_file($GLOBALS['ini_array'],$GLOBALS['inifile']);
                        killproc();
                        $GLOBALS['status'] = "Solid Color";
                        changecolor($colorv[0],$colorv[1],$colorv[2]);
                        $response = "Color R" .$GLOBALS['rl'] ." G" .$GLOBALS['gl'] ." B" .$GLOBALS['bl'] ."\n";
                        socket_write($client, $response);
                        socket_close($client);
                    }   
                }
                break;
                case '-setcolor';
                echo "In SetColor Case\n";
                if (isset($output[1])) 
                {
                    $colorv = explode(",", $output[1]);
                    if (sizeof($colorv) == 3) 
                    {
                        $response = "Color Set to\n";
                        $response .="R " .$colorv[0] ." G " .$colorv[1] ." B " .$colorv[2];
                        socket_write($client, $response);
                        socket_close($client);
                        //readini($GLOBALS['inifile']);
                        $GLOBALS['rl'] = $colorv[0];
                        $GLOBALS['gl'] = $colorv[1];
                        $GLOBALS['bl'] = $colorv[2];
                        
                        $GLOBALS['ini_array']['color']['r']=$GLOBALS['rl'];
                        $GLOBALS['ini_array']['color']['g']=$GLOBALS['gl'];
                        $GLOBALS['ini_array']['color']['b']=$GLOBALS['bl'];
                        $result = write_ini_file($GLOBALS['ini_array'],$GLOBALS['inifile']);
                        
                    }   
                }
                break;
            
            case 'test':
                $response = "Testing\n\n";
                socket_write($client, $response);
                socket_close($client);
                looptest();
                break;
            case '-christmas':
            case '-xmas':
            case '-chris':
                killproc();
                $GLOBALS['status'] = "Christmas";
                $response = "Christmas\n\n";
                socket_write($client, $response);
                socket_close($client);
                $command =  $GLOBALS['basedir'] .'/rgbchristmas.php' . ' > /dev/null 2>&1 & echo $!; ';
                echo $command;
                $pid = exec($command, $output);
                $GLOBALS['pid'] = $pid;
                break;
            case '-fade':
            case '-f':
                killproc();
                $GLOBALS['status'] = "Fading";
                $response = "Fading\n\n";
                socket_write($client, $response);
                socket_close($client);
                $command =  $GLOBALS['basedir'] .'/rgbfade.php' . ' > /dev/null 2>&1 & echo $!; ';
                echo $command;
                $pid = exec($command, $output);
                $GLOBALS['pid'] = $pid;
                break;    
            case '-strobe':
                $GLOBALS['status'] = "Strobe";
                $GLOBALS['stop'] = TRUE;
                killproc();
                $command =  $GLOBALS['basedir'] .'/rgbstrobe.php' . ' > /dev/null 2>&1 & echo $!; ';
                $pid = exec($command, $output);
                $GLOBALS['pid'] = $pid;
                $response = "Strobing\n\n";
                socket_write($client, $response);
                socket_close($client);
                break;  
            case '-temp':
                $response = gettemp();
                socket_write($client, $response);
                socket_close($client);
                break;    
            case '-wigwag':
                $GLOBALS['stop'] = TRUE;
                killproc();
                if (isset($output[1])) 
                    {
                        $response = "Hour\n";
                        socket_write($client, $response);
                        socket_close($client);
                        flashhour($output[1]);
                        //$scmd = "/rgbwigwag.php 1";
                        //$GLOBALS['status'] = "Hour";

                        break;
                    }else{
                        $scmd = "/rgbwigwag.php";
                        $GLOBALS['status'] = "WigWag";
                        $command =  $GLOBALS['basedir'] .$scmd . ' > /dev/null 2>&1 & echo $!; ';
                        $pid = exec($command, $output);
                        $GLOBALS['pid'] = $pid;
                        $response = "WigWaging\n";
                        socket_write($client, $response);
                        socket_close($client);
                    }
                break;            
            case 'kill':
                $response = "Killing\n\n";
                socket_write($client, $response);
                socket_close($client);
                socket_close($GLOBALS['sock']);
                exit;
                break;    
            case 'stoptest':
                $GLOBALS['stop'] = true;
                break;    
            case '-stop':
                killproc();
                 alloff();
                $response = "Stopping\n";
                socket_write($client, $response);
                socket_close($client);
                $GLOBALS['status'] = "Stop";
                $GLOBALS['stop'] = true;
                break;    
            case "--help":
            case "-help":
            case "--h":
            case "-h":
                $response = shwhelp();
                socket_write($client, $response,strlen($response));
                socket_close($client);
                break;
            default:
                $response = "Default:\nCommand given: " .$output[0] ."\n" .shwhelp();
                socket_write($client, $response);
                socket_close($client);
                break;
        }
    }
    
}
//'*******************************************************************************
function showconfig()
{
    $config = "RGB LED Config\n";
    $config .= "Pins:\n";
    $config .= "  Red " .$GLOBALS['redpin'] ."\n";
    $config .= "  Green " .$GLOBALS['greenpin'] ."\n";
    $config .= "  Blue " .$GLOBALS['bluepin'] ."\n"; 
    return $config;
    
     
}
//'*******************************************************************************

//'*******************************************************************************
function flashhour($s=0)
{
    if($s > 0) //start timer
    {
        $GLOBALS['hourtmr'] = 1;
        $GLOBALS['last'] = $GLOBALS['status'];
        $GLOBALS['hourcolor'] = $GLOBALS['rl'] ."," .$GLOBALS['gl'] ."," .$GLOBALS['bl'];
        switch($s)
        {
            case 1:
                echo "Starting hour\n";
                $scmd = "/rgbwigwag.php";
                $GLOBALS['status'] = "Hour";
                $command =  $GLOBALS['basedir'] .$scmd . ' > /dev/null 2>&1 & echo $!; ';
                $pid = exec($command, $output);
                $GLOBALS['pid'] = $pid;
                break;
            case 2:
                echo "Starting 15\n";
                $scmd = "/rgbstrobe.php";
                $GLOBALS['status'] = "Hour";
                $GLOBALS['rl'] = $GLOBALS['ini_array']['color']['r'] = 0;
                $GLOBALS['gl'] = $GLOBALS['ini_array']['color']['g'] = 10;
                $GLOBALS['bl'] = $GLOBALS['ini_array']['color']['b'] = 0;
                write_ini_file($GLOBALS['ini_array'],$GLOBALS['inifile']);
                //changecolor($GLOBALS['rl'],$GLOBALS['gl'],$GLOBALS['bl']);
                $command =  $GLOBALS['basedir'] .$scmd . ' > /dev/null 2>&1 & echo $!; ';
                $pid = exec($command, $output);
                $GLOBALS['pid'] = $pid;
                break;
            case 3:
                echo "Starting 30\n";
                $scmd = "/rgbstrobe.php";
                $GLOBALS['status'] = "Hour";
                $GLOBALS['rl'] = $GLOBALS['ini_array']['color']['r'] = 0;
                $GLOBALS['gl'] = $GLOBALS['ini_array']['color']['g'] = 0;
                $GLOBALS['bl'] = $GLOBALS['ini_array']['color']['b'] = 10;
                write_ini_file($GLOBALS['ini_array'],$GLOBALS['inifile']);
                echo "red " .$GLOBALS['rl'] ." \n";
                echo "green " .$GLOBALS['gl'] ." \n";
                echo "blue " .$GLOBALS['bl'] ." \n";
                //changecolor($GLOBALS['rl'],$GLOBALS['gl'],$GLOBALS['bl']);
                $command =  $GLOBALS['basedir'] .$scmd . ' > /dev/null 2>&1 & echo $!; ';
                $pid = exec($command, $output);
                $GLOBALS['pid'] = $pid;
                break;
        }
    }else if($GLOBALS['hourct'] > 3000){
        killproc();
        $GLOBALS['hourct'] = 0;
        $GLOBALS['hourtmr'] = 0;
        $oldc = explode(",",$GLOBALS['hourcolor']);
        echo "\nCOLOR " .$oldc[1] ."\n";
        $GLOBALS['rl'] = $GLOBALS['ini_array']['color']['r'] = $oldc[0];;
        $GLOBALS['gl'] = $GLOBALS['ini_array']['color']['g'] = $oldc[1];;
        $GLOBALS['bl'] = $GLOBALS['ini_array']['color']['b'] = $oldc[2];;
        write_ini_file($GLOBALS['ini_array'],$GLOBALS['inifile']);
        readini($GLOBALS['inifile']);

        switch(strtolower($GLOBALS['last']))
        {
            case "fading":
                $command =  $GLOBALS['basedir'] .'/rgbfade.php' . ' > /dev/null 2>&1 & echo $!; ';
                echo $command;
                $pid = exec($command, $output);
                $GLOBALS['pid'] = $pid;
                $GLOBALS['status'] = "Fading";
            break;
            case "strobe":
                $command =  $GLOBALS['basedir'] .'/rgbstrobe.php' . ' > /dev/null 2>&1 & echo $!; ';
                $pid = exec($command, $output);
                $GLOBALS['pid'] = $pid;
                $GLOBALS['status'] = "Srobe";
                break;
            case "solid color":
                changecolor($GLOBALS['rl'],$GLOBALS['gl'],$GLOBALS['bl']);
                $GLOBALS['status'] = "Solid Color";
                break;  
            default;
                  $GLOBALS['status'] = "";
        }
    }
    $GLOBALS['hourct']++;
    //echo "Hour count " .$GLOBALS['hourct'] ."\n";


}
//'*******************************************************************************


//'*******************************************************************************
function gettemp()
{
    $jsonurl = "http://api.openweathermap.org/data/2.5/weather?q=usa,tracy,ca";
    $json = file_get_contents($jsonurl);

    $weather = json_decode($json);
    $kelvin = $weather->main->temp;
    //$celcius = $kelvin - 273.15;
    $k_2_f = (($kelvin - 273.15) * 9 / 5) + 32;
 
    return round($k_2_f,2);
    
     
}
//'*******************************************************************************                

//'*******************************************************************************
function killproc()
{
    if($GLOBALS['pid'] != 0)
        {
            $rus = exec("kill " .$GLOBALS['pid']);
            $GLOBALS['pid'] = 0;
        }
    alloff();
}
//'*******************************************************************************

//'*******************************************************************************
function shutdown()
{
    killproc();
}
//'*******************************************************************************

//'*******************************************************************************
function looptest()
{
    while(true)
    {
        echo "Sleeping....." .date('H:i:s') ."\n";
        checksock();
        if($GLOBALS['stop'])
            {
                $GLOBALS['stop'] = false;
                return;
            }
        sleep(1);
    }
}
//'*******************************************************************************


//'*******************************************************************************
function checkstop()
{
    if($GLOBALS['stop'])
            {
                $GLOBALS['stop'] = FALSE;
                return TRUE;
            }
}
//'*******************************************************************************


//'*******************************************************************************
function yardlight($p)
{
    //When debuging without pi-blaster use this function
    //change this function's name to changecolor then change the next function down to changecolorI
    //You will need to reverse this when using pi-blaster
    //readcmdini($GLOBALS['cmdinifile']);
    //$p = $GLOBALS['cmdini_array']['white']['pwr'];
    //$GLOBALS['cmdini_array']['command']['cmd'] = 'z';
    //write_ini_file($GLOBALS['cmdini_array'],$GLOBALS['cmdinifile']);
    if ($p > 10) {
        $p = 10;
    }
    $outy = "echo \"" .$GLOBALS['whitepin'] ."=" .$p ."\" > /dev/pi-blaster";
    if ($GLOBALS['debug']) 
    {
        echo "yardlight power " .$p ."\n";
        echo $outy ."\n";

    }else{
        $result = shell_exec($outy);
    }
    
    
}
//'*******************************************************************************



//'*******************************************************************************
function alloff($r=0,$g=0,$b=0)
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

//'*******************************************************************************
function showusage()
{
    /*echo "rgbsock.php Rev ". $GLOBALS['revmajor'] ."." .$GLOBALS['revminor'] ."\n";*/
    echo "rgbledsck.php Rev ? \n";
    echo "Usage: rgbledsck.php [option]...\n Using the Raspberry pi as an RGB LED Controller\n";
    echo "Mandatory arguments\n";
    echo "  r, \t Used for debuging from console\n";
    echo "  D, \t Daemon mode usualy called from sprinkd\n";
    echo "Pin numbers are set in the rgbled.ini file\n";      
    echo "\n\n";
}
//'*******************************************************************************

//'*******************************************************************************
function shwhelp()
{
    /*echo "rgbsock.php Rev ". $GLOBALS['revmajor'] ."." .$GLOBALS['revminor'] ."\n";*/
    $hstring = "\nrgbledclient.php Rev 1 \n";
    $hstring .= "Usage: rgbledclient.php [option]...\n Using the Raspberry pi as an RGB LED Controller\n";
    $hstring .= "Mandatory arguments\n";
    $hstring .= "  -h, \t This help\n";
    $hstring .= "  -c || color ,[.001-10],[.001-10],[.001-10],\t Set and turn on LED - Color values seperated by comma.\n";
    $hstring .= "  -setcolor,[.001-10],[.001-10],[.001-10] \t Sets the color\n";
    $hstring .= "  -stop, \t Stop the Strobe or the fade\n";
    $hstring .= "  -strobe [.001-10] [.001-10] [.001-10] [x-duration] [y-count],\t Strobe LED - Color values seperated by space. \n";
    $hstring .= "  -setstrobe\t Used for seting duration\n";
    $hstring .= "  -y || -yard, [1-10] \t Turn on yard lights at power 1-10 \n";
    $hstring .= "  -f || -fade\t Fade random colors\n";
    $hstring .= "  -wigwag [1-3] || red/blue strobe green strobe blue\n";
    $hstring .= "  -setfade\t Sets the fade duration\n";
    $hstring .= "  -get [option]:\n";
    $hstring .= "        fd: Fade duration\n";
    $hstring .= "        sd: Strobe duration\n";
    $hstring .= "         c: Colors returned is a coma seperated string in the format red,green,blue (0,0,0)\n";
    $hstring .= "Pin numbers are set in the " .$GLOBALS['inifile'] ." file\n";      
    $hstring .= "\n\n";
    return $hstring;
}
//'*******************************************************************************


?>
