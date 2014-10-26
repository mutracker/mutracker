<?
if (!check_perms('admin_manage_rules')) {
	error(403);
}

if (!isset($_GET['id']) || !is_number($_GET['id'])) {
	error(404);
}
$ID = (int)$_GET['id'];

if ($ID == INDEX_ARTICLE) {
	error('You cannot delete the main wiki article.');
}

$DB->query("
	SELECT Title
	FROM rules_articles
	WHERE ID = $ID");

if (!$DB->has_results()) {
	error(404);
}

list($Title) = $DB->next_record(MYSQLI_NUM, false);
//Log
Misc::write_log("Wiki article $ID ($Title) was deleted by ".$LoggedUser['Username']);
//Delete
$DB->query("DELETE FROM rules_articles WHERE ID = $ID");
$DB->query("DELETE FROM rules_aliases WHERE ArticleID = $ID");
$DB->query("DELETE FROM rules_revisions WHERE ID = $ID");
Rules::flush_aliases();
Rules::flush_article($ID);

header("location: rules.php");
