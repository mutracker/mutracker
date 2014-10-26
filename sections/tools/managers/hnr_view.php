<?
/**
 * HnR View ~robot
 */

if(!check_perms('admin_hitandrun')) { error(403); }

define('USERS_PER_PAGE', 50);
list($Page,$Limit) = page_limit(USERS_PER_PAGE);

$sql_where = array();

if (isset($_REQUEST['disabled']) && $_REQUEST['disabled'] == '1') {
	$enabled_only = false;
} else {
	$enabled_only = true;
	array_push($sql_where, "um.Enabled = '1'");
}

if (isset($_REQUEST['user']) && !empty($_REQUEST['user']) 
        && strlen($_REQUEST['user']) >= 2) {
	$user = $_REQUEST['user'];
	array_push($sql_where, "um.Username LIKE '%" . db_string($_REQUEST['user']) . "%'");
} else {
	$user = false;
}

if (count($sql_where)) {
	$sql_where = 'WHERE ' . join(' AND ', $sql_where);
} else {
	$sql_where = '';
}

View::show_header('Hit and Runs');

?>
<style type="text/css">
.cformat { font-weight:normal;font-size:8pt }
</style>
<script type="text/javascript" src="https://www.google.com/jsapi"></script> <!-- for chart -->
<script type="text/javascript">
function HitAndRun()
{
	this.debug = false;

	var self = this;

	this.gi = function(id) { return document.getElementById(id); }

	this.chartToggle = function(caller,id)
	{
		if (id == 1) { //togvis
			switch(caller.innerHTML) {
				case '[View Chart]':
					caller.innerHTML = '[Hide Chart]';
					self.gi('chart_switch').style.display = '';
					self.get('chart1');
					break;
				case '[Hide Chart]':
				default:
					caller.innerHTML = '[View Chart]';
					self.gi('chart_switch').style.display = 'none';
					self.gi('chart_switch').type = 1;
					self.gi('chart1_div').innerHTML = '';
					self.gi('chart2_div').innerHTML = '';
					break;
			}
		} else if (id == 2) { //switch type
			switch(caller.type) {
				case '1':
					caller.type = 2;
					self.get('chart2');
					break;
				case '2':
				default:
						caller.type = 1;
					self.get('chart1');
					break;
			}
		}
	};

	this.drawChart = function(timeline,type)
	{ //https://code.google.com/apis/chart/interactive/docs/gallery/linechart.html
		if (!timeline.length) { self.gi('chart1_div').innerHTML="No data"; return; }

		var chart_title = "HnR Statistics Last " + (type == "chart1" ? "Month" : " Six Months");
		var chart_unit = (type == "chart1" ? 'Day' : 'Month');
		var chart_legend1 = (type == "chart1" ? 'Total' : 'Average HnR Total per Day');
		var chart_legend2 = (type == "chart1" ? 'Average HnRs per Offender' : 'Average HnRs per Offender per Day');

		var data1 = new google.visualization.DataTable();
		var data2 = new google.visualization.DataTable();

		data1.addColumn('string', chart_unit);
		data1.addColumn('number', chart_legend1);
		if (type == "chart1") { data1.addColumn('number', 'HnRunners'); }
		data1.addRows(timeline.length);

		data2.addColumn('string', chart_unit);
		data2.addColumn('number', chart_legend2);
		data2.addRows(timeline.length);

		for (var i=0; i<timeline.length; i++) {
			if (type == "chart2") {
				var av_total = Math.round(timeline[i].MonthTotal / timeline[i].Days);
				var av_hnrs = Math.round(timeline[i].MonthHnRunners / timeline[i].Days);

				data1.setValue(i,0,timeline[i].Month);
				data1.setValue(i,1,Number(av_total));

				data2.setValue(i,0,timeline[i].Month);
				data2.setValue(i,1,Number(av_hnrs));
			} else {
				var av = Math.round(timeline[i].Total / timeline[i].HnRunners);

				data1.setValue(i,0,timeline[i].Day); 
				data1.setValue(i,1,Number(timeline[i].Total));
				data1.setValue(i,2,Number(timeline[i].HnRunners));

				data2.setValue(i,0,timeline[i].Day);
				data2.setValue(i,1,Number(av));
			}
		}

		var chart1 = new google.visualization.LineChart(self.gi('chart1_div'));
		chart1.draw(data1, {interpolateNulls:true, fontSize:10, legend:'bottom', vAxis:{baseline:0,format:'#'}, chartArea:{left:'auto'}, width:900, height:240, title:chart_title});
		var chart2 = new google.visualization.LineChart(self.gi('chart2_div'));
		chart2.draw(data2, {interpolateNulls:true, fontSize:10, legend:'bottom', vAxis:{baseline:0,format:'#'}, chartArea:{left:'auto'}, width:900, height:240, title:chart_title});
	};

	this.toggle = function(caller)
	{
		switch(caller.innerHTML) {
			case '[View]':
				caller.innerHTML = '[Hide]';
				var trs = document.getElementsByClassName('main_row');
				for (var i=0; i<trs.length; i++) { trs[i].style.display = 'none'; } //hide trs
				caller.parentNode.parentNode.parentNode.style.display = ''; //faster
				self.get('hnr',caller.id.replace('toggle_',''));
				break;
			case '[Hide]':
			default:
				caller.innerHTML = '[View]';
				var table = self.gi('user_table');
				table.style.display = 'none';
				table.innerHTML = null;
				var trs = document.getElementsByClassName('main_row');
				for (var i=0; i<trs.length; i++) { trs[i].style.display = ''; } //show trs
		}
	};

	this.get = function(type,id)
	{
		if (!type || (type=='hnr' && (!id || isNaN(id)))) { return; }
		ajax.get('tools.php?action=hnr_view_ajax&type=' + type + '&id=' + id, function(response) {
			if (self.debug) {
				self.gi('debug').innerHTML = "------JSON------<br>"+response+"<br>------------";
			}
			switch(type) {
				case 'chart1':
				case 'chart2':
					self.drawChart(json.decode(response),type);
					break;
				case 'hnr':
				default:
					self.fill(id,json.decode(response));
					break;
			}
		});
	};

	this.fill = function(id,data)
	{
		if (!id || !data.length) { return; }
		var table = self.gi("user_table");

		if (navigator.appName == 'Microsoft Internet Explorer') {
			/* internet explorer */
			var iec = self.gi('user_table_ie');
			var c = '<table><tbody id="ietb"><tr class="colhead"><td>Torrent</td><td>Snatched</td><td>Remaining</td></tr>';
			for (row in data) {
				if (typeof data[row] == "undefined") { continue; }
				c += 
					'<tr class="group">'+
					'<td class="cformat">'+self.makeTorrentDisplay(data[row])+'</td>'+
					'<td class="cformat">'+self.timeConvert(data[row].Snatched)+' ago<br>'+data[row].Snatched+'</td>'+
					'<td class="cformat">'+self.timeHumanReadable(data[row].Remaining)+'</td>'+
					'</tr>';
			}
			c += '</tbody></table>';
			iec.innerHTML = c;
			var ietb = self.gi("ietb");
			ietb.parentNode.replaceChild(iec.firstChild.firstChild, ietb);
			table.style.display = '';
		} else {
			/* the rest of the bunch */
			var header = document.createElement("tr");
			header.className = 'colhead';
			table.appendChild(header);
			header.innerHTML = '<td>Torrent</td><td>Snatched</td><td>Remaining</td>'; //IE error
			for (row in data) {
				if (typeof data[row] == "undefined") { continue; }
				var newRow = document.createElement("tr");
				table.appendChild(newRow);
				newRow.innerHTML = 
					'<tr class="group">'+
					'<td class="cformat">'+self.makeTorrentDisplay(data[row])+'</td>'+
					'<td class="cformat">'+self.timeConvert(data[row].Snatched)+' ago<br>'+data[row].Snatched+'</td>'+
					'<td class="cformat">'+self.timeHumanReadable(data[row].Remaining)+'</td>'+
					'</tr>';
			}
			table.style.display = '';
		}
	};

	this.makeTorrentDisplay = function(data)
	{
		var artists = data.Artist.split('||'), artistIds = data.ArtistID.split('||'), artistId = null, artistArr = [];
		while (artistId = artistIds.shift()) {
			artistArr.push('<a href="artist.php?id='+artistId+'">'+artists.shift()+'</a>');
		}
		artists = artistArr.length >= 3 ? 'Various Artists' : artistArr.join(' and ');
		var title = '<a href="torrents.php?id='+data.GroupID+'&torrentid='+data.TorrentID+'">'+data.Name+'</a>'
		var year = parseInt(data.RemasterYear) ? data.RemasterYear : (parseInt(data.Year) ? data.Year : '1900');

		var extrainfo = [];
		extrainfo.push(data.Format);
		extrainfo.push(data.Encoding);
		if (parseInt(data.HasLog)) {
			extrainfo.push('Log' + (parseInt(data.LogScore) ? ' ('+data.LogScore+'%)' : ''));
		}
		if (parseInt(data.HasCue)) { extrainfo.push('Cue'); }
		extrainfo.push(data.Media);
		var size = self.sizeHumanReadable(data.Size);

		return artists + ' - ' + title + ' [' + year + '] - ' + extrainfo.join(' / ') + ' (' + size + ')';
	};

	this.sizeHumanReadable = function(size)
	{
		if (isNaN(size)) { return '0 KB'; }
		var kb = (size / 1024).toFixed(2);
		var mb = (size / (1024 * 1024)).toFixed(2);
		var gb = (size / (1024 * 1024 * 1024)).toFixed(2);
		return kb < 1000 ? kb+' KB' : (mb < 1000 ? mb+' MB' : gb+' GB');
	};

	this.timeConvert = function(date)
	{ //format: 2011-11-25 09:56:40
		date = date.split(' ');
		var year = date[0].split('-'), month = day = 0;
		day = year.pop(), month = year.pop() - 1, year = year.pop();

		var hours = date[1].split(':'), seconds = minutes = 0;
		seconds = hours.pop(), minutes = hours.pop(), hours = hours.pop();

		var diff = Date.parse(new Date()) - Date.parse(new Date(year, month, day, hours, minutes, seconds, 0));
		return self.timeHumanReadable(diff / 1000);
	};

	this.timeHumanReadable = function(seconds)
	{
		if (isNaN(seconds)) { return ''; }
		var years = days = hours = minutes = 0;
		years = parseInt(seconds / 31536000);
		seconds = seconds - years * 31536000;
		days = parseInt(seconds / 86400);
		seconds = seconds - days * 86400;
		hours = parseInt(seconds / 3600);
		minutes = ((seconds - hours * 3600) / 60).toFixed(0);
		return (years ? years + " year"+(years != 1 ? 's' : '')+", " : '') + 
					 (days ? days + " day"+(days != 1 ? 's' : '')+", " : '') +
					 (hours ? hours + " hour"+(hours != 1 ? 's' : '')+", " : '') +
					 (minutes ? minutes + " minute"+(minutes != 1 ? 's' : '') : '');
	};

	this.ctrl_username_handle_blur = function(caller)
	{
		var v = caller.value.replace(/\s+/g,'');
		if (!v.length) {
			caller.value = 'Username';
		} else {
			caller.value = v;
		}
	};

	this.ctrl_submit = function(caller)
	{ // assemble and send
		var q_user = self.gi('ctrl_username').value.replace(/\s+/g,'');
		if (q_user.length && q_user != 'Username') {
			self.gi('payload_user').value = q_user;
		}
		
		if (!self.gi('ctrl_disabled').checked) {
			self.gi('payload_disabled').value = '1';
		}
		
		document.ctrl_form.submit();
	};

	this.ctrl_handle_key = function(caller,event)
	{
		if (event.keyCode == 13) {
			return self.ctrl_submit(caller);
		}
	};

}

