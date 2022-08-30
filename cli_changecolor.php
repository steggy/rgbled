<?
//'*******************************************************************************
function changecolor($r,$g,$b)
{
    $outr = "echo \"" .$GLOBALS['redpin'] ."=" .$r / 10 ."\" > /dev/pi-blaster";
    $outg = "echo \"" .$GLOBALS['greenpin'] ."=" .$g / 10 ."\" > /dev/pi-blaster";
    $outb = "echo \"" .$GLOBALS['bluepin'] ."=" .$b / 10 ."\" > /dev/pi-blaster";
    $result = shell_exec($outr ." && " .$outg ." && " .$outb);
    //$result = shell_exec($outg);
    //$result = shell_exec($outb);
    if($GLOBALS['debug'])
    {
    	echo "\"" .$GLOBALS['redpin'] ."=" .$r / 10 ."\"";
    	echo "\"" .$GLOBALS['greenpin'] ."=" .$g / 10 ."\"";
    	echo "\"" .$GLOBALS['bluepin'] ."=" .$b / 10 ."\"";
    }
}
//'*******************************************************************************
?>