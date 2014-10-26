<?
/**
 * HnR View (ajax) ~robot
 */

enforce_login();
if(!check_perms('admin_hitandrun')){ error(403); }

$type = $_GET['type'];
if (!isset($type) || empty($type)) { die; }

switch($type)
{
	case 'hnr': /* hnr data */
		$id = $_GET['id'];
		if (!isset($id) || empty($id)) { die; }

		$DB->query("SELECT
			uh.UserID,
			uh.TorrentID,
			uh.Snatched,
			uh.Remaining, /* Remaining seed time required to remove */
			g.ID as GroupID,
			GROUP_CONCAT(aa.Name ORDER BY aa.Name) AS Artist,
			GROUP_CONCAT(ta.ArtistID SEPARATOR '||') AS ArtistID,
			g.Name,
			g.Year,
			g.CategoryID,
			t.Size,
			g.TagList,
			t.Media,
			t.Format,
			t.Encoding,
			t.RemasterYear,
			t.Remastered,
			t.RemasterTitle,
			t.Scene,
			t.HasLog,
			t.HasCue,
			t.LogScore,
			t.FreeTorrent,
			t.FreeLeechType
			FROM users_hnrs as uh
			LEFT JOIN torrents AS t ON t.ID = uh.TorrentID
			INNER JOIN torrents_group AS g ON g.ID=t.GroupID
			LEFT JOIN torrents_artists as ta ON ta.GroupID = g.ID
			LEFT JOIN artists_alias as aa ON aa.ArtistID = ta.ArtistID
			WHERE uh.UserID=$id AND ta.Importance=1
			GROUP BY uh.UserID, uh.TorrentID
			ORDER BY uh.Snatched DESC
		");

		if($DB->record_count()) {
			$HnRs = $DB->to_array(false,MYSQLI_ASSOC);
			echo json_encode($HnRs);
		} else {
			echo "error: no records for user $id";
		}
		break;

	case 'chart1': /* chart data (month) */
		$DB->query("SELECT DATE_FORMAT(Date ,'%c/%d') AS Day, Total, HnRunners FROM users_hnrs_stat ORDER BY Date DESC LIMIT 31");
		$Data = array_reverse($DB->to_array(false,MYSQLI_ASSOC));
		echo json_encode($Data);
		break;

	case 'chart2': /* chart data (half year max, grouped by months) */
		$DB->query("SELECT DATE_FORMAT(Date ,'%b') AS Month, SUM(Total) as MonthTotal, COUNT(Date) as Days,
			SUM(Total / HnRunners) as MonthHnRunners
			FROM users_hnrs_stat GROUP BY Month(Date) ORDER BY Date ASC LIMIT 6
		");
		$Data = $DB->to_array(false,MYSQLI_ASSOC);
		echo json_encode($Data);
		break;
	default:
}
?>
