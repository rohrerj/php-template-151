<?php echo $csrf?>
<div style='float:left;'>
	<h4>Directory: '<?php echo htmlspecialchars($dir)?>'</h4>
<script>
	function deleteClick(id) {
		
		var xhttp = new XMLHttpRequest();
		var csrf = document.getElementsByName("csrf")[0].value;
		var params = "csrf="+csrf+"&file="+id;
		console.log(csrf);
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
            	document.getElementById("messageContent").innerHTML = this.responseText;
            }
        };
        
        xhttp.open("POST", "/delete", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(params);
        //
        
	}
</script>
<table>

<?php
if($dir != "/") {
	$pos = strrpos($dir, "/");
	if ($pos !== false) {
		echo "<tr><td><a href='?dir=".substr($dir,0, $pos)."'>".".."."</a></td><td></td></tr>";
	}
}
for($i=0; $i<count($files);$i++) {
	if (strpos($files[$i][1], '/') === 0){
		echo "<tr><td><a href='?dir=".$files[$i][1]."'>".$files[$i][1]."</a></td><td>".$files[$i][2]."</td>";
		if($files[$i][2] == "Owner" || $files[$i][2] == "ReadWrite") {
			echo "<td><input type='button' onClick='deleteClick(".$files[$i][0].")' value='delete'></td>";
		}
		if($files[$i][2] == "Owner") {
			echo "<td><input type='button' value='share'></td>";
		}
		echo "</tr>";
	}
	else {
		echo "<tr><td><a href='/download?file=".$files[$i][0]."'>".$files[$i][1]."</a></td><td>".$files[$i][2]."</td>";
		if($files[$i][2] == "Owner" || $files[$i][2] == "ReadWrite") {
			echo "<td><input type='button' onClick='deleteClick(".$files[$i][0].")' value='delete'></td>";
		}
		if($files[$i][2] == "Owner") {
			echo "<td><input type='button' value='share'></td>";
		}
		echo "</tr>";
	}
	
}
?>
</table>
<div id='messageContent'></div>
</div>