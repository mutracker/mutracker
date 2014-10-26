<?php

$GroupID = $_GET['groupid'];
if (!is_number($GroupID) || !$GroupID) {
	error(0);
}

// Get the torrent group name and the body of the last revision
$DB->query("
	SELECT
		tg.Name,
		wt.Image,
		wt.Body,
		tg.WikiImage,
		tg.WikiBody,
		tg.Year,
		tg.RecordLabel,
		tg.CatalogueNumber,
		tg.ReleaseType,
		tg.CategoryID,
		tg.VanityHouse
	FROM torrents_group AS tg
		LEFT JOIN wiki_torrents AS wt ON wt.RevisionID = tg.RevisionID
	WHERE tg.ID = '$GroupID'");
if (!$DB->has_results()) {
	error(404);
}
list($Name, $Image, $Body, $WikiImage, $WikiBody, $Year, $RecordLabel, $CatalogueNumber, $ReleaseType, $CategoryID, $VanityHouse) = $DB->next_record();

if (!$Body) {
	$Body = $WikiBody;
	$Image = $WikiImage;
}

View::show_header('Edit torrent group');

?>
<div class="thin">
	<div class="header">
		<h2>Edit Art for <a href="torrents.php?id=<?=$GroupID?>"><?=$Name?></a></h2>
	</div>
	<div class="box pad">
		<form class="edit_form" name="torrent_group" action="torrents.php" method="post">
			<div>
				<input type="hidden" name="action" value="takeartedit" />
				<input type="hidden" name="auth" value="<?=$LoggedUser['AuthKey']?>" />
				<input type="hidden" name="groupid" value="<?=$GroupID?>" />
				<h3>Image:</h3>
				<input type="text" name="image" size="92" value="<?=$Image?>" /><br />
				<div style="text-align: center;">
					<input type="submit" value="Submit" />
				</div>
			</div>
		</form>
	</div>
</div>