<form Method='post'>
<?php echo $csrf?>
	<table>
		<tr>
			<td><label>Vorname</label></td>
			<td><input type='Text' name='vorname'></td>
		</tr>
		<tr>
			<td><label>Nachname</label></td>
			<td><input type='Text' name='nachname'></td>
		</tr>
		<tr>
			<td><label>Email</label></td>
			<td><input type='Text' name='email'></td>
		</tr>
		<tr>
			<td><input type="submit" value="Registrieren"></td>
		</tr>
	</table>
</form>