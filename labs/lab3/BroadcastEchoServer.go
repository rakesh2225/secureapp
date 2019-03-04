/* Simple EchoServer in GoLang by Phu Phung, customized by Rakesh for SecAD-S19*/
package main

import (
	"fmt"
	"net"
	"os"
	"strings"
)

const BUFFERSIZE int = 1024

var allClient_conns = make(map[net.Conn]string)

func sendAll(data []byte) {
	for client_conn := range allClient_conns {
		_, write_err := client_conn.Write(data)
		if write_err != nil {
			fmt.Println("Error in receiving...")
			return
		}
	}
}

//func client_goroutine(client_conn net.Conn, allClient_conns map[net.Conn]string) {
func client_goroutine(client_conn net.Conn) {
	var buffer [BUFFERSIZE]byte
	fmt.Printf("Total connected clients: %d\n", len(allClient_conns))
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
			go sendAll(data)
			fmt.Printf("Received data: %sEchoed back!\n", buffer)
		}
	}()
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
				allClient_conns[client_conn] = client_conn.RemoteAddr().String()
				go client_goroutine(client_conn)
		}
	}
}
