<?
enforce_login();

if (!empty($_REQUEST['action'])) {
	switch ($_REQUEST['action']) {
		case 'upload':
			require(SERVER_ROOT.'/sections/logchecker/takeupload.php');
			break;
		case 'takeupload':
			require(SERVER_ROOT.'/sections/logchecker/takeupload.php');
			break;
		default:
			//error(0);
			echo $_GET['action'];
	}
}
else {
	require(SERVER_ROOT.'/sections/logchecker/upload.php');
}

?>
