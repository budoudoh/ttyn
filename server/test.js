var express = require('express'),
    calls = require('./routes/calls'),
    tropowebapi = require('tropo-webapi'),
    https = require("https");
    http = require("http");
    fs = require('fs');
    app = express();


app.configure(function () {
    app.use(express.logger('dev'));     /* 'default', 'short', 'tiny', 'dev' */
    app.use(express.bodyParser());
});
 
//Get all calls
app.get('/calls', calls.findAll);

//Get call by session ID
app.get('/calls/:id', calls.findById);

//Add new call
app.post('/calls', calls.addCall);

//Update call
app.put('/calls/:id', calls.updateCall);

//Start a session without CMS ID
app.post('/startSession', calls.startSession);

//Add session id to call without session
app.post('/findSession', calls.findSession);

// Get from CMS

// Post to CMS

app.post('/CMS', calls.postToCMS);




// Get from SocketIO
//Get calls go here

// Post to SocketIO
// Post calls go here


app.listen(80);
console.log('Version 7 Listening on port 80...');


