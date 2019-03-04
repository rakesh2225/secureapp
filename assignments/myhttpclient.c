/* include libraries */
#include <stdio.h>
#include <stdlib.h>
#include <ctype.h>
#include <string.h>
#include <netdb.h>
#include <stdbool.h>
#include <sys/types.h>
#include <sys/socket.h>

#define BUFFER_SIZE 2048

int isPort(char port[]) {
   int i = 0;
   for (;i < strlen(port); i++) {
      if (!isdigit(port[i])) {
         return 0;
      }
   }
	if (strlen(port) == 5) {
		if (port[0] > '6' || port[1] > '5' || port[2] > '5' || port[3] > '3' || port[4] > '5') {
			return 0;
		}
	}
	return 1;
}

int main (int argc, char *argv[])
{
   	printf("TCP client by Rakesh - SecAd Spring 2019\n");
   	if (argc != 2) {
      	printf("Usage: %s <URL>\n",argv[0]);
    	exit(0);
   	}
   	char *url = argv[1];
   	if (strlen(url) > 3000) {
    	perror("URL is too long, there might be a suspicious activity. Try again with a different URL.\n");
    	exit(1);
   	}
	char buffer[BUFFER_SIZE];
    char servername[100];
	char path[3000];
	char *filename;
	char *urlPattern = "http://%[^/]/%s/%s";
	sscanf(url, urlPattern, servername, path);
	filename = strrchr(url, '/');
	filename = filename + 1;
	char *fileExists = strrchr(filename, '.');
	if (!fileExists) {
		filename = "index.html";
	}

	printf("Servername: %s %s %s\n", servername, path, filename);
   	if (strlen(servername) > 255 || strlen(path) > 2500) {
    	perror("Invalid Hostname, url or path, please try again with valid input\n");
    	exit(1);
   	}
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
   	int addr_lookup = getaddrinfo(servername, "80", &hints, &serveraddr);
   	if (addr_lookup != 0) {
    	fprintf(stderr, "getaddrinfo %s\n: ", gai_strerror(addr_lookup));
      	exit(1);
   	}

   	int connected = connect(socfd, serveraddr->ai_addr, serveraddr->ai_addrlen);
   	if (connected < 0) { 
   		perror("Can not connect to server\n");
      	exit(3);
   	} else {
    	printf("Connected to server %s\n", servername);
   	}
	freeaddrinfo(serveraddr);

	bzero(buffer, BUFFER_SIZE);
	sprintf(buffer, "GET /%s HTTP/1.1\r\nHost: %s\r\n\r\n", path, servername);
    int bytes_sent = send(socfd, buffer, strlen(buffer), 0);
	char output[BUFFER_SIZE];
	bzero(output, BUFFER_SIZE);
	int bytes_received = recv(socfd, output, BUFFER_SIZE, 0);
	if (bytes_received < 0) {
		perror("Error in reading");
		exit(4);
	}
	
	int responseCode;
	sscanf(output, "HTTP/1.%*[01] %d", &responseCode);
	
	if (responseCode != 200) {
		printf("Error code: %d", responseCode);
		perror("Error occurred while communicating with server. Verify your input and try again.");
		exit(5);
	}
	printf("%d OK, receiving file: %s\n", responseCode, filename);
	char *data;
	data = strstr(output, "\r\n\r\n");
	data = data + 4;

	FILE *fp;
	fp = fopen(filename, "w");
	if(fp == NULL)
   	{
    	printf("Error creating and writing into the file");
    	exit(1);
   	}
   	int received_count = 0;
   	printf("Received times> : %d\n", ++received_count);
   	fwrite(output, 1, (bytes_received - (data - output)), fp);
   	printf("Written\n");
	while((bytes_received = recv(socfd, output, BUFFER_SIZE, 0)) > 0) {
		printf("DEBUG> Received times : %d\n", ++received_count);
		fwrite(output, 1, bytes_received, fp);
	}
	fclose(fp);
	printf("Finished writing to the file...\n");
    
    close(socfd);
	return 0;
}
