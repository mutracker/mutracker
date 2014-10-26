<? View::show_header('Irc'); 
$name = $LoggedUser['Username'];

echo '<iframe src="https://kiwiirc.com/client/irc.mutracker.org/?&theme=cli&nick='.$name.'#mutracker" style="border:0; width:100%; height:550px;">';
echo '</iframe>';
 View::show_footer(); ?>
