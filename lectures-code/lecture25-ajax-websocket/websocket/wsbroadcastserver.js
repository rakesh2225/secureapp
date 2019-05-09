const WebSocket = require('ws');
const wsserver = new WebSocket.Server({port: 8001 });
console.log("WebSocket Server is listening on port: " + wsserver.address().port);
wsserver.on('connection', function connection(wsclient, request) {
  	const clientID = request.connection.remoteAddress + ":" + request.connection.remotePort;
  	console.log("A new connection from: "+ clientID);
	wsclient.on('message', function incoming(data) {
		console.log("Received from " + clientID + ", data=" + data);
    		wsserver.clients.forEach(function each(client) {
    			client.send(clientID + " says " + data);
    		});
  	});
});
