<?php
	if($numTo)
	{
		call($numTo);
		_log("Making Call");
		while(true)
		{
		    record("", array (
					    "maxTime" => 60,
					    "silenceTimeout" => 2,
					    "transcriptionOutURI" => "https://doublewindsortech.com:8000/",
					    "transcriptionID" => $currentCall->sessionId, 
					    "allowSignals" => 'exit',
					    "onSignal" => function($event){
					    	_log("Testing");
							say("Your call has been interrupted!");
			 				say($event->value);		
					    })
					);
			say("Recording is done!");
		}
	}
?>