var hnr = new HitAndRun();

function LoadGoogle()
{
	if(typeof google != 'undefined' && google && google.load)
	{
		google.load("visualization", "1", {packages:["corechart"]});
	}
	else
	{ // try later
		setTimeout(LoadGoogle, 30);
	}
}

LoadGoogle();
</script>
<?

$q = $DB->query("SELECT SQL_CALC_FOUND_ROWS
   uh.UserID as UserID,
   um.Username,
   um.Enabled,
   um.Uploaded,
   um.Downloaded,
   COUNT(uh.TorrentID) as HnRs,
   ui.Donor,
   ui.Warned,
   ui.JoinDate,
   ui.WarnedTimes,
   p.Level AS Class
   FROM users_hnrs as uh
   LEFT JOIN users_main as um ON um.ID = uh.UserID
   LEFT JOIN users_info AS ui ON ui.UserID = um.ID
   LEFT JOIN permissions AS p ON p.ID = um.PermissionID
   $sql_where
   GROUP BY uh.UserID
   ORDER BY HnRs DESC
   LIMIT $Limit
");

$DB->query("SELECT FOUND_ROWS()");
list($Results) = $DB->next_record();
$DB->set_query_id($q);

?>
<center><h2>Hit and runs</h2></center>
<div class="thin">

<form name="ctrl_form" action="tools.php" method="get" onkeypress="hnr.ctrl_handle_key(this,event); return;">
  <input type="hidden" name="action" value="hnr_view" />
  <input type="hidden" id="payload_user" name="user" value="" /> <!-- set by js -->
  <input type="hidden" id="payload_disabled" name="disabled" value="" /> <!-- set by js -->
  <table id="ctrl_table">
	<tr>
		<td>
			<input id="ctrl_username" type="text" value="<?=($user !== false ? $user : 'Username');?>" onclick="this.value='';" onblur="hnr.ctrl_username_handle_blur(this);" />
		</td>
		<td>
			<input id="ctrl_disabled" type="checkbox" <?=($enabled_only == true ? 'checked="checked"' : '');?> />&nbsp;Enabled only
		</td>
		<td>
			<input id="ctrl_submit" type="button" value="Submit" onclick="hnr.ctrl_submit(this);" />
		</td>
	</tr>
  </table>
</form>
<?

if($Results)
{
?>
   <p id="chart_toggle" class="center">
     <a href="javascript:void(0)" onclick="hnr.chartToggle(this,1)">
       [View Chart]
     </a>&nbsp;
     <a href="javascript:void(0)" 
        id="chart_switch" 
        onclick="hnr.chartToggle(this,2)" 
        type="1" style="display:none">[Switch]</a></p>
   <div id="debug"></div>
   <div id="chart1_div"></div><div id="chart2_div"></div>

   <div class="linkbox">
<?
   $Pages=get_pages($Page,$Results,USERS_PER_PAGE,11) ;
   echo $Pages;
?>
   </div>

   <div class="box pad">
	<table class="torrent_table" id="main_table" width="100%">
		<tr class="colhead">
			<td class="small cats_col">User</td>
			<td class="small cats_col">HnR Count</td>
			<td class="small cats_col">Registered</td>
			<td class="small cats_col">Warned</td>
		</tr>
<? ob_start(); //buffer
   $Row = 'a';
   while(list($UserID, $UserName, $Enabled, $Uploaded, $Downloaded, $HnRCount, $Donor, $Warned, $Joined, $WarnedTimes, $Class)=$DB->next_record()) {
	$Row = ($Row == 'b') ? 'a' : 'b';
?>
	<tr class="main_row row<?=$Row?>">
		<td><?=Users::format_username($UserID, true, true, true, false, true)?> <?="(".$ClassLevels[$Class]['Name'].")"?></td>
		<td><?=$HnRCount?> <span style="float:right;"><a href="#a_<?=$UserID?>" name="a_<?=$UserID?>" id="toggle_<?=$UserID?>" onclick="hnr.toggle(this)">[View]</a></span></td>
		<td style="float:right; border:none"><?=time_diff($Joined,2)?></td>
		<td><?=$WarnedTimes?></td>
	</tr>
<? }
   print ob_get_clean();
?>
   </table>
   <table id="user_table" class="torrent_table" style="display:none;"><tbody id="ietb"></tbody></table>
   <span id='user_table_ie' style='visibility:hidden'></span>
   </div>
   <div class="linkbox">
   <? echo $Pages; ?>
   </div>
<?
} else { ?>
	<p class="center"><strong>No results.</strong></p>
<?
}
?>
</div>
<?
View::show_footer('Hit and Runs');
?>
