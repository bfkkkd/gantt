<?php

if (empty($_POST['p'])) {
	echo '<form enctype="multipart/form-data" action="login.php" method="post">
		<table>
			<tr>
				<td><label for="txtname">NAME</label></td>
				<td><input type="text" id="txtname" name="u" /></td>
			</tr>
			<tr>
				<td><label for="txtpswd">PWD</label></td>
				<td><input type="password" id="txtpswd" name="p" /></td>
			</tr>
			<tr>
				<td colspan=2>
					<input type="reset" />
					<input type="submit" />
				</td>
			</tr>
		</table>
	</form> ';
	die;
} else {

	$username = (isset($_POST['u']) && !empty($_POST['u'])) ? trim($_POST['u']) . " - " . $_SERVER ['REMOTE_ADDR'] : $_SERVER ['REMOTE_ADDR'];
	$password = isset($_POST['p']) ? trim($_POST['p']) : '';

	session_start();
	if ($password == 'Vip') {
		$_SESSION['uname'] = $username;
		echo $username . " login! <a href='gantt.html'>ENTER</a>";
	} else {
		unset($_SESSION['uname']);
		echo "error!";
	}

}

?>