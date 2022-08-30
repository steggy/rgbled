#! /usr/bin/php

<?

//lets look for arg
if(sizeof($argv) < 2)
{
	sendcmd('-h');
	die;
}
	for($i=1;$i < sizeof($argv); $i++)
	{
		if ($i == 1) 
		{
			$cstring=$argv[$i];
		}else{
			$cstring .= " " .$argv[$i];
		}
	} 
     sendcmd($cstring);

/*switch ($argv[1]) {
    case 'test':
        sendcmd('test');
        break;
    case '-D':
        setsock();
        $GLOBALS['debug'] = false;
        main();
        break;
    default:
        //showusage();
    	echo "thanks for playing\n\n";
    	return;
        break;
}*/


function sendcmd($cmd)
{
	// where is the socket server?
	$host="127.0.0.1";
	$port = 9000;
	 
	// open a client connection
	$fp = fsockopen ($host, $port, $errno, $errstr);
	 
	if (!$fp)
	{
	$result = "Error: could not open socket connection";
	$result .= "Check that rgbledsck.php is running";
	}
	else
	{
	// get the welcome message
	//fgets ($fp, 1024);
	// write the user string to the socket
	fputs ($fp, $cmd);
	// get the result
	//$result = fgets ($fp, 100000);
	$result = fread($fp, 100000);
	// close the connection
	fputs ($fp, "exit");
	fclose ($fp);
	 
	// trim the result and remove the starting ?
	//$result = trim($result);
	//$result = substr($result, 2);
	 echo $result ."\n";

	// now print it to the browser
	/*}
	?>
	Server said: <b><? echo $result; ?></b>
	<?*/
	}
}


?>

