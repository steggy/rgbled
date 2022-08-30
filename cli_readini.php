<?
//'*******************************************************************************
function readini($file)
{
if (!file_exists($file)) {
    echo "*********************************************\nrgbled.php\nFile not found: " .$file ."\n\n";
    die;
}
$GLOBALS['ini_array'] = parse_ini_file($file,true);
$GLOBALS['basedir'] = $GLOBALS['ini_array']['directory']['basedir'];
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
?>