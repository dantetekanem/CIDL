<?PHP

	// list all commands
	echo nl2br(file_get_contents("generate/help/controller.php"));
	echo "<br /><br /><br />------------------------------------------------<br /><br /><br />";
	echo nl2br(file_get_contents("generate/help/model.php"));
	echo "<br /><br /><br />------------------------------------------------<br /><br /><br />";
	echo nl2br(file_get_contents("generate/help/view.php"));
	echo "<br /><br /><br />------------------------------------------------<br /><br /><br />";
	echo nl2br(file_get_contents("generate/help/helper.php"));
	echo "<br /><br /><br />------------------------------------------------<br /><br /><br />";
	echo nl2br(file_get_contents("generate/help/migration.php"));
	echo "<br /><br /><br />------------------------------------------------<br /><br /><br />";

?>