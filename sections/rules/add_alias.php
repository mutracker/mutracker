<?
authorize();

if (!isset($_POST['article']) || !is_number($_POST['article'])) {
	error(0);
}

$ArticleID = (int)$_POST['article'];

$DB->query("SELECT MinClassEdit FROM rules_articles WHERE ID = $ArticleID");
list($MinClassEdit) = $DB->next_record();
if ($MinClassEdit > $LoggedUser['EffectiveClass']) {
	error(403);
}

$NewAlias = Rules::normalize_alias($_POST['alias']);
$Dupe = Rules::alias_to_id($_POST['alias']);

if ($NewAlias != '' && $NewAlias!='addalias' && $Dupe === false) { //Not null, and not dupe
	$DB->query("INSERT INTO rules_aliases (Alias, UserID, ArticleID) VALUES ('$NewAlias', '$LoggedUser[ID]', '$ArticleID')");
} else {
	error('The alias you attempted to add was either null or already in the database.');
}

Rules::flush_aliases();
Rules::flush_article($ArticleID);
header('Location: rules.php?action=article&id='.$ArticleID);
