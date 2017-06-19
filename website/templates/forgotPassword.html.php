<!--Protected for CSRF-->
<form Method='post'>
<?php echo $csrf?>
	<table>
		<tr>
			<td><label>Email</label></td>
			<td><input type="text" name="email"></td>
		</tr>
		<tr>
			<td><input type="submit" value="Reset"></td>
		</tr>
	</table>
</form>