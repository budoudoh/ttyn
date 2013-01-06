<?php
    /*
	 * TTYN.php: Main PHP file for TTYN Application
	 */
	 $appname = "Talk To Me Now";
	 function initialChoice($event)
	 {
	 	if($event->value == 1)
		{
			callRecorder();		
		}
		else 
		{
			sendMessage();
		}
	 }
			 
	 function callRecorder()
	 {
	 	say("Your call has begun.");
	 	while(true)
		{
			record(array (
			    "maxTime" => 30,
			    "recordURI"=>"http://example.com/recording.php",
			    "transcriptionOutURI" => "mailto:budoudoh@gmail.com",
			    "transcriptionID" => $currentCall->id
			    )
			);
		}
	 }
	 
	 function sendMessage()
	 {
	 	ask("Please Leave your message", 
		array(
		        "timeout" => 15.0,
		        "mode" => "speech",
		        "speechCompleteTimeout" => 15.0,
		        "onChoice" => "messageSender"
		 ));
		
	 }
	 
	 function messageSender($event)
	 {
	 	global $appname;
	 	if($numFrom)
		{
			say("Your Message has been sent. Thank you for using $appname");
			hangup();
			call($numFrom, array("network" => "SMS"));
			say ($event->value);
			hangup();
		}
		else 
		{
			say("Your Message could not be sent. Thank you for using $appname.");
			hangup();	
		}
	 }
	 
	 if($currentCall) //Check if current call object exists
	 {
		
	 }
	 else 
	 {
		if($numTo)
		{
			call($numTo);
			
			$initial_speach = "You are receiving a call from $appname.";
			if($caller)
			{
				$initial_speach = $caller." is would like to chat with you using $appname.";
			}
			
			$initial_speach = $initial_speach." If you would like to continue, press 1. If not, press 2 to send the caller a message.";
			
			ask($initial_speach, array(
					"choices" => "[1 DIGITS]",
			        "timeout" => 15.0,
			        "mode" => "dtmf",
			        "interdigitTimeout" => 2,
			        "onChoice" => "initialChoice"
			 ) ); 
			 
		}	 
	 }
?>