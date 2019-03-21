package accountmanager

import (
	"fmt"
	"os"
	"errors"
	"io/ioutil"
	"encoding/json"
	"golang.org/x/crypto/pbkdf2"
	"crypto/sha256"
	"encoding/hex"
)


var users Users

type Users struct {
	Users []User `json:"users"`
}

type User struct {
	Username string `json:"username"`
	Password string `json:"password"`
}

func getHash(salt string, val string) string {
	hashed_data := hex.EncodeToString(pbkdf2.Key([]byte(val), []byte(salt), 4096, 32, sha256.New))
	return hashed_data	
}

func loadUsers() {
	fmt.Println("Loading users.")
	fd, err := os.Open("./users.json")
	defer fd.Close()
	if err != nil {
		fmt.Println("Failed to open users json file")
		os.Exit(1)
	}
	bytes, _ := ioutil.ReadAll(fd)
	json.Unmarshal(bytes, &users)
}

func usersExists(username string) bool {
	loadUsers()
	for i := 0; i < len(users.Users); i++ {
		if username == users.Users[i].Username {
			return true
		}
	}
	return false
}

func CheckAccount(username string, password string) bool {
	fmt.Println("Checking database for user")
	loadUsers()
	fmt.Println(len(users.Users))
	for i := 0; i < len(users.Users); i++ {
		if username == users.Users[i].Username && getHash(username, password) == users.Users[i].Password {
			return true
		}
	}
	return false
}

func AddUser(username string, password string) error {
	fmt.Println("Adding new user")
	loadUsers()
	if usersExists(username) {
		return errors.New("User already exists: " + username)
	}
	users.Users = append(users.Users, User{Username: username, Password: getHash(username, password)})
	write_err := updateUsers()
	if write_err != nil {
		return write_err
	}
	return nil	
}

func updateUsers() error {
	fmt.Println("Updating users database")
	if users.Users == nil {
		json.Unmarshal([]byte(`{"users":[]}`), & users)
	}
	database, ind_err := json.MarshalIndent(users, "", " ")
	if ind_err != nil {
		return errors.New("User could not be formatted to JSON")
	}
	write_err := ioutil.WriteFile("users.json", database, 0644)
	if write_err != nil {
		return write_err	
	}
	fmt.Println("User added to the database")
	return nil
}