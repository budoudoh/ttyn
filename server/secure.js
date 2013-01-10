var express = require('express'),
    calls = require('./routes/calls'),
    https = require('https'),
    fs = require('fs'),
    crypto = require('crypto');
    app = express();

var options = {
	key: fs.readFileSync('privatekey.pem'),
	cert: fs.readFileSync('certificate.pem')
};

https.createServer(options, app).listen(443);

app.configure(function () {
    app.use(express.logger('dev'));     /* 'default', 'short', 'tiny', 'dev' */
    app.use(express.bodyParser());
});
 
//Add session id to call without session
console.log(calls);

app.post('/shipIt', calls.sendToIO);

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


app.post('/CMS', calls.postToCMS);



console.log('Version Secure - Listening on port 443...');


