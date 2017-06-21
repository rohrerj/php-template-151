<script>
	function deleteClick(id) {
		
		var xhttp = new XMLHttpRequest();
		var csrf = document.getElementsByName("csrf")[0].value;
		var params = "csrf="+csrf+"&file="+id;
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
            	document.getElementById('messageContent').innerHTML = this.responseText;
            }
        };
        
        xhttp.open("POST", "/delete", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(params);
	}
	function createClick(dir) {
		var xhttp = new XMLHttpRequest();
		var csrf = document.getElementsByName("csrf")[0].value;
		var name = document.getElementById("folderName").value;
		var params = "csrf="+csrf+"&name="+name+"&dir="+dir;
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
            	document.getElementById('messageContent').innerHTML = this.responseText;
            }
        };
        
        xhttp.open("POST", "/create", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(params);
	}
</script>
<div style='float:left;'>
<fieldset>

	<h5>Directory: '<?php echo htmlspecialchars($dir)?>'</h5>
<table>

<?php
if($dir != "/") {
	$pos = strrpos($dir, "/");
	if ($pos !== false) {
		echo "<tr><td><a href='?dir=".substr($dir,0, $pos)."'>".".."."</a></td><td></td></tr>";
	}
}
for($i=0; $i<count($files);$i++) {
	if (strpos($files[$i][1], '/') === 0){//folder
		echo "<tr><td><a href='?dir=".$files[$i][1]."'>".substr($files[$i][1],strripos($files[$i][1],"/"))."</a></td><td>".$files[$i][2]."</td>";
		if($files[$i][2] == "Owner" || $files[$i][2] == "ReadWrite") {
			echo "<td><input type='button' onClick='deleteClick(".$files[$i][0].")' value='delete'></td>";
		}
		if($files[$i][2] == "Owner") {
			echo "<td><input type='button' value='share'></td>";
		}
		echo "</tr>";
	}
	else {//file
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
<tr>
	<td>Create Folder</td>
	<td><input type="text" id="folderName"></td>
	<td><input type="button" value="Create" <?php echo "onClick=createClick('".$dir."')"?>></td>
</tr>
</table>
</fieldset>
<div id='messageContent' <?php if($_SESSION["accessLevel"] != "admin") { echo "hidden"; }?>></div>
</div>
<div style='float:left;'>
	<fieldset>
		<h5>Upload File</h5>
		<form method='POST' action="/upload" enctype="multipart/form-data">
			<?php echo $csrf?>
			<input type="hidden" name="dir" value=<?php echo $dir?>>
			<table>
				<tr>
					<td><input type="file" name="file"></td>
				</tr>
				<tr>
					<td><input type="submit" value="Upload"></td>
				</tr>
			</table>
		</form>
	</fieldset>
</div>






