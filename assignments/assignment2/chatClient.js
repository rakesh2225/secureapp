var net = require('net');
var readlineSync = require('readline-sync');
 
if(process.argv.length != 4) {
	console.log("Usage: node %s <host> <port>", process.argv[1]);
	process.exit(0);	
}

var host=process.argv[2];
var port=process.argv[3];

if(host.length >253 || port.length > 5) {
	console.log("Invalid host or port. Try again!\nUsage: node %s <port>", process.argv[1]);
	process.exit(1);	
}

var client = new net.Socket();
console.log("Simple telnet.js developed by Rakesh, SecAD-S19");
console.log("Connecting to: %s:%s", host, port);

client.connect(port,host, connected);

function connected(){
	console.log("Connected to: %s:%s", client.remoteAddress, client.remotePort);
	console.log("Login to ChatServer before sending/receiving message.\n");
	login();
}


client.on("error", function(err) {
	console.log("Error");
	process.exit(2);
});

var username;
var password;
function login() {
	// Wait for user's response.
	username = readlineSync.question('Username:');
	password = readlineSync.question('Password:', {
  		hideEchoBack: true
	});
	var logindata = "{\"Username\":\"" + username + "\",\"Password\":\"" + password + "\"}";
	console.log(logindata);
	client.write(logindata);
}

client.on("data", data => {
	var response = "" + data;
	console.log(response);
	if (response == "LOGIN_FAILED") {
		console.log("Invalid credentials. Try again.");
		login();
	} else if (response == "LOGIN_SUCCESS") {
		console.log("User Authenticated. You can now message your friends. Type HELP + ENTER for commands.");
		startChat();
	} else if (response == "INVALID_SYNTAX") {
		console.log("Invalid syntax. Try again.");
		help();
	}	
});

client.on("close", function(data) {
	console.log("Connection has been disconnected");
	process.exit(3);
});

function help() {
	console.log("To send a private message, Usage:  To:<username>:<message> + ENTER");
	console.log("To send a public message, Usage:  To:ALL:<message> + ENTER");
	console.log("To get list of online users, Usage:  USERS + ENTER");
	console.log("To add new user, Usage:  ADD:<username>:<password> + ENTER");
}

function startChat() {
	var keyboard = require('readline').createInterface({
  		input: process.stdin,
  		output: process.stdout
	});
	keyboard.on("line", (input) => {
		if (input === ".exit") {
			client.destroy();
			process.exit();
		} if (input === "HELP") {
			help();
		} else {
			client.write(input);
		}
	});
}