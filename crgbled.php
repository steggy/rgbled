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
                        url: "argbled.php",
                        data: "ss="+ss+"&red="+rr+"&green="+gg+"&blue="+bb,
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
font-size: 182%;
background-color:cyan;
}
button
{
width:180px;
height:120px;
font-size:40px;
border: 1px solid #96968d;
border-radius: 10px;
box-shadow: 2px 2px 3px #888;
}
select
{
width:95px;
height:100px;
font-size:40px;

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
    </head>
<body onload="mm('status');">


<div id="div0">Yard Light Control</div>
<br><div id="div1"></div>

<br>
<button id="button2" onclick="mm('status')">RGB STATUS</button>
<button id="button2" onclick="mm('start')">START RGB</button>
<button id="button2" onclick="mm('stop')">STOP RGB</button>

<br><br>
<button id="button2" onclick="mm('fade')">FADE</button>

<button id="button2" onclick="mm('off')">OFF</button>
<br><br>
<button id="button2" style="background-color:red;" onclick="mm('red')">RED</button>
<button id="button2" style="background-color:green;" onclick="mm('green')">GREEN</button>
<button id="button2" style="background-color:blue;" onclick="mm('blue')">BLUE</button>
<br><br>
<button id="button2" style="background-color:purple;" onclick="mm('purple')">PURPLE</button>
<button id="button2" style="background-color:white;" onclick="mm('strobe')">STROBE</button>
<button id="button2" style="background-color:cyan;" onclick="mm('peri')">PERI</button>

<br><br>
<button id="button2" onclick="mm('color')">COLOR</button>
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
for($i=0;$i < 11; $i += 0.5)
{
?><option value="<?=$i;?>"><?=$i;?></option><?
}
?>
</select>
B<select id="blue" name="blue">
<?
for($i=0;$i < 11; $i += 0.5)
{
?><option value="<?=$i;?>"><?=$i;?></option><?
}
?>
</select>
<br>
<br>

<?=$_SERVER['HTTP_HOST']?>

</body>
</html>





