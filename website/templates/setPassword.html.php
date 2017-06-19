<form Method='post'>
<?php echo $csrf?>
	<input type="hidden" name='url' value='<?php echo $url?>'>
	<table>
		<tr>
			<td><label>Neues Passwort</label></td>
			<td><input type='Password' name='password1'></td>
		</tr>
		<tr>
			<td><label>Passwort bestätigen</label></td>
			<td><input type='Password' name='password2'></td>
		</tr>
		<tr>
			<td><input type="submit" value="Passwort setzen"></td>
		</tr>
	</table>
</form>