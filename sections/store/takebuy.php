<?
authorize();
View::show_header('The Mutracker Store');			
?> <center class="thin"> <?
require(SERVER_ROOT.'/sections/store/user_promotion_criteria.php');
$P = db_array($_POST);

$Cost_Invite 		= 300;
$Cost_CustomTitle 	= 1000;

if ($_POST['submit'] == 'Buy Invites')
{ 
	if (!is_number($_POST['newinvites']) || $_POST['newinvites'] == '' || $_POST['price'] < 0 || !is_number($_POST['price']) ) 
	{
		?><h1>Invalid input</h1><br>
		<a href="store.php">Click here to go back</a><?
	}
	else
	{
		// Get info
		$DB->query('
			 SELECT
				Credits,
				Invites
			FROM users_main
			 WHERE ID = '.$LoggedUser['ID']
		);
		$Cost = $Cost_Invite * $_POST['newinvites'];
		list($UserCredits, $UserInvites) = $DB->next_record();
		if( $UserCredits <  ($Cost) || $_POST['newinvites'] <= 0 ) // Check if user has enough credits and not negative
		{
			?><h1>Not enough credits or invalid number entered.</h1><br>
			<a href="store.php">Click here to go back</a><?
		}
		else
		{
			$UserInvites = $UserInvites + $_POST['newinvites'];
			
			// Update DB
			$DB->query('
				UPDATE users_main
				SET
					Invites = '.$UserInvites.',
					Credits = Credits - '.$Cost.'
				WHERE ID = '.$LoggedUser['ID']
			);
			
			$DB->query('
				SELECT Credits, Invites FROM users_main
				WHERE ID = '.$LoggedUser['ID']
			);
			// Update Cache
			list($Balance, $Invites) = $DB->next_record();

			$Cache->begin_transaction('user_info_'.$UserID);
			$Cache->update_row(false, array('Credits' => $Balance));
			$Cache->commit_transaction(0);

			$UserID = $LoggedUser['ID'];
			$Cache->begin_transaction("user_info_heavy_$UserID");
			$Cache->update_row(false, array('Invites' => $Invites));
			$Cache->commit_transaction(0);
				
			
			?>You have purchased <?=$_POST['newinvites']?> invites! <br>
			<a href="store.php">Click here to go back</a><?
		}
	}
} else if($_POST['submit'] == 'Change Title')
{
	echo 'change title shit';
}

$action = filter_input(INPUT_POST, 'action'
              , FILTER_SANITIZE_STRING);

if(false !== array_search($action, array('buy_memer', 'buy_power_tripper', 'buy_entitled', 'buy_Patrician'))){
  $UserID = (int) $LoggedUser['ID'];
  
  if ($action == 'buy_memer' && $mayBuy['memer']){
    $cr = $Criteria['pleb_to_memer'];  
  } 
  else if ($action == 'buy_power_tripper' && $mayBuy['power_tripper']){
    $cr = $Criteria['memer_to_powertripper']; 
  }
  else if ($action == 'buy_entitled' && $mayBuy['entitled']){
    $cr = $Criteria['powertripper_to_entitled']; 
  }
  else if($action == 'buy_Patrician' && $mayBuy['Patrician']){
    $cr = $Criteria['entitled_to_Patrician']; 
  }
  
  if(!empty($cr))
  {
    $sql = "UPDATE users_main
                  SET PermissionID = ".$cr['To']."
                  WHERE ID=%s";
    $DB->query(sprintf($sql, $UserID));
    
    $DB->query("UPDATE users_info
                SET AdminComment = CONCAT('".sqltime()." - Class changed to ".Users::make_class_string($cr['To'])." by System\n\n', AdminComment)
                WHERE UserID = $UserID");
    $DB->query('UPDATE users_main
                  SET Credits = Credits - '.$cr['Sheckels'].'
                  WHERE ID = '.$UserID);
    $Cache->delete_value("user_info_$UserID");
    $Cache->delete_value("user_info_heavy_$UserID");
    $Cache->delete_value("user_stats_$UserID");
    $Cache->delete_value("enabled_$UserID");
    
    ?>
      <h1>You are now a(n) <?=Users::make_class_string($cr['To']) ?>, congratulations!</h1><br>
      <a href="/store.php">Click here to go back</a>
    <?
  } 
  else{
    
    ?>
      <h1>You are not allowed to leave the <?=Users::make_class_string($rd['Class']) ?> class.</h1><br>
      <a href="/store.php">Click here to go back</a>
    <?
  }
  
}
?> </center> <?
View::show_footer();		
?>
