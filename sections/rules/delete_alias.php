<?
authorize();

$ArticleID = Rules::alias_to_id($_GET['alias']);

$DB->query("SELECT MinClassEdit FROM rules_articles WHERE ID = $ArticleID");
list($MinClassEdit) = $DB->next_record();
if ($MinClassEdit > $LoggedUser['EffectiveClass']) {
	error(403);
}

$DB->query("DELETE FROM rules_aliases WHERE Alias='".Rules::normalize_alias($_GET['alias'])."'");
Rules::flush_article($ArticleID);
Rules::flush_aliases();
