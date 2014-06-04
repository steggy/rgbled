#! /usr/bin/php

<?php

//Set globals
global $address;
global $port;
global $sock;
global $debug;
global $stop;

$GLOBALS['debug'] = true;
$GLOBALS['stop'] = false;

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
$STDOUT = fopen('/var/log/sprink.log', 'wb');
$STDERR = fopen('/var/log/sprinkerror.log', 'wb');
//dont't forget to create these log files
    while (true) 
    {    
        checksock(); 
    }
// Close the master sockets
socket_close($sock);

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
socket_close($sock);

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
    echo "Status " .$status ."\n";

    $input = @socket_read($client, 1024);

    // Strip all white spaces from input
    echo $input ."\n";
    if($input == '')
    {
        break;
    }
    //$output = ereg_replace("[ \t\n\r]","",$input).chr(0);
    $output = ereg_replace("[ \t\n\r]","",$input);
    switch ($output) {
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
        case 'stoptest':
            $GLOBALS['stop'] = true;
            break;    
        default:
            $response = "default\n\n";
            socket_write($client, $response);
            socket_close($client);
            break;
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



?>