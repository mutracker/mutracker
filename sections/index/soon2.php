<? require(SERVER_ROOT.'/design/publicheader.php'); ?>
<div class="center">We are currently down for maintenance, please check back later or check the IRC for current status.</div>
<span class="center"></span>
<? require(SERVER_ROOT.'/design/publicfooter.php'); 


	global $SessionID;
	setcookie('session', '', time() - 60 * 60 * 24 * 365, '/', '', false);
	setcookie('keeplogged', '', time() - 60 * 60 * 24 * 365, '/', '', false);
	setcookie('session', '', time() - 60 * 60 * 24 * 365, '/', '', false);
	if ($SessionID) {

		G::$DB->query("
			DELETE FROM users_sessions
			WHERE UserID = '" . G::$LoggedUser['ID'] . "'
				AND SessionID = '".db_string($SessionID)."'");

		G::$Cache->begin_transaction('users_sessions_' . G::$LoggedUser['ID']);
		G::$Cache->delete_row($SessionID);
		G::$Cache->commit_transaction(0);
	}
	G::$Cache->delete_value('user_info_' . G::$LoggedUser['ID']);
	G::$Cache->delete_value('user_stats_' . G::$LoggedUser['ID']);
	G::$Cache->delete_value('user_info_heavy_' . G::$LoggedUser['ID']);

	?>
