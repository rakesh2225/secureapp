/* Simple EchoServer in GoLang by Phu Phung, customized by Rakesh for SecAD-S19*/
package main

import (
	"fmt"
	"net"
	"os"
	"strings"
	"encoding/json"
   "bitbucket.org/secad-s19/golang/jsondb/users"
)

const BUFFERSIZE int = 1024

var allClient_conns = make(map[net.Conn]string)

var database = []byte(`{ "users" : {"rakeshsv1": {"password" : "test123"}, "rakeshsv2" : {"password" : "udayton"}}}`)

func sendToClient(data []byte, client_conn net.Conn) {
	_, write_err := client_conn.Write(data)
	if write_err != nil {
		fmt.Println("Error in receiving...")
		return
	}
}

func sendToUsernames(fromClient net.Conn, fromUser string, toUser string, message string) {
	sendMessage := fromUser + ": " + message
	for client_conn, user := range allClient_conns {
		if client_conn != fromClient && user == toUser {
			if user == fromUser {
				sendMessage = "You: " + message 
			}
			_, write_err := client_conn.Write([]byte(sendMessage))
			if write_err != nil {
				fmt.Println("Error in sending message to %s", toUser)
			}
		}
		sendMessage =  fromUser + ": " + message
	}
}

func sendToAllClients(fromClient net.Conn, fromUser string, message string) {
	sendMessage := fromUser + ": " + message	
	for client_conn, user := range allClient_conns {
		if client_conn != fromClient {
			if user == fromUser {
				sendMessage = "You: " + message
			}
			_, write_err := client_conn.Write([]byte(sendMessage))
			if write_err != nil {
				fmt.Println("Error in sending message to clients...")
			}
		}
		sendMessage = fromUser + ": " + message
	}
}

func getOnlineUsers() (onlineUsers string) {
	onlineUsers = "Online Users: \n"
	set := make(map[string]string)
	for _, user := range allClient_conns {
		set[user] = user
	}
	for _, user := range set {
		onlineUsers = onlineUsers + user + "\n"
	}
	return
}

func sendOnlineUsers(toClient net.Conn) {
	onlineUsers := getOnlineUsers()
	_, write_err := toClient.Write([]byte(onlineUsers))
	if write_err != nil {
		fmt.Println("Error in Sending online users...")
	}
}

func authenticateUser(creds map[string]interface{}, client_conn net.Conn) {
	username := creds["username"].(string)
	password := creds["password"].(string)
	if users.CheckAccount(username, password) {
		allClient_conns[client_conn] = username
		sendToClient([]byte("User authenticated. You can now message your friends."), client_conn)
	} else {
		sendToClient([]byte("User authentication failed, try again"), client_conn)
	}
}

func handleNewClient(client_conn net.Conn) {
	var buffer [BUFFERSIZE]byte
	askCreds := "Login to chat server.\nUsage: {\"username\":\"<your_username>\", \"password\":\"<your_password>\"} and press Enter\n"
	sendToClient([]byte(askCreds), client_conn)
	lostClientChannel := make(chan net.Conn)
	go func() {
		for {
			byte_received, read_err := client_conn.Read(buffer[0:])
			if read_err != nil {
				fmt.Println("Error in receiving...")
				lostClientChannel <- client_conn
				return
			}
			data := buffer[0:byte_received]
			fmt.Printf("Received data: %s\n", buffer)
			//buffer = nil
			//buffer = make([BUFFERSIZE]byte)
			if string(data) == "3" {
				sendOnlineUsers(client_conn)
			}
			if fromUserName, ok := allClient_conns[client_conn]; ok {
				if string(data) == "1" {
					onlineUsers := getOnlineUsers()
					sendToClient([]byte("Online Users: " + onlineUsers + "\nUsage: 'To:<username>:<message> + [ENTER]'"), client_conn)
				} else if string(data) == "2" {
					sendToClient([]byte("Usage: 'To:All:<message> + [ENTER]'"), client_conn)
				} else {
					messageFormat := strings.Split(string(data), ":")
					if len(messageFormat) < 2 {
						sendToClient([]byte("To send message to user, \nUsage: `To:<username>:<your_message>` and ENTER"), client_conn)
					} else {
						if strings.ToLower(messageFormat[0]) == "to" {
							if strings.ToLower(messageFormat[1]) == "all" {
								sendToAllClients(client_conn, fromUserName, messageFormat[2])
							} else {
								sendToUsernames(client_conn, fromUserName, messageFormat[1], messageFormat[2])
							}
						} else {
							sendToClient([]byte("To send message to user, \nUsage: `To:<username>:<your_message>` and ENTER"), client_conn)
						}
					}
				}
				
			} else {
				var creds map[string]interface{}
				if err := json.Unmarshal(data, &creds); err != nil {
					sendToClient([]byte("Please login first to use the chat server.\nUsage: {\"username\":\"<your_username>\", \"password\":\"<your_password>\"} and press Enter\n"), client_conn)
					//panic(err)
				} else {
					authenticateUser(creds, client_conn)
				}				
			}
		}
	}()
	
	for {
		select {
			case client_conn := <-lostClientChannel:
				username := allClient_conns[client_conn]
				message := username + " with IP " + client_conn.RemoteAddr().String() + ": has exited chat room.\n"
				delete(allClient_conns, client_conn)
				go sendToAllClients(client_conn, "Server", message)				
				fmt.Printf("Total connected clients: %d\n", len(allClient_conns))
		}
	}
	
}

func main() {
	if len(os.Args) != 2 {
		fmt.Printf("Usage: %s <port>\n", os.Args[0])
		os.Exit(0)
	}
	port := os.Args[1]
	if len(port) > 5 {
		fmt.Println("Invalid port value. Try again!")
		os.Exit(1)
	}
	server, err := net.Listen("tcp", ":" + port)
	if err != nil {
		fmt.Printf("Cannot listen on port '" + port + "'!\n")
		os.Exit(2)
	}
	fmt.Println("EchoServer in GoLang developed by Rakesh, SecAD-S19")
	fmt.Printf("EchoServer is listening on port '%s' ...\n", port)
	newClientChannel := make(chan net.Conn)
	go func() {
		for {
			fmt.Println("Accepting")
			client_conn, _ := server.Accept()
			fmt.Println("New Client")
			newClientChannel <- client_conn
		}
	}()
	for {
		select {
			case client_conn := <-newClientChannel:
				go handleNewClient(client_conn)
		}
	}
}
