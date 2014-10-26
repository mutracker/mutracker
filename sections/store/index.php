<?
enforce_login();
include(SERVER_ROOT.'/classes/validate.class.php');
$Val = new VALIDATE;

if (empty($_REQUEST['action'])) {
	$_REQUEST['action'] = '';
}

switch ($_REQUEST['action']) {
	case 'items_alter':
		include('sections/store/takebuy.php');
		break;
	case 'buy_memer':
		include('sections/store/takebuy.php');
		break;
	case 'buy_power_tripper':
		include('sections/store/takebuy.php');
		break;
	case 'buy_entitled':
		include('sections/store/takebuy.php');
		break;
	case 'buy_patrician':
		include('sections/store/takebuy.php');
		break;
      default:
		include('sections/store/store.php');
		break;
}
?>
