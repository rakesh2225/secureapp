package main

import (
	"fmt"
	"encoding/json"
)

func main() {
	fmt.Println("Parsing JSON")

	database := []byte(`{ "users" : {"rakeshsv1": {"password" : "test123"}, "rakeshsv2" : {"password" : "udayton"}}}`)

	byt := []byte(`{"username":"rakeshsv1", "password":"test123"}`)

	var creds map[string]interface{}
	var credsDB map[string]interface{}
	if err := json.Unmarshal(database, &credsDB); err != nil {
		fmt.Println("Error in database parsing.");
		panic(err)
	}
	if err := json.Unmarshal(byt, &creds); err != nil {
		fmt.Println("Error in user data parsing.")
		panic(err)
	}
	users := credsDB["users"].(map[string]interface{})
	username := creds["username"].(string)
	if users[username] != nil && (users[username].(map[string]interface{}))["password"].(string) == creds["password"].(string) {
		fmt.Println("User authenticated")
	} else {
		fmt.Println("User authentication failed")
	}
}