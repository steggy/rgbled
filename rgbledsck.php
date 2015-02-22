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

global $basedir;
$GLOBALS['basedir'] = "/var/www/rgbled";

$GLOBALS['cmd'] = '';
$GLOBALS['count'] = 0;
$GLOBALS['count2'] = 0;
$GLOBALS['pid'] = 0;

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
        setsock();
        $GLOBALS['debug'] = true;
        maindebug();
        break;
    case 'D':
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
    }
// Close the master sockets
socket_close($GLOBALS['sock']);

}
/*************************************/

/*************************************/
function maindebug()
{
    while (true) 
    {    
        checksock(); 
    }
// Close the master sockets
socket_close($GLOBALS['sock']);

}
/*************************************/

function checksock()
{
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
    //$output = ereg_replace("[ \t\n\r]","",$input).chr(0);
    //$output = ereg_replace("[ \t\n\r]","",$input);
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
                                $response = "Colors ";
                                $response .= "Red > " .$GLOBALS['rl'];
                                $response .= " Green > " .$GLOBALS['gl'];
                                $response .= " Blue > " .$GLOBALS['bl'] ."\n";
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
                $response = "Default:\n\n" .shwhelp();
                socket_write($client, $response);
                socket_close($client);
                break;
        }
    }
    
}

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
    echo "rgbsock.php Rev ? \n";
    echo "Usage: rgbsock.php [option]...\n Using the Raspberry pi as an RGB LED Controller\n";
    echo "Mandatory arguments\n";
    echo "  -h, \t This help\n";
    echo "  -x, \t Turn off all sprinklers\n";
    echo "  -z [1-8] [0,1], \t Turn on/off zone\n";
    echo "  -c [.001-10] [.001-10] [.001-10], \t Set and turn on LED - Color values seperated by space.\n";
    echo "  -s [.001-10] [.001-10] [.001-10] [x-duration] [y-count], \t Strobe LED - Color values seperated by space. \n";
    echo "  -r, \t Used for debuging from console\n";
    echo "  -D, \t Daemon mode usualy called from sprinkd\n";
    echo "Zones and pin numbers are set in the sprink.ini file\n";      
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
    $hstring .= "  -setfade\t Sets the fade duration\n";
    $hstring .= "  -get [option]:\n";
    $hstring .= "        fd: Fade duration\n";
    $hstring .= "        sd: Strobe duration\n";
    $hstring .= "         c: Colors\n";
    $hstring .= "Pin numbers are set in the " .$GLOBALS['inifile'] ." file\n";      
    $hstring .= "\n\n";
    return $hstring;
}
//'*******************************************************************************


?>
