#! /usr/bin/php
<?
$rl = '0';
$gl = '0';
$bl = '0';
$orl = '99';
$ogl = '99';
$obl = '99';
global $inifile;
global $count;
global $count2;
global $fadepause;
global $redpin;
global $greenpin;
global $bluepin;
global $randcolorpause;
global $debug;
global $basedir;

$GLOBALS['debug'] = 0;
$GLOBALS['count'] = 0;
$GLOBALS['count2'] = 0;
$GLOBALS['basedir']= "/var/www/rgbled";

$GLOBALS['inifile']= "/var/www/rgbled/rgbled.ini";
require($GLOBALS['basedir'] ."/cli_changecolor.php");
require($GLOBALS['basedir'] ."/cli_readini.php");
readini($GLOBALS['inifile']);
randomlight();


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
        
        echo "Red = " .$rl ." green = " .$gl ." Blue = " .$bl ."\n\n";
        echo "FADED\n";
        
        fade($rl,$gl,$bl);
        $GLOBALS['count']++;
        sleep($GLOBALS['randcolorpause']);
        
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
?>