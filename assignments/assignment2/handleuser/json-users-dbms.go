package main

import (
	"os"
	"fmt"
	"bitbucket.org/secad-s19/golang/jsondb/users"
)


var (
	username string
	password string
)
func usage(option string) {
	fmt.Printf("Usage: %s %s\n", os.Args[0], option)
	os.Exit(0)
	
}
func main(){
	if len(os.Args) < 2 {
		usage("--show|--check[2]|--remove|--add|--update [username] [password]")
	}
	option := os.Args[1]
	if len(os.Args) > 2 {
		username = os.Args[2]		
	}
	if len(os.Args) > 3 {
		password = os.Args[3]	
	}
	switch option{
		case "--show":
			users.ListAllAccounts()
		
		case "--remove":
			if username == "" {
				usage("--remove <username>")	
			}
			rm_err := users.RemoveAccount(username)
			if rm_err != nil {
				fmt.Printf("RemoveAccount error: %s\n", rm_err)
				return
			}
			fmt.Printf("Account '%s' is deleted\n",username)

		case "--check": 
			if username == "" || password == ""{
				usage("--check <username> <password>")	
			}
			if users.CheckAccount(username,password) {
				fmt.Printf("Testing: Account('%s','%s') is valid\n",username,password)
			} else {
				fmt.Printf("Testing: Account('%s','%s') is invalid\n",username,password)
			}	
		case "--check2": 
			if username == "" || password == ""{
				usage("--check2 <username> <password>")	
			}
			if users.CheckAccountHashed(username,password) {
				fmt.Printf("Testing: CheckAccountHashed('%s','%s') is valid\n",username,password)
			} else {
				fmt.Printf("Testing: CheckAccountHashed('%s','%s') is invalid\n",username,password)
			}	
		case "--update":
			if username == "" || password == ""{
				usage("--update <username> <password>")	
			}
			upd_err := users.UpdateAccount(username,password)
			if upd_err != nil {
				fmt.Printf("UpdateAccount error: %s\n", upd_err)
				return
			}
			fmt.Printf("Account '%s' is updated with new password\n",username)

		case "--add":
			if username == "" || password == ""{
				usage("--add <username> <password>")	
			}
			add_err := users.AddAccount(username,password)
			if add_err != nil {
				fmt.Printf("AddAccount error: %s\n", add_err)
				return
			}
			fmt.Printf("Account '%s' added\n",username)
		
		default:
			fmt.Printf("%s: Unknown option\n", os.Args[0])
			usage("--show|--check[2]|--remove|--add|--update [username] [password]")
	}
}

