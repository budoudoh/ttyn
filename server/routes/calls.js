var fs = require('fs');
var io = require('socket.io-client');
var socket = io.connect('http://atthack.cloudapp.net', {reconnect: true}); 
var https= require("https");

exports.findAll = function(req, res) {
    res.send([{name:'call1'}, {name:'call2'}, {name:'call3'}]);
};
 
exports.findById = function(req, res) {
    res.send({id:req.params.id, name: "The Name", description: "description"});
};

exports.addCall = function(req, res) {
    var call = req.body;
    console.log('Adding call: ' + JSON.stringify(call));
}
 
exports.updateCall = function(req, res) {
    var id = req.params.id;
    var call = req.body;
    console.log('Updating call: ' + id);
    console.log(JSON.stringify(call));
}

exports.startSession = function(req, res) {
    console.log('Entering startSession');
    var callerName = req.body.callerName;
    var callerNumber = req.body.callerNumber;
    fs.writeFile('session.sla', callerNumber, function (err) {
        if (err) console.log('fail: ' + err);
        console.log('It\'s saved!');
    });
    fs.writeFileSync('session.sla', callerNumber, 'UTF8');
    var userName = req.body.userName;    
    var userNumber = req.body.userNumber;
    console.log('Getting callerName: ' + callerName);
    console.log('Getting callerNumber: ' + callerNumber);
    console.log('Getting userName: ' + userName);    
    console.log('Getting userNumber: ' + userNumber);
    //getSessionID();
    //forwardSessionToIO();
    
    // Returns the JSON to the Calling device
   getSessionID(userNumber, 
   	callerNumber, 
   	callerName, 
   	function(ttynJSON)
   	{
   		console.log('Returning the JSON to the device: ' + JSON.stringify(ttynJSON));
   		forwardSessionToIO(ttynJSON.id);
	    res.json(ttynJSON);
   	}, 
   	function(response)
   	{
   		
   	});
   	
   /*for (var i = 0; i < 1; i++) {
   (
       function(i) {
          console.log('looping');   
        
	  setTimeout(function () {
	       if(sessionID) {
    		  console.log('Returning the JSON to the device: ' + JSON.stringify(ttynJSON));
	          i = 1;
	          
              res.json(ttynJSON);
	       }
            }, 1000);
        }
    )(i)};*/
}

exports.findSession = function(req, res) {
    console.log('Entering findSession');
    var callerNumber = fs.readFileSync('session.sla', 'UTF8');
    console.log(callerNumber);
    sessionID = req.body.sessionID;
    console.log(req.body);
    var tempCaller = req.body.callerNumber;
	if (tempCaller === callerNumber && callerNumber){
		//Match
		console.log("We have a match!");
		id = sessionID;
	} else {
		console.log("No caller match");
		// Code to handle no match
	}
}

exports.postToCMS = function(req, res){
   console.log('Entering postToCMS'); 
   getSessionID();
   // Forward Session ID on to Socket.IO
   //forwardSessionToIO(sessionID);

} 

exports.sendToIO = function(req, res)
{
	var results = req.body.result;
	if(socket != null)
	{
		socket.emit('user message', results.transcription);
	}
}
/*
 * getSessionID: returns the sessionID to the callback
 */
function getSessionID(userNumber, callerNumber, callerName, success, failure)
{
	console.log('Entering getSessionID');
    // Create session
    // numTo: The number that needs to be called
    // numFrom: The number of the caller
    // caller: The name of the caller
    // Authorization: Bearer b31765d63752f0664e7fa350dbe8e3f7    
    
    var options = {
		host: 'api.att.com',
		path: '/rest/1/Sessions',
		method: 'POST',
		headers: {
			"Content-Type": "application/json",
			Authorization: "Bearer b31765d63752f0664e7fa350dbe8e3f7"
		}
    };

    var post_data=JSON.stringify({
		numTo: userNumber,
		numFrom: callerNumber,
		caller: callerName
    });

    // Post to CMS API
    console.log('Posting to CMS');

    var req = https.request(options, function(res) {
  	console.log("In the request.  status code is " + res.statusCode);
  	if(res.statusCode == 200){
		console.log("StatusCode = 200");
 		res.setEncoding('utf8');
 		res.on('data', function (chunk) {
   			console.log('BODY: ' + chunk);
			ttynJSON = JSON.parse(chunk);
			success(ttynJSON);
			/*sessionID = ttynJSON.id;
			console.log('sessionID: ' + sessionID);
			console.log('ttynJSON: ' + JSON.stringify(ttynJSON));*/
 		});
		
 	}
 	else
 	{
 		failure(res);
 	}
    });

	req.write(post_data);
	req.end();
	req.on('error', function(e) {
  		console.error(e);
	});
//    return ttynJSON;	
}

function newMessage() {

}

function forwardSessionToIO(sessionID)
{
   console.log('Forwarding the session on to the Socket.IO Server ' + sessionID);

  // if(io.sockets.clients() === 0) {
	//   socket.on('connect', function() { 
   		//call the join room function on the server passing in the room that you would like to join
	//socket.emit('join room', sessionID);
	//console.log('joined room ' + sessionID);
   	//});
   	//} else {
   	//socket.emit('join room', sessionID);
	//	console.log('joined room ' + sessionID);
  	// }
	socket.emit('join room', sessionID);

   //We got a new message!!
	socket.on('user message', message);
	function message (from, sessionid, msg) {
	//pass the sessionid and msg through to the CMS server so it can be spoken back to the 
	//appropriate user.
	console.log('Got something: ' + from + ' ' + sessionid);
	var options = {
		host: 'api.att.com',
		path: '/rest/1/Sessions/' + sessionID + '/Signals',
		method: 'POST',
		headers: {
			"Content-Type": "application/json",
			Authorization: "Bearer b31765d63752f0664e7fa350dbe8e3f7"
		}
	};

    var post_data=JSON.stringify({
		signal: sessionid,
    });

    // Post to CMS API
    console.log('Posting to CMS');

    var req = https.request(options, function(res) {
	  	console.log("In the request.  status code is " + res.statusCode);
	  	if(res.statusCode == 200)
	  	{
			console.log("StatusCode = 200");
	 		res.setEncoding('utf8');
	 		res.on('data', function(chunk) {});
			
	 	}
    });

	req.write(post_data);
	req.end();
	req.on('error', function(e) {
  		console.error(e);
	});
	} 
}

function populateTtynJSON(sessionID)
{
	console.log('Entering populateTtynJSON');
	ttynJSON = JSON.parse(sessionID);
}
