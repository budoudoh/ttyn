<?php
	if($numTo)
	{
		call($numTo);
		_log("Making Call");
		while(true)
		{
		    wait(30000, array (
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