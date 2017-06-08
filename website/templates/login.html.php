<!--<form Method="post">
	<label>Email</label><input type="text" name="email" value="<?= (isset($email)) ? $email: ""?>">
	<label>Password</label><input type="password" name="password">
	<br>
	<input type="submit">
</form>

-->
<form Method='post'>
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
<!--

-->
