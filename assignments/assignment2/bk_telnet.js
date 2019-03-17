var net = require('net');
var readlineSync = require('readline-sync')
const keyboard = require('readline').createInterface({
	input: process.stdin,
	output: process.stdout
});
 
if(process.argv.length != 4){
	console.log("Usage: node %s <host> <port>", process.argv[1]);
	process.exit(0);	
}

var host=process.argv[2];
var port=process.argv[3];

if(host.length >253 || port.length >5 ){
	console.log("Invalid host or port. Try again!\nUsage: node %s <port>", process.argv[1]);
	process.exit(1);	
}

var client = new net.Socket();
console.log("Simple telnet.js developed by Rakesh, SecAD-S19");
console.log("Connecting to: %s:%s", host, port);

client.connect(port,host, connected);

function connected(){
	console.log("Connected to: %s:%s", client.remoteAddress, client.remotePort);
}

client.on("data", data => {
	var response = "" + data;
	if (response == "login") {
		var username = readlineSync.question('Username:');
		var password = readlineSync.question('Password:', {
	  		hideEchoBack: true
		});
		var creds = "{\"username:\" " + username + ", \"password:\" " + password + "}";
		client.write(creds);
	}
	console.log("" + data);
});

client.on("error", function(err) {
	console.log("Error");
	process.exit(2);
});

client.on("close", function(data) {
	console.log("Connection has been disconnected");
	process.exit(3);
});

keyboard.on("line", (input) => {
	if (input === ".exit") {
		client.destroy();
		process.exit();
	} if (input === "help") {
		console.log("Choose your option");
		console.log("1 Send private message");
		console.log("2 Send public message");
		console.log("3 List online friends");
	} else if (input === "1") {
		client.write("1");
	} else if (input === "2") {
		client.write("2");
	} else if (input === "3") {
		client.write("3");
	} else {//if (input.toLoer.startsWith("to")) {
		client.write(input);
	}
});