/* A Simple HTTPS server with socket.IO in Node.js
   by Phu Phung for SecAD-S19
   */
/* ensure that you have the key and certificate files 
   copied to the /etc/ssl folder as in Lab 5.
   change the file name accordingly.*/
var https = require('https'), fs = require('fs');
var sslcertificate  = {
  key: fs.readFileSync('/etc/ssl/secad-s19-rakeshsv.key'),
  cert: fs.readFileSync('/etc/ssl/secad-s19-rakeshsv.crt')
};
var httpsServer = https.createServer(sslcertificate,httphandler);
var socketio = require('socket.io')(httpsServer);

httpsServer.listen(4430); //cannot use 443 as since it reserved for Apache HTTPS
console.log("HTTPS server is listenning on port 4430");

function httphandler (request, response) {
  //console.log("URL requested = " + request.url);
  //read the websocketchatclient.html file and 
  //to create a HTTP Reponse regardless of the requests
  response.writeHead(200); // 200 OK 
  var clientUI_stream = fs.createReadStream('./client.html');
  clientUI_stream.pipe(response);
}

socketio.on('connection', function (socketclient) {
  console.log("A new socket.IO client is connected: "+ socketclient.client.conn.remoteAddress+
               ":"+socketclient.id);
   socketclient.on("message", (data) => {
      console.log("Received data: " + data);
   });
});
