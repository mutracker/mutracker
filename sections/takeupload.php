<?
//******************************************************************************//
//--------------- Take upload --------------------------------------------------//
// This pages handles the backend of the torrent upload function. It checks     //
// the data, and if it all validates, it builds the torrent file, then writes   //
// the data to the database and the torrent to the disk.                        //
//******************************************************************************//

//ini_set('upload_max_filesize',1000000);
require_once('../classes/logchecker.class.php');
enforce_login();

$File = (isset($_FILES['log'])) ? $_FILES['log'] : null; // Our log file
$FileName = $File['tmp_name'];

if (is_uploaded_file($FileName) && filesize($FileName)) {
	$File = fopen($FileName, 'rb'); // open file for reading
	if (!$File) { die('File doesn\'t exist, or couldn\'t open'); } // File doesn't exist, or couldn't open
	$LogFile = fread($File, 1000000);
	fclose($File);
	// Contents of the log are now stored in $LogFile
} elseif (!empty($_POST["pastelog"])) {
	$LogFile = $_POST["pastelog"];
} else {
	error('No log file uploaded or file is empty.');
}
View::show_header();
echo '<div class="thin center"><br><a href="javascript:history.go(-1)">Upload another log file</a></div><div class="thin">';

//detect & transcode unicode
//if (detect_utf_bom_encoding($FileName)) { $LogFile = iconv("unicode","UTF-8",$LogFile); }

$Log = new LOG_CHECKER;
$Log->new_file($LogFile);
list($Score, $Good, $Bad, $Text) = $Log->parse();

if($Score == 100)	{ $Color = '#418B00'; }
elseif($Score > 90)	{ $Color = '#74C42E'; }
elseif($Score > 75)	{ $Color = '#FFAA00'; }
elseif($Score > 50)	{ $Color = '#FF5E00'; }
else 			{ $Color = '#FF0000'; }

echo "<blockquote><strong>Score:</strong> <span style=\"color:$Color\">$Score</span> (out of 100)</blockquote>";
if($Bad){?>
<blockquote>
	<h3>Log validation report:</h3>
	<ul>
<?
	foreach($Bad as $Property){
?>
		<li><?=$Property?></li>
<?
	}
?>
	</ul>
</blockquote>
<?
}
echo '<blockquote><pre>';
echo $Text;
echo '</pre></blockquote></div>';
View::show_footer();
?>