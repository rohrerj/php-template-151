<div>
	<a href='/'>Home</a>
	<?php
		if(isset($_SESSION["email"])) {
			$name = $_SESSION["email"];
		}
		else {
			$name = "";
		}
		if($name == "Admin") {
			echo "<a href='/'>Admin</a>";
		}
		if($name == "") {
			echo "<a href='/login'>Login</a>";
			echo "<a href='/register'>Registrieren</a>";
		}
		else {
			echo "<a href='/logout'>Logout</a>";
		}
	?>
</div>