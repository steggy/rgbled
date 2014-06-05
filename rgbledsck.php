#! /usr/bin/php

<?php
//Hey you have to turn on pi-blaster
///root/bin/pi-blast/pi-blaster exec it

//Set globals
global $address;
global $port;
global $sock;
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
global $debugmode;


$GLOBALS['cmd'] = '';
$GLOBALS['count'] = 0;
$GLOBALS['count2'] = 0;

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

readini($GLOBALS['inifile']);

switch ($argv[1]) {
    case '-r':
        setsock();
        $GLOBALS['debug'] = true;
        maindebug();
        break;
    case '-D':
        setsock();
        $GLOBALS['debug'] = false;
        main();
        break;
    default:
        showusage();
        break;
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

    $input = @socket_read($client, 1024);

    // Strip all white spaces from input
    echo "RAW " .$input ."\n";
    if($input == '')
    {
        break;
    }
    //$output = ereg_replace("[ \t\n\r]","",$input).chr(0);
    $output = ereg_replace("[ \t\n\r]","",$input);
    if(strstr($output, 'color') || strstr($output, '-c'))
    {
            $response = "In Color " .$output ." ";
            $colorv = explode(",", $output);
            if(isset($colorv))
            {
                echo "IS SET\n";
                echo sizeof($colorv);
                if (sizeof($colorv) == 4) {
                    $response .= "We Have all 3\n";
                    $GLOBALS['rl'] = $colorv[1];
                    $GLOBALS['gl'] = $colorv[2];
                    $GLOBALS['bl'] = $colorv[3];
                    changecolor($colorv[1],$colorv[2],$colorv[3]);
                }
            }
            if(isset($colorv[3]))
                {
                    $e = $colorv[3];
                    $response .= $e;
                    $response .="\n";
                }else{
                    $response .="Need one more\n\n";
                }

            socket_write($client, $response);
            socket_close($client);
            
    }elseif (strstr(strtolower($output), '-yard')) 
    {
        $yp = explode(",", $output);
            if (isset($yp[1])) {
                echo "Yardlight Power " .$yp[1] ."\n";
                yardlight($yp[1]);
            }else{
                echo "Yardlight NO Power\n";
            }
    }else{
        switch (strtolower($output)) {
            case 'white':
                $response = "Turn on white\n\n";
                socket_write($client, $response);
                socket_close($client);
                break;
            case 'test':
                $response = "Testing\n\n";
                socket_write($client, $response);
                socket_close($client);
                looptest();
                break;
            case 'fade':
                $response = "Testing\n\n";
                socket_write($client, $response);
                socket_close($client);
                randomlight();
                break;    
            case '-strobe':
                $response = "Testing\n\n";
                socket_write($client, $response);
                socket_close($client);
                strobeII();
                break;        
            case '-yard':
                # code...
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
                $GLOBALS['stop'] = true;
                break;    
            case "--help":
            case "-help":
            case "--h":
            case "-h":
                var_dump(socket_get_option($GLOBALS['sock'], SOL_SOCKET, SO_SNDBUF));
                $response = shwhelp();
                socket_write($client, $response,strlen($response));
                sleep(1);
                socket_close($client);
                break;
            default:
                $response = "default\n\n";
                socket_write($client, $response);
                socket_close($client);
                break;
        }
    }
    }
    // Display output back to client

    //socket_write($client, $response);

    // Close the client (child) socket

    //socket_close($client);
}

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
        //readcmdini($GLOBALS['cmdinifile']);
        checksock();
        if($GLOBALS['stop'])
            {
                $GLOBALS['stop'] = false;
                return;
            }
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
function strobeII()
{
$d=$GLOBALS['ini_array']['strobe']['delay'];  
echo "STROBE DELAY " .$d ."\n";  
$r = $GLOBALS['rl'];
$g = $GLOBALS['gl'];
$b = $GLOBALS['bl'];
  
    while(true)
    {
        changecolor($r,$g,$b);
        usleep($d/2);
        changecolor(0,0,0);
        usleep($d/2);
        //readcmdini($GLOBALS['cmdinifile']);
        checksock();
        if($GLOBALS['stop'])
            {
                $GLOBALS['stop'] = false;
                return;
            }
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
    if ($GLOBALS['debug']) 
    {
        debugcolor($r,$g,$b);

    }else{
        daemoncolor($r,$g,$b);

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
$GLOBALS['rl'] = $GLOBALS['ini_array']['color']['r'];
$GLOBALS['gl'] = $GLOBALS['ini_array']['color']['g'];
$GLOBALS['bl'] = $GLOBALS['ini_array']['color']['b'];
/*$GLOBALS['dbhost'] = $ini_array['database']['dbhost'];
$GLOBALS['sname'] = $ini_array['sensor']['name'];
$GLOBALS['samprate'] = $ini_array['sensor']['sample_rate'];*/
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
    $hstring = "rgbsock.php Rev ? \n";
    $hstring .= "Usage: rgbsock.php [option]...\n Using the Raspberry pi as an RGB LED Controller\n";
    $hstring .= "Mandatory arguments\n";
    $hstring .= "  -h, \t This help\n";
    $hstring .= "  -x, \t Turn off all sprinklers\n";
    $hstring .= "  -z [1-8] [0,1], \t Turn on/off zone\n";
    $hstring .= "  -c || color ,[.001-10],[.001-10],[.001-10], \t Set and turn on LED - Color values seperated by comma.\n";
    $hstring .= "  -s [.001-10] [.001-10] [.001-10] [x-duration] [y-count], \t Strobe LED - Color values seperated by space. \n";
    $hstring .= "  -r, \t Used for debuging from console\n";
    $hstring .= "  -D, \t Daemon mode usualy called from sprinkd\n";
    $hstring .= "Zones and pin numbers are set in the sprink.ini file\n";      
    $hstring .= "\n\n";
    return $hstring;
}
//'*******************************************************************************


?>