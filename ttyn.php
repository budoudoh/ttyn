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
	 	global $currentCall;
	 	say("Your call has begun.");
	 	while(true)
		{
			record("", array (
			    "maxTime" => 60,
			    "silenceTimeout" => 2,
			    "transcriptionOutURI" => "https://doublewindsortech.com:8000/",
			    "transcriptionID" => $currentCall->sessionId, 
			    "allowSignals" => '')
			);
			wait(1000, array (
			    "onSignal" => function($event){
	 				say($event->value);		
			    })
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
	 	say("Setting up your phone call using $appname");
	 	$requestBody = json_encode(array('sessionId' => $currentCall->sessionId, 'numFrom'=>$currentCall->callerID));
		$url = $FQDN . "/rest/1/Sessions/".$session_id."/Signals";
	    $content = "Content-Type: application/json";
	 
	    //Invoke the URL
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLINFO_HEADER_OUT, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array (
            $content
        ));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $curl_response = curl_exec($curl);
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
 
        if ($responseCode == 200) 
        {
        	echo $CMS_response;
            /*$jsonResponse = json_decode($CMS_response, true);
            $id = $jsonResponse["id"];
    		$success = $jsonResponse["success"];
    		$_SESSION["id"] = $id;
    		$_SESSION["success"] = $success;*/
		}
	 }
	 else 
	 {
		if($numTo)
		{
			call($numTo);
			_log("Making Call");
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