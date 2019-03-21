/* Simple EchoServer in GoLang by Phu Phung, customized by Rakesh for SecAD-S19*/
package main

import (
	"fmt"
	"net"
	"os"
	"strings"
	"encoding/json"
    //"bitbucket.org/secad-s19/golang/jsondb/users"
    "chatlib"
)

type Account struct {
	Username string
	Password string
}

const BUFFERSIZE int = 1024

var newClientChannel = make(chan net.Conn)
var lostClientChannel = make(chan net.Conn)
var allClient_conns = make(map[net.Conn]string)

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
	server, err := net.Listen("tcp", ":"+port)
	if err != nil {
		fmt.Printf("Cannot listen on port '" + port + "'!\n")
		os.Exit(2)
	}
	fmt.Println("EchoServer in GoLang developed by Rakesh, SecAD-S19")
	fmt.Printf("EchoServer is listening on port '%s' ...\n", port)
	for {
		client_conn, _ := server.Accept()
		go authenticateUser(client_conn)
	}
}

func authenticateUser(client_conn net.Conn) {
	fmt.Println("Authenticating User.")
	go func() {
		for {
			var buffer [BUFFERSIZE]byte
			byte_received, read_err := client_conn.Read(buffer[0:])
			if read_err != nil {
				fmt.Println("Error in receiving...")
				return
			}
			data := buffer[0:byte_received]
			fmt.Printf("Received data: %s\n", buffer)
			var account Account
			if err := json.Unmarshal(data, &account); err != nil {
				sendToClient([]byte("LOGIN_FAILED"), client_conn)
				//panic(err)
			} else {
				if accountmanager.CheckAccount(account.Username, account.Password) {
					sendToClient([]byte("LOGIN_SUCCESS"), client_conn)
					allClient_conns[client_conn] = account.Username
					newClientChannel <- client_conn
					break
				} else {
					sendToClient([]byte("LOGIN_FAILED"), client_conn)
					break
				}
			}
		}
	}()
	for {
		select {
			case client_conn := <-newClientChannel:
				go handleUserMessages(client_conn)
		}
	}
}

func handleUserMessages(client_conn net.Conn) {
	go func() {
		for {
			var buffer [BUFFERSIZE]byte
			byte_received, read_err := client_conn.Read(buffer[0:])
			if read_err != nil {
				fmt.Println("Error in receiving...")
				lostClientChannel <- client_conn
				return
			}
			data := buffer[0:byte_received]
			fmt.Printf("Received data:: %s\n", buffer)
			if string(data) == "USERS" {
				sendOnlineUsers(client_conn)
			} else {
				messageFormat := strings.Split(string(data), ":")
				if len(messageFormat) < 2 || len(messageFormat) > 3 {
					sendToClient([]byte("INVALID_SYNTAX"), client_conn)
				} else {
					if strings.ToLower(messageFormat[0]) == "to" {
						if strings.ToLower(messageFormat[1]) == "all" {
							sendToAllClients(client_conn, allClient_conns[client_conn], messageFormat[2])
						} else {
							sendToUsernames(client_conn, allClient_conns[client_conn], messageFormat[1], messageFormat[2])
						}
					} else if strings.ToLower(messageFormat[0]) == "add" {
						addNewUser(messageFormat[1], messageFormat[2])
					} else {
						sendToClient([]byte("INVALID_SYNTAX"), client_conn)
					}
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

func addNewUser(username string, password string) {
	fmt.Println("Adding new user")
	err := accountmanager.AddUser(username, password)
	if err != nil {
		fmt.Println(err)
	}
}
