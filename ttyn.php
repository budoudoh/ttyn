<?php
    /*
	 * TTYN.php: Main PHP file for TTYN Application
	 */
	 
	 if($currentCall) //Check if current call object exists
	 {
		
	 }
	 else 
	 {
		if($numToCall)
		{
			call($numToCall);	
			say("Jeremy, if you are hearing this, then this app is working");
		}	 
	 }
?>