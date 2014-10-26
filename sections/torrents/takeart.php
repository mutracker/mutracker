<?php


authorize();

// Quick SQL injection check
if (!$_REQUEST['groupid'] || !is_number($_REQUEST['groupid'])) {
	error(404);
}
// End injection check

if (!check_perms('torrents_art_edit') && !check_perms('site_edit_wiki')) {
	error(403);
}
$UserID = $LoggedUser['ID'];
$GroupID = (int) $_REQUEST['groupid'];

$DB->query("
    SELECT Summary, Body
    FROM wiki_torrents
    WHERE PageID = '$GroupID' ORDER BY Time ASC LIMIT 1");

if (!$DB->has_results()) {
	error(404);
}
list($Summary, $Body) = $DB->next_record();

$Image = $_POST['image'];

$DB->query("
    INSERT INTO wiki_torrents
        (PageID, Body, Image, UserID, Summary, Time)
    VALUES
        ('$GroupID', '".db_string($Body)."', '".db_string($Image)."', '$UserID', 'Change Album Art', '".sqltime()."')");

$RevisionID = $DB->inserted_id();

$DB->query("
	UPDATE torrents_group
	SET
		RevisionID = '$RevisionID',
		WikiImage = '$Image'
	WHERE ID='$GroupID'");

Torrents::update_hash($GroupID);

header("Location: torrents.php?id=$GroupID");