<!--Protected for CSRF-->
<form Method='post'>
<?php echo $csrf?>
	<table>
		<tr>
			<td><label>Email</label></td>
			<td><input type="text" name="email" value="<?= (isset($email)) ? $email: ""?>"></td>
		</tr>
		<tr>
			<td><label>Passwort</label></td>
			<td><input type='password' name='password'></td>
		</tr>
		<tr>
			<td><input type="submit" value="Login"></td>
		</tr>
	</table>
	
</form>
<a href="/forgotPassword">Password vergessen</a>
