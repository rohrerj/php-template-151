<h5>Admin SQL Console</h5>
<div>
	<form Method="post">
		<?php echo $csrf?>
		<textarea rows="4" cols="80" name="query"></textarea><br>
		<input type="submit" value="Query">
	</form>
</div>
<div class="lines">
<?php 
if(isset($result)) {
	echo "<table style=\"border-collapse: collapse;\">";
	for($i = 0; $i < count($result); $i++) {
		echo "<tr>";
		for($y=0; $y < count($result[$i]);$y++) {
			echo "<td>".$result[$i][$y]."</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
}
?>
</div>