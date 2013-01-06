<?php
    /*
	 * TTYN.php: Main PHP file for TTYN Application
	 */
	 
	 if($currentCall)
	 {
	 	$session_id = $currentCall->id;
	 	say("The session ID is ".$session_id);
	 }
	 else 
	 {
		say("The currentCall Object does not exist.");	 
	 }
?>