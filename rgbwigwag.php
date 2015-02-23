#! /usr/bin/php

<?php
//Hey you have to turn on pi-blaster
///root/bin/pi-blast/pi-blaster exec it

global $inifile;
global $count;
global $count2;
global $fadepause;
global $redpin;
global $greenpin;
global $bluepin;
global $randcolorpause;
global $basedir;
$GLOBALS['basedir'] = getenv("RGB_LED_HOME");


$GLOBALS['inifile']= $GLOBALS['basedir'] ."/rgbled.ini";
require($GLOBALS['basedir'] ."/cli_readini.php");
readini($GLOBALS['inifile']);

wigwag();



//'*******************************************************************************
function wigwag()
{
 
//sleep for 5 seconds
//usleep(5000000);
//250000

$rw = 10;
$gw = 0;
$bw = 0;

$rg = 0;
$gg = 0;
$bg = 10;

$ro = 0;
$go = 0;
$bo = 0;
$us = 60000;

$outr = "echo \"" .$GLOBALS['redpin'] ."=" .$rw / 10 ."\" > /dev/pi-blaster";
$outg = "echo \"" .$GLOBALS['greenpin'] ."=" .$gw / 10 ."\" > /dev/pi-blaster";
$outb = "echo \"" .$GLOBALS['bluepin'] ."=" .$bw / 10 ."\" > /dev/pi-blaster";

$outrg = "echo \"" .$GLOBALS['redpin'] ."=" .$rg / 10 ."\" > /dev/pi-blaster";
$outgg = "echo \"" .$GLOBALS['greenpin'] ."=" .$gg / 10 ."\" > /dev/pi-blaster";
$outbg = "echo \"" .$GLOBALS['bluepin'] ."=" .$bg / 10 ."\" > /dev/pi-blaster";

$outro = "echo \"" .$GLOBALS['redpin'] ."=" .$ro / 10 ."\" > /dev/pi-blaster";
$outgo = "echo \"" .$GLOBALS['greenpin'] ."=" .$go / 10 ."\" > /dev/pi-blaster";
$outbo = "echo \"" .$GLOBALS['bluepin'] ."=" .$bo / 10 ."\" > /dev/pi-blaster";


    while(true)
    {
        for ($i=0; $i < 3; $i++) 
        { 
            $result = shell_exec($outr ." && " .$outg ." && " .$outb);
            usleep($us);
            $result = shell_exec($outro ." && " .$outgo ." && " .$outbo);
            usleep($us);        
        }
        usleep($us);        

        for ($i=0; $i < 3; $i++) 
        { 
            $result = shell_exec($outrg ." && " .$outgg ." && " .$outbg);
            usleep($us);
            $result = shell_exec($outro ." && " .$outgo ." && " .$outbo);
            usleep($us);        
        }
        usleep($us);        

        //changecolor($r,$g,$b);
        /*$result = shell_exec($outr ." && " .$outg ." && " .$outb);
        usleep(250000);
        $result = shell_exec($outro ." && " .$outgo ." && " .$outbo);
        //changecolor(0,0,0);
        usleep(250000);
        $result = shell_exec($outrg ." && " .$outgg ." && " .$outbg);
        usleep(250000);
        //readcmdini($GLOBALS['cmdinifile']);*/

    }
}
//'*******************************************************************************

?>


