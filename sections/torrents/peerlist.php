<?php
if (!isset($_GET['torrentid']) || !is_number($_GET['torrentid'])) {
	error(404);
}
$TorrentID = $_GET['torrentid'];

if (!empty($_GET['page']) && is_number($_GET['page'])) {
	$Page = $_GET['page'];
	$Limit = (string)(($Page - 1) * 100) .', 100';
} else {
	$Page = 1;
	$Limit = 100;
}



	/* ===============================================
		LITTLE SCRIPT TO FREELEECH ALOLIMOUS AFTER THE CONTEST
	/* =============================================== */
	// $DB->query("
		// SELECT ID FROM torrents
		// WHERE UserID = 1469
		// ");
	
	// $TorrentIDs = $DB->collect('ID');
	// Torrents::freeleech_torrents( $TorrentIDs, 2, 2);
	// Torrents::freeleech_torrents( $TorrentIDs, 1, 2);
	//====
	
	/* ===============================================
		BONUS/CREDIT SYSTEM WIP SHITTY CODE  (goes in sections/scheduler.php)
	/* =============================================== */
 
 // First run, initialize shit
 
	// $DB->query(" 
	// UPDATE xbt_files_users
		// SET lastknownseed = 0, seedtime = 0;
	// ");
	// $DB->query(" 
	// UPDATE users_main
		// SET Credits = 50;
	// ");

 // Updating times
	// $DB->query(" 
	// UPDATE xbt_files_users
		// SET seedtime = seedtime + UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(lastknownseed)
		// WHERE remaining=0 AND connectable=1 AND lastknownseed>0;
	// ");
	
	// $DB->query(" 
	// UPDATE xbt_files_users
		// SET lastknownseed = NOW();
	// "); 


 
 // // Points..
 // $DB->query("
		 // SELECT ID
		 // FROM users_main
		 // ");
// $UserIDs = $DB->collect('ID');
	// /* horrible code, shitty var names.
	
	// bonusseedtime = (previous) total time seeded torrents
	// totalbonustime = new total time seeded torrents
	// seedtimes = seedtimes of individual torrents, array
	
	// */
	// if (count($UserIDs) > 0) {
		// foreach ($UserIDs as $UserID) {  // find torrents that are seeded by < 5 seeders, give bonus points to those seeders
			// if( $UserID != 147 && $UserID != 60 && $UserID != 2651 ) continue; // Check yoself b4 you shrek others
			// $DB->query(" 
				// SELECT u.seedtime, m.bonusseedtime, t.Seeders
				// FROM xbt_files_users as u
				// JOIN torrents as t on u.fid = t.ID
				// JOIN users_main as m on u.uid = m.ID
				// WHERE u.uid='$UserID' AND u.remaining=0
			// ");
			// list( $seedtime ) = $DB->next_record();
			// $seeders = $DB->collect('Seeders');
			// $bonusseedtime = $DB->collect('bonusseedtime');
			// $seedtimes = $DB->collect('seedtime');
			// $seedersum = array_sum( $seeders );
			// $bonusseeds = count($seedtimes);
			// $totalbonustime = array_sum($seedtimes);
			// $timediff = $totalbonustime - $bonusseedtime[0];
			// $newcredits =  (10 /*$bonusseeds*/) * ($timediff / 86400) / $seedersum;    // magic 86400
			// echo "newcredits = $newcredits <br/>"; 		
			// $newcredits = max($newcredits, 0);
			// $newcredits = min($newcredits, 100);
		 	// echo "prev seed time = $bonusseedtime[0] <br/>";
			// echo "new seed time = $totalbonustime <br/>";
			// echo "timediff = $timediff <br/>";
			// echo "seeding = $bonusseeds <br/>";
			// echo "seeders = $seedersum <br/>";
			// echo "newcredits clamped = $newcredits <br/><br/>"; 			
			// $DB->query("
				// UPDATE users_main
				// SET Credits = Credits + '$newcredits',
					// bonusseedtime = '$totalbonustime'
				// WHERE ID='$UserID'
			// ");
			// $DB->query("SELECT Credits FROM users_main WHERE ID='$UserID'");
			// list($newcredits) = $DB->next_record();

			// $Cache->begin_transaction('user_info_'.$UserID);
			// $Cache->update_row(false, array('Credits' => $newcredits));
			// $Cache->commit_transaction(0);
			
		// }
	// }

	
	/* ===============================================
		PEERLIST.PHP continues here with normal shit
	/* =============================================== */
	
$Result = $DB->query("
	SELECT
		SQL_CALC_FOUND_ROWS
		xu.uid,
		t.Size,
		xu.active,
		xu.connectable,
		xu.uploaded,
		xu.remaining,
		xu.useragent
	FROM xbt_files_users AS xu
		LEFT JOIN users_main AS um ON um.ID = xu.uid
		JOIN torrents AS t ON t.ID = xu.fid
	WHERE xu.fid = '$TorrentID'
		AND um.Visible = '1'
	ORDER BY xu.uid = '$LoggedUser[ID]' DESC, xu.uploaded DESC
	LIMIT $Limit");
$DB->query('SELECT FOUND_ROWS()');
list($NumResults) = $DB->next_record();
$DB->set_query_id($Result);

?>
<h4>Peer List</h4>
<? if ($NumResults > 100) { ?>
<div class="linkbox"><?=js_pages('show_peers', $_GET['torrentid'], $NumResults, $Page)?></div>
<? } ?>

<table>
	<tr class="colhead_dark" style="font-weight: bold;">
		<td>User</td>
		<td>Active</td>
		<td>Connectable</td>
		<td class="number_column">Up (this session)</td>
		<td class="number_column">%</td>
		<td>Client</td>
	</tr>
<?
while (list($PeerUserID, $Size, $Active, $Connectable, $Uploaded, $Remaining, $UserAgent) = $DB->next_record()) {
?>
	<tr>
		<td>Peer</td>
		<td><?=($Active) ? '<span style="color: green;">Yes</span>' : '<span style="color: red;">No</span>' ?></td>
		<td><?= ($Connectable) ? '<span style="color: green;">Yes</span>' : '<span style="color: red;">No</span>' ?></td>
		<td class="number_column"><?=Format::get_size($Uploaded) ?></td>
		<td class="number_column"><?=number_format(($Size - $Remaining) / $Size * 100, 2)?></td>
		<td><?=display_str($UserAgent)?></td>
	</tr>
<?
}
?>
</table>
<? if ($NumResults > 100) { ?>
<div class="linkbox"><?=js_pages('show_peers', $_GET['torrentid'], $NumResults, $Page)?></div>
<? } ?>
