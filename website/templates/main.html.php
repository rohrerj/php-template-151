<head>
	<style type="text/css">
		div.lines table {
		    border-collapse: collapse;
		}
		div.lines td {
			border: 1px solid black;
		}
	</style>
</head>
<div>
	<?php
		if(isset($_SESSION["email"])) {
			$name = $_SESSION["email"];
		}
		else {
			$name = "";
		}
		
		echo "<a href='/'>Home</a>";
		if(isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] == "admin") {
			echo "<a href='/admin'>Admin</a>";
		}
		if($name == "") {
			echo "<a href='/login'>Login</a>";
			echo "<a href='/register'>Registrieren</a>";
		}
		else {
			echo "<a href='/logout'>Logout</a>";
		}
		echo "<div>";
		if($name != "") {
			echo "Hello, you are currently logged in as ".htmlspecialchars($name)."<br>";
		}
		
		echo "</div>";
	?>
</div>