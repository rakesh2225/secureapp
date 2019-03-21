package accountmanager

import (
	"fmt"
	"os"
	"io/ioutil"
	"encoding/json"
	"golang.org/x/crypto/pbkdf2"
	"crypto/sha256"
	"encoding/hex"
)

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

func CheckAccount(username string, password string) bool {
	fd, err := os.Open("./users.json")
	defer fd.Close()
	if err != nil {
		fmt.Println("Failed to open users json file")
		os.Exit(1)
	}
	bytes, _ := ioutil.ReadAll(fd)
	var users Users
	json.Unmarshal(bytes, &users)
	fmt.Println(len(users.Users))
	for i := 0; i < len(users.Users); i++ {
		//fmt.Println(users.Users[i].Username + users.Users[i].Password);
		if username == users.Users[i].Username && getHash(username, password) == users.Users[i].Password {
			return true
		}
	}
	return false
}

/*
func VerifyAccount(string username, string password) {
	
}
*/