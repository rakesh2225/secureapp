/* include libraries */
#include <stdio.h>
#include <stdlib.h>
#include <ctype.h>
#include <string.h>
#include <netdb.h>
#include <stdbool.h>
#include <sys/types.h>
#include <sys/socket.h>

int isPort(char port[]) {
   int i = 0;
   for (int i = 0; i < strlen(port); i++) {
      if (!isdigit(port[i])) {
         printf("Not digit %c", port[i]);
         return 0;
      }
   }
   return 1;
}

int main (int argc, char *argv[])
{
   printf("TCP client by Rakesh - SecAd Spring 2019\n");
   if (argc != 3) {
      printf("Usage: %s <server name> <port>\n",argv[0]);
      exit(0);
   }
	int BUFFER_SIZE = 1024;
	char buffer[BUFFER_SIZE];
   char *servername = argv[1];
   char *port = argv[2];
   if (strlen(servername) > 255 || strlen(port) > 5 || isPort(port) == 0) {
      perror("Servername and port could not be validated\n");
      exit(1);
   }
   printf("Servername: %s, port: %s\n", servername, port);
	bzero(buffer, BUFFER_SIZE);
	sprintf(buffer, "GET / HTTP/1.1\r\nHost: %s\r\n\r\n", servername);
   int socfd = socket(AF_INET, SOCK_STREAM, 0);
   if (socfd < 0) {
      perror("Error creating socket\n");
      exit(0);
   }
   printf("Socket created and opened for use\n");
   
   struct addrinfo hints, *serveraddr;
   memset(&hints, 0, sizeof hints);
   hints.ai_family = AF_INET;
   hints.ai_socktype = SOCK_STREAM;
   int addr_lookup = getaddrinfo(servername, port, &hints, &serveraddr);
   if (addr_lookup != 0) {
      fprintf(stderr, "getaddrinfo %s\n: ", gai_strerror(addr_lookup));
      exit(1);
   }
   
   int connected = connect(socfd, serveraddr->ai_addr, serveraddr->ai_addrlen);
   if (connected < 0) { 
      perror("Can not connect to server\n");
      exit(3);
   } else {
      printf("Connected to server %s at port %s\n", servername, port);
   }
	//printf("Enter your message to send: ");
	//fgets(buffer, 1024, stdin);
   int bytes_sent = send(socfd, buffer, strlen(buffer), 0);
	
	char output[BUFFER_SIZE];
	bzero(output, BUFFER_SIZE);
	int bytes_received = recv(socfd, output, BUFFER_SIZE, 0);
	if (bytes_received < 0) {
		perror("Error in reading");
		exit(4);
	}
	printf("Received from server: %s", output);
   freeaddrinfo(serveraddr);
   close(socfd);
	return 0;
}
