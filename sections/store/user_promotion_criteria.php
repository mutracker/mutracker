<?php
  
define('DAY_IN_SECONDS', 3600 * 24);
define('ONE_WEEK', DAY_IN_SECONDS * 7);
define('TWO_WEEKS', ONE_WEEK * 2);
define('FOUR_WEEKS', TWO_WEEKS * 2);
define('EIGTH_WEEKS', FOUR_WEEKS * 2);
define('SIXTEEN_WEEKS', EIGTH_WEEKS * 2);

define('PLEB', USER);
define('MEMER', MEMBER);
define('POWER_TRIPPER', POWER);
define('ENTITLED', ELITE);
define('PATRICIAN', POWER_TM);


$mayBuy = array(
      'memer'         => false,
      'power_tripper' => false,
      'entitled'      => false,
      'Patrician'     => false
  );
$Criteria = array();
//from pleb to memer 15 torrents uploaded, 2 week membership and 1500 sheckels
$Criteria['pleb_to_memer'] = array(
    'From'       => PLEB,
    'To'         => MEMER, 
    'MinUploads' => 15,
    'MaxTime'    => time() - TWO_WEEKS,
    'Sheckels'   => 1500);
//memer to power tripper 25 torrents uploaded, 4 week membership and 5000 sheckels
$Criteria['memer_to_powertripper'] = array(
    'From'       => MEMER,
    'To'         => POWER_TRIPPER,
    'MinUploads' => 25,
    'MaxTime'    => time() - FOUR_WEEKS,
    'Sheckels'   => 5000);
//power tripper to entitled 100 torrents uploaded, 8 week membership and 10000 sheckels
$Criteria['powertripper_to_entitled'] = array(
    'From'       => POWER_TRIPPER, 
    'To'         => ENTITLED,
    'MinUploads' => 100,
    'MaxTime'    => time() - EIGTH_WEEKS,
    'Sheckels'   => 10000);

//entitled to patrician 500 torrents uploaded, 16 week membership and 25000 sheckels
$Criteria['entitled_to_Patrician'] = array(
    'From'       => ENTITLED, 
    'To'         => PATRICIAN,
    'MinUploads' => 500, 
    'MaxTime'    => time() - SIXTEEN_WEEKS,
    'Sheckels'   => 25000);
	
if($LoggedUser['Warned'] == '0000-00-00 00:00:00')
{
  $sql = "SELECT
        p.Id as Class,
        m.Enabled,
        UNIX_TIMESTAMP(i.JoinDate) as JoinDate,
        m.Credits as Sheckels
    FROM users_main AS m 
        JOIN users_info AS i ON i.UserID = m.ID 
        LEFT JOIN permissions AS p ON p.ID = m.PermissionID 
    WHERE m.ID = %d";
  
  $DB->query(sprintf($sql, (int) $LoggedUser['ID'] )); 
  
  $relevantData = $DB->next_record(MYSQL_ASSOC);
  
  $sql = "SELECT COUNT(ID)
		FROM torrents
		WHERE UserID = %d";
  
  $DB->query(sprintf($sql, (int) $LoggedUser['ID']));
  
  list($relevantData['Uploads']) = $DB->next_record();
  
  //rd and $c because I don't feel like writing long ass var names
  $rd = $relevantData;
  $c = $Criteria['pleb_to_memer'];
  if(($rd['Uploads'] >= $c['MinUploads'])
      && (($c['MaxTime'] - $rd['JoinDate']) >= TWO_WEEKS)
      && ($rd['Sheckels'] >= $c['Sheckels'])
      && ($rd['Class'] == PLEB)){
    $mayBuy['memer'] = true;
  }
  
  $c = $Criteria['memer_to_powertripper'];
  if(($rd['Uploads'] >= $c['MinUploads'])
      && (($c['MaxTime'] - $rd['JoinDate']) >= FOUR_WEEKS)
      && ($rd['Sheckels'] >= $c['Sheckels'])
      && ($rd['Class'] == MEMER)){
    $mayBuy['power_tripper'] = true;
  }
  
  
  $c = $Criteria['powertripper_to_entitled'];
  if(($rd['Uploads'] >= $c['MinUploads'])
      && (($c['MaxTime'] - $rd['JoinDate']) >= EIGTH_WEEKS)
      && ($rd['Sheckels'] >= $c['Sheckels'])
      && ($rd['Class'] == POWER_TRIPPER))
  {
    $mayBuy['entitled'] = true;
  }
  
  $c = $Criteria['entitled_to_Patrician'];
  if(($rd['Uploads'] >= $c['MinUploads'])
      && (($c['MaxTime'] - $rd['JoinDate']) >= SIXTEEN_WEEKS)
      && ($rd['Sheckels'] >= $c['Sheckels'])
      && ($rd['Class'] == ENTITLED)){
    $mayBuy['Patrician'] = true;
  }
  
}
$c = null;