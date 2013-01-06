<?php
    /*
	 * TTYN.php: Main PHP file for TTYN Application
	 */
	 
	 if($currentCall) //Check if current call object exists
	 {
		$parameters = $currentCall->getParameters();
		
		if(array_key_exists('numToCall', $parameters)) //This is a dial out situation
		{
			$number = $parameters['numToCall'];
			call($number);	
		}
		else 
		{
			//send session ID to server
			$session_id = $currentCall->id;
		}
		
		say("Jeremy, if you are hearing this, then this app is working");
		
	 }
	 else 
	 {
		say("Unfortunatly, your call could not be completed.");	 
	 }
?>