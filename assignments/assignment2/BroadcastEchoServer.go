/* Simple EchoServer in GoLang by Phu Phung, customized by Rakesh for SecAD-S19*/
package main

import (
	"fmt"
	"net"
	"os"
	//"strings"
	"encoding/json"
)

const BUFFERSIZE int = 1024

var allClient_conns = make(map[string]net.Conn)

var credsDB map[string]interface{}

var database = []byte(`{ "users" : {"rakeshsv1": {"password" : "test123"}, "rakeshsv2" : {"password" : "udayton"}}}`)

func init() {
	fmt.Println("Initializing...")
	if err := json.Unmarshal(database, &credsDB); err != nil {
		fmt.Println("Error in database parsing.");
		panic(err)
		os.Exit(1)
	}
}

func sendToClient(data []byte, client_conn net.Conn) {
	_, write_err := client_conn.Write(data)
	if write_err != nil {
		fmt.Println("Error in receiving...")
		return
	}
}
/*
func sendAll(data []byte) {
	for client_conn := allClient_conns {
		_, write_err := client_conn.Write(data)
		if write_err != nil {
			fmt.Println("Error in receiving...")
			return
		}
	}
}
*/

func authenticateUser(creds map[string]interface{}, client_conn net.Conn) {
	fmt.Println("Authenticating user...")
	users := credsDB["users"].(map[string]interface{})
	
	username := creds["username"].(string)
	if users[username] != nil && (users[username].(map[string]interface{}))["password"].(string) == creds["password"].(string) {
		allClient_conns[username] = client_conn
		sendToClient([]byte("User authenticated. You can now message your friends."), client_conn)
	} else {
		sendToClient([]byte("User authentication failed, try again"), client_conn)
	}
}

//func client_goroutine(client_conn net.Conn, allClient_conns map[net.Conn]string) {
func handleNewClient(client_conn net.Conn) {
	var buffer [BUFFERSIZE]byte
	askCreds := "Enter your credentials.\nUsage: {\"username\":\"<your_username>\", \"password\":\"<your_password>\"} and press Enter\n"
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
			var creds map[string]interface{}
			if err := json.Unmarshal(data, &creds); err != nil {
				fmt.Println("Error in user data parsing.")
				panic(err)
			}
			username := creds["username"].(string)
			if (allClient_conns[username] != nil) {
				fmt.Println("Welcome " + username)
			} else {
				authenticateUser(creds, client_conn)
			}
		}
	}()
	/*
	for {
		select {
			case client_conn := <-lostClientChannel:
			fmt.Printf("Test123");
			clientInfo := allClient_conns[client_conn]
			delete(allClient_conns, client_conn)	
			var str []string
			str = append(str, clientInfo)
			str = append(str, ": Disconnected.\n")
			go sendAll([]byte(strings.Join(str, "")))
			fmt.Printf("Total connected clients: %d\n", len(allClient_conns))
		}
	}
	*/
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
	newClientChannel := make(chan net.Conn)
	go func() {
		for {
			client_conn, _ := server.Accept()
			//go client_goroutine(client_conn, allClient_conns)
			//go client_goroutine(client_conn)
			newClientChannel <- client_conn
		}
	}()
	for {
		select {
			case client_conn := <-newClientChannel:
				fmt.Printf("A new client '%s' connected!\n", client_conn.RemoteAddr().String())
				//allClient_conns[client_conn] = client_conn.RemoteAddr().String()
				go handleNewClient(client_conn)
		}
	}
}
