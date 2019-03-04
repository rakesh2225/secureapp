/* include libraries */
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <netdb.h>
#include <sys/types.h>
#include <sys/socket.h>

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
   if (strlen(servername) > 255 || isnumber(port) == 0 || strlen(port) > 5) {
      perror("Servername and port too long\n");
      exit(1);
   }
   printf("Servername: %s, port: %s\n", servername, port);
   bzero(buffer, BUFFER_SIZE);
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
	printf("Enter your message to send: ");
	fgets(buffer, BUFFER_SIZE, stdin);
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
