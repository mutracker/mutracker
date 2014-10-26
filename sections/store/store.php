<?
require(SERVER_ROOT.'/sections/store/user_promotion_criteria.php');
?>

<?
View::show_header('The Mutracker Store');	
?>

<div class="header">
	<script type="text/javacript">document.getElementByID('content').style.overflow = 'visible';</script>
	<h2>The Mutracker Store</h2>
	<h3>Balance: <?=(int)$rd['Sheckels']?></h3>
    <h3>User Class: <?=$Classes[$rd['Class']]['Name']?></h3>
    <h3>Torrents Uploaded: <?=$rd['Uploads'] ?></h3>
</div>


<table width="100%">
	<tr class="colhead">
		<td>Class</td>
		<td>Price</td>
		<td>Description</td>
		<td>Submit</td>
	</tr>  
    <tr class="row">
		<form class="buy_title" name="buy_title" action="" method="post">
			<input type="hidden" name="action" value="buy_memer" />
			<input type="hidden" name="auth" value="<?=$LoggedUser['AuthKey']?>" />
			<td>
		       <h3> memer </h3>
			</td>
			<td>
				<span><?= $Criteria['pleb_to_memer']['Sheckels'] ?></span>
			</td>
			<td>
				<span>You need 15 uploaded torrents, 2 week membership on the site and 1500 credits.</span>
			</td>
			<td>
				<input <?php echo $mayBuy['memer'] ? '' : 'disabled="true"' ?> type="submit" name="submit" value="Buy memer" />
			</td>
		</form>		
	</tr>
    <tr class="row">
		<form class="buy_title" name="buy_title" action="" method="post">
			<input type="hidden" name="action" value="buy_power_tripper" />
			<input type="hidden" name="auth" value="<?=$LoggedUser['AuthKey']?>" />
			<td>
		       <h3> power tripper </h3>
			</td>
			<td>
				<span><?= $Criteria['memer_to_powertripper']['Sheckels'] ?></span>
			</td>
			<td>
				<span>You need 25 uploaded torrents, 4 week membership on the site and 5000 credits.</span>
			</td>
			<td>
				<input <?php echo $mayBuy['power_tripper'] ? '' : 'disabled="true"' ?> type="submit" name="submit" value="Buy power tripper" />
			</td>
		</form>		
    </tr>
    <tr class="row">
		<form class="buy_title" name="buy_title" action="" method="post">
			<input type="hidden" name="action" value="buy_entitled" />
			<input type="hidden" name="auth" value="<?=$LoggedUser['AuthKey']?>" />
			<td>
		       <h3> entitled </h3>
			</td>
			<td>
				<span><?= $Criteria['powertripper_to_entitled']['Sheckels'] ?></span>
			</td>
			<td>
				<span>You need 100 uploaded torrents, 8 week membership on the site and 10000 credits.</span>
			</td>
			<td>
				<input <?php echo $mayBuy['entitled'] ? '' : 'disabled="true"' ?> type="submit" name="submit" value="Buy entitled" />
			</td>
		</form>		
	</tr>
    <tr class="row">
		<form class="buy_title" name="buy_title" action="" method="post">
			<input type="hidden" name="action" value="buy_patrician" />
			<input type="hidden" name="auth" value="<?=$LoggedUser['AuthKey']?>" />
			<td>
		       <h3> Patrician </h3>
			</td>
			<td>
				<span><?=$Criteria['entitled_to_Patrician']['Sheckels'] ?></span>
			</td>
			<td>
				<span>You need 500 uploaded torrents, 16 week membership on the site and 25000 credits.</span>
			</td>
			<td>
				<input <?php echo $mayBuy['Patrician'] ? '' : 'disabled="true"' ?> type="submit" name="submit" value="Buy Patrician" />
			</td>
		</form>		
	</tr>
</table>  
<br /><br />
<table width="100%">
	<tr class="colhead">
		<td>Item</td>
		<td>Price</td>
		<td>You have:</td>
		<td></td>
		<td>Description</td>
		<td>Submit</td>
	</tr>
	<tr class="row">
		<form class="buy_invites"name="buy_invites" action="" method="post">
			<input type="hidden" name="action" value="items_alter" />
			<input type="hidden" name="price" value="300" />
			<input type="hidden" name="auth" value="<?=$LoggedUser['AuthKey']?>" />
			<td>
				<h3> Invites </h3>
			</td>
			<td>
				</span><span>300</span>
			</td>
			<td>
				<span><?=$LoggedUser['Invites']?> invites</span>
			</td>
			<td>
				<span>Buy <input type="number" name="newinvites" value="1" size="2"> invite(s)</input></span>
			</td>
			<td>
				<span>Invite your mates</span>
			</td>
			<td>
				<input type="submit" name="submit" value="Buy Invites" />
			</td>
		</form>
	</tr>
    <tr class="row">
		<form class="buy_title" name="buy_title" action="" method="post">
			<input type="hidden" name="price" value="1000" />
			<input type="hidden" name="action" value="items_alter" />
			<input type="hidden" name="auth" value="<?=$LoggedUser['AuthKey']?>" />
			<td>
					<h3> Custom Title </h3>
			</td>
			<td>
				<span>1000</span>
			</td>
			<td>
				<?= ($LoggedUser['Title'] != "")?$LoggedUser['Title'] : 'None'?>
			</td>
			<td>
				<input type="text" name="customtitle" value="" ></input>
			</td>
			<td>
				<span>Note: Every 'change' counts as a 'buy'</span>
			</td>
			<td>
				Out of Stock <?//<input type="submit" name="submit" value="Change Title" />?>
			</td>
		</form>		
	</tr>
</table>
<? View::show_footer(); ?>
