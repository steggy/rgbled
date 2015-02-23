<?

?>
<html>
    <head>
        <script src="jquery.min.js"></script>
        <script type="text/javascript">
            function mm(ss){
		     //var selects = document.getElementById("vid");
			var red = document.getElementById("red");
			var green = document.getElementById("green");
			var blue = document.getElementById("blue");
		     //var vv = selects.options[selects.selectedIndex].value;
		     var rr = red.options[red.selectedIndex].value;
		     var gg = red.options[green.selectedIndex].value;
		     var bb = red.options[blue.selectedIndex].value;
                 //alert('This is vv: ' + vv);
                $.ajax({
                    type: "POST",
                    url: "asck.php",
                    data: "ss="+ss+"&red="+rr+"&green="+gg+"&blue="+bb,
                    success:function(data){
                        //alert('This was sent back: ' + data);
                        //Next line adds the data from PHP into the DOM
                        $('#div1').html(data);				
                    }
                    });
                }
            function cc(ss){
             //var selects = document.getElementById("vid");
            var red = document.getElementById("red");
            var green = document.getElementById("green");
            var blue = document.getElementById("blue");
             //var vv = selects.options[selects.selectedIndex].value;
             var rr = red.options[red.selectedIndex].value;
             var gg = red.options[green.selectedIndex].value;
             var bb = red.options[blue.selectedIndex].value;
                 //alert('This is vv: ' + vv);
                $.ajax({
                    type: "POST",
                    url: "asck.php",
                    data: "ss='"+ss+"'", //+" "+rr+","+gg+","+bb,
                    success:function(data){
                        //alert('This was sent back: ' + data);
                        //Next line adds the data from PHP into the DOM
                        $('#div1').html(data);              
                    }
                    });
                }
            function ww(ss){
            var pwr = document.getElementById("pwr");
            var pw = pwr.options[pwr.selectedIndex].value;
                 //alert('This is vv: ' + vv);
                $.ajax({
                    type: "POST",
                    url: "asck.php",
                    data: "ss="+ss+"&pwr="+pw,
                    success:function(data){
                        //alert('This was sent back: ' + data);
                        //Next line adds the data from PHP into the DOM
                        $('#div1').html(data);              
                    }
                    });
                }    
        </script>
<style>
body {
font-family: "Trebuchet MS", "Helvetica", "Arial",  "Verdana", "sans-serif";
color:white;
/*font-size: 162.5%;*/
background-image: url('./grunge.jpg');
background-repeat: no-repeat;
/*background-color:cyan;*/

}
button
{
/*width:160px;
height:100px;*/
/*font-size:40px;*/
-moz-border-radius: 3px;
-webkit-border-radius: 3px;
/*border: 5px solid #009900;*/

border:1px solid gray;
/*border-radius: 10px;*/
box-shadow: 3px 3px 3px #494949;

padding: 5px;
}

select
{
/*width:75px;
height:100px;
font-size:40px;*/
-moz-border-radius: 3px;
-webkit-border-radius: 3px;
/*border: 5px solid #009900;*/

border:1px solid gray;
/*border-radius: 10px;*/
box-shadow: 3px 3px 3px #494949;

padding: 5px;
}

#div0
{
display:inline;
font-size:18pt;
}
#video
{
background-color:cyan;
width:1100px;
height:650px;
position:absolute;
top:100px;
left:250px;
}
#videoframe
{
background-color:cyan;
width:1090px;
height:650px;
/*position:absolute;
top:100px;
left:250px;*/
}
</style>
<meta name="viewport" content="width=device-width">
    </head>
<body onload="mm('status');">


<div id="div0">RGB LED Socket Control</div><div id=div1></div>

<br>
<button id="button2" onclick="mm('status')">RGB STATUS</button>
<button id="button2" onclick="mm('start')">START RGB</button>
<button id="button2" onclick="mm('stop')">STOP RGB</button>
<button id="button2" onclick="mm('restart')">RESTART RGB</button>

<br><br>
<button id="button2" onclick="mm('-fade')">FADE</button>
<button id="button2" style="background-color:white;" onclick="mm('-strobe')">STROBE</button>
<button id="button2" style="background-color:white;" onclick="mm('-wigwag')">WIGWAG</button>
<br><br>
<button id="button2" onclick="mm('-stop')">OFF</button>
<br><br>
<button id="button2" style="background-color:red;" onclick="mm('-red')">RED</button>
<button id="button2" style="background-color:green;" onclick="mm('-green')">GREEN</button>
<button id="button2" style="background-color:blue;" onclick="mm('-blue')">BLUE</button>
<button id="button2" style="background-color:purple;" onclick="mm('-purple')">PURPLE</button>

<button id="button2" style="background-color:cyan;" onclick="mm('-peri')">PERI</button>

<br><br>
<button id="button2" onclick="mm('-color')">color</button>
R<select id="red" name="red">
<?
for($i=0;$i < 10.5; $i += 0.5)
{
?><option value="<?=$i;?>"><?=$i;?></option><?
}
?>
</select>
G<select id="green" name="green">
<?
for($i=0;$i < 10.5; $i += 0.5)
{
?><option value="<?=$i;?>"><?=$i;?></option><?
}
?>
</select>
B<select id="blue" name="blue">
<?
for($i=0;$i < 10.5; $i += 0.5)
{
?><option value="<?=$i;?>"><?=$i;?></option><?
}
?>
</select>
<br>
<br>

<button id="button2" style="background-color:white;" onclick="ww('white')">WHITE</button>
<select id=pwr name=pwr>
<?
for($i=1;$i < 11; $i++)
{
    ?><option value="<?=$i;?>"><?=$i;?></option><?
}
?>
    </select>
    <button id="button2" style="background-color:white;" onclick="ww('whiteoff')">WHITE OFF</button>
<br><br>
<button id="button2" onclick="mm('-temp')">Temp</button>
<button id="button2" style="background-color:white;" onclick="mm('-help')">HELP</button>
<br><br>

<?=$_SERVER['HTTP_HOST']?>

</body>
</html>





