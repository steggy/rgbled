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
#$GLOBALS['basedir'] = getenv("RGB_LED_HOME");
$GLOBALS['basedir'] ="/var/www/rgbled";


$GLOBALS['inifile']= $GLOBALS['basedir'] ."/rgbled.ini";
require($GLOBALS['basedir'] ."/cli_readini.php");
readini($GLOBALS['inifile']);
strobeII();

//'*******************************************************************************
function strobeII()
{
$d=$GLOBALS['strobedelay'];  
echo "STROBE DELAY " .$d ."\n";  
$r = $GLOBALS['rl'];
$g = $GLOBALS['gl'];
$b = $GLOBALS['bl'];
$outr = "echo \"" .$GLOBALS['redpin'] ."=" .$r / 10 ."\" > /dev/pi-blaster";
$outg = "echo \"" .$GLOBALS['greenpin'] ."=" .$g / 10 ."\" > /dev/pi-blaster";
$outb = "echo \"" .$GLOBALS['bluepin'] ."=" .$b / 10 ."\" > /dev/pi-blaster";

$r = 0;
$g = 0;
$b = 0;
$outro = "echo \"" .$GLOBALS['redpin'] ."=" .$r / 10 ."\" > /dev/pi-blaster";
$outgo = "echo \"" .$GLOBALS['greenpin'] ."=" .$g / 10 ."\" > /dev/pi-blaster";
$outbo = "echo \"" .$GLOBALS['bluepin'] ."=" .$b / 10 ."\" > /dev/pi-blaster";



    while(true)
    {
        //changecolor($r,$g,$b);
        $result = shell_exec($outr ." && " .$outg ." && " .$outb);
        usleep($d/2);
        //changecolor(0,0,0);
        $result = shell_exec($outro ." && " .$outgo ." && " .$outbo);
        usleep($d/2);
        //readcmdini($GLOBALS['cmdinifile']);
    }
}
//'*******************************************************************************

?>


