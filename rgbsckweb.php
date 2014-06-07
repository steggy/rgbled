


<?
//Remove 4 bottom lines before launch - Steggy
ini_set('display_errors', 1); 
ini_set('log_errors', 1); 
ini_set('error_log', dirname(__FILE__) . '/itrequest/error_log.txt'); 
error_reporting(E_ALL);
?>


<html>
<head>
	<title></title>
	<script src="jquery.min.js"></script>
        <script type="text/javascript">
            function mm(ss){
		     //var selects = document.getElementById("vid");
			//var red = document.getElementById("red");
			//var green = document.getElementById("green");
			//var blue = document.getElementById("blue");
		     //var vv = selects.options[selects.selectedIndex].value;
		     //var rr = red.options[red.selectedIndex].value;
		     //var gg = red.options[green.selectedIndex].value;
		     //var bb = red.options[blue.selectedIndex].value;
                 //alert('This is vv: ' + vv);
                $.ajax({
                    type: "POST",
                    url: "asck.php",
                    data: "ss="+ss,
                    success:function(data){
                        //alert('This was sent back: ' + data);
                        //Next line adds the data from PHP into the DOM
                        $('#div1').html(data);				
                    }
                    });
                }
                </script>
</head>
<body>
something
<br>
<br>
<div id=div1></div>
<button id="button2" onclick="mm('stop')">TEST</button>
</body>
</html>

<?


?>