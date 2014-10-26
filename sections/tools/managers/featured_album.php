<?php
enforce_login();
if (!check_perms('users_mod')) {
	error(403);
}

if (!empty($_POST)) {
	if (empty($_POST['groupid']) || !is_number($_POST['groupid'])) {
		error('AlbumID should be a number...');
		header('Location: tools.php?action=featured_album');
		die();
	}

	$GroupId = (int)$_POST['groupid'];
    $ThreadId = (int)$_POST['threadid'];
	$Title = db_string($_POST['title']);
    

	if (!$Title) {
		$Title = db_string('Featured Album');
	}
    
    $DB->query("
		UPDATE featured_albums
		SET Ended = '".sqltime()."'
		WHERE Ended = 0");
	$DB->query("
		INSERT INTO featured_albums (GroupID, ThreadID, Title)
		VALUES ($GroupId, '$ThreadId', '$Title')");
	$Cache->delete_value('featured_album');
	header('Location: index.php');
	die();
}

View::show_header();
?>
<h2>Change featured album</h2>
<div class="thin box pad">
	<form action="" method="post">
		<input type="hidden" name="action" value="featured_album" />
		<input type="hidden" name="auth" value="<?=$LoggedUser['AuthKey']?>" />
		<table align="center">
			<tr>
				<td class="label">Group ID:</td>
				<td>
					<input type="text" name="groupid" size="10" />
				</td>
			</tr>
			<tr>
				<td class="label">Thread ID (Optional):</td>
				<td>
					<input type="text" name="threadid" size="30" />
				</td>
			</tr>
			<tr>
				<td class="label">Title  (Optional):</td>
				<td>
					<input type="text" name="title" size="30" />
				</td>
			</tr>
			<tr>
				<td colspan="2" class="center">
					<input type="submit" value="Submit" />
				</td>
			</tr>
		</table>
	</form>
</div>
<?
View::show_footer();
?>

