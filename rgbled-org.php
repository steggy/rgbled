#! /usr/bin/php

<?
//Hey you have to turn on pi-blaster
///root/bin/pi-blast/pi-blaster exec it
//add fading to this

//add strobe to this


//red is pin 22
//green is pin 23
//blue is pin 24
$red = "22";
$green = "23";
$blue = "24";
$rl = '0';
$gl = '0';
$bl = '0';
$orl = '99';
$ogl = '99';
$obl = '99';

$count = 0;
$count2 = 0;
if(isset($argv[1]))
{
	if($argv[1] == 'x')
	{
		changecolor(0,0,0);		
		exit;

	}

	if($argv[1] == 'r')
	{
		while(true)
		{		
			//echo(rand(0,10) / 100);
			//echo "\n";
		
			if($count == 10)
			{
				$count = 0;
				$count2++;
			}
			switch($count2)
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
					$count2 = 0;
			}

			/*if(!$orl == '0' && !$ogl == '0' && !$obl == '0')
			{
				//if orl is less then rl count down else count up
			}else{*/
			echo "Red = " .$rl ." green = " .$gl ." Blue = " .$bl ."\n\n";
			fade($rl,$gl,$bl);
			$count++;
			sleep(3);
		
			/*}*/
		}

	}

	
}
if(sizeof($argv) >= 3)
{
	//echo "Arg 1 " .$argv[1] ."\n";	
	$filename = $argv[1];
	$rl = $argv[1];
	$gl = $argv[2];
	$bl = $argv[3];	
	changecolor($rl,$gl,$bl);
}else{
	echo "Must enter 3 values r=0-1 g=0-1 b=0-1 | or x as arg 1 for all off | or r for random\n";
exit;
}


function fade($r,$g,$b)
{
$stopfade = 0;
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
		usleep(9600); //2000000 = 2 sec
		}
		$GLOBALS['orl'] = $r;
		$GLOBALS['ogl'] = $g;
		$GLOBALS['obl'] = $b;

		
	}
}

function strobe($r,$g,$b,$t,$d)
{


 changecolor($r,$g,$b);

}

function updown($o,$n)
{
	if($o > $n)
	{
		return 0;
	}else{
		return 1;
	}
}

function changecolor($r,$g,$b)
{
	$outr = "echo \"" .$GLOBALS['red'] ."=" .$r / 10 ."\" > /dev/pi-blaster";
	$outg = "echo \"" .$GLOBALS['green'] ."=" .$g / 10 ."\" > /dev/pi-blaster";
	$outb = "echo \"" .$GLOBALS['blue'] ."=" .$b / 10 ."\" > /dev/pi-blaster";
	//debug without pi-blaster
	/*echo "\"" .$GLOBALS['red'] ."=" .$r / 10 ."\"";
	echo "\"" .$GLOBALS['green'] ."=" .$g / 10 ."\"";
	echo "\"" .$GLOBALS['blue'] ."=" .$b / 10 ."\"";
	echo "\n\n";*/
	$result = shell_exec($outr);
	$result = shell_exec($outg);
	$result = shell_exec($outb);
}








?>
