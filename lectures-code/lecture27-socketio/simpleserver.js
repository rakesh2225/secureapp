var http = require("http"), fs = require('fs');
var server = http.createServer(httphandler);
var port = 8080;
server.listen(port);
function httphandler(request,response){
	response.writeHead(200); // 200 OK 
  	var clientUI_stream = fs.createReadStream('./nodejs.html');
  	clientUI_stream.pipe(response);
}
console.log("Server is running at port: " + port);