/* include libraries */
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <ctype.h>
#include <string.h>
#include <netdb.h>
#include <error.h>
#include <stdbool.h>
#include <sys/types.h>
#include <netinet/in.h>
#include <sys/socket.h>

#include <arpa/inet.h>

#define BUFFER_SIZE 1024

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
    char servername[100];
	char path[3000];
	
	char *urlPattern = "http://%[^/]/%s/%s";
	//Extract servername and path from the URL.
	sscanf(url, urlPattern, servername, path);

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
	printf("Connecting to server...\n");
	
	//Connect to the server using socket.
   	int connected = connect(socfd, serveraddr->ai_addr, serveraddr->ai_addrlen);
   	if (connected < 0) { 
   		perror("Can not connect to server\n");
      	exit(3);
   	} else {
    	printf("Connected to server %s\n", servername);
   	}
	freeaddrinfo(serveraddr);

	//Prepare the http request to send server.
	char buffer[BUFFER_SIZE];
	bzero(buffer, BUFFER_SIZE);
	sprintf(buffer, "GET /%s HTTP/1.0\r\nHost: %s\r\n\r\n", path, servername);
    int bytes_sent = send(socfd, buffer, strlen(buffer), 0);
	printf("Sent data to the server...\n");
	bzero(buffer, BUFFER_SIZE);

	//Receive the response from the server.
	int bytes_received = recv(socfd, buffer, BUFFER_SIZE, 0);
	if (bytes_received < 0) {
		perror("Error in reading");
		exit(4);
	}
	
	int responseCode;
	sscanf(buffer, "HTTP/1.%*[01] %d", &responseCode);
	printf("Response status: %d\n", responseCode);
	if (responseCode != 200) {
		printf("Error code: %d\n", responseCode);
		perror("Error occurred while communicating with server. Verify your input and try again.");
		exit(5);
	}
	char *filename;
	filename = strrchr(url, '/');
	filename = filename + 1;
	char *fileExists = strrchr(filename, '.');
	if (!fileExists) {
		filename = "index.html";
	}
	printf("%d OK, receiving file: %s\n", responseCode, filename);

	//split and get last part of the response data and place the pointer to the start address of the data.
	char *data = strstr(buffer, "\r\n\r\n");
	data = data + 4;

	FILE *fp;
	fp = fopen(filename, "w");
	if(fp == NULL)
   	{
    	printf("Error creating and writing into the file");
    	exit(1);
   	}

   	//Write data received in the initial packet of data.
   	fwrite(data, 1, bytes_received - (data - buffer), fp);
   	bzero(buffer, BUFFER_SIZE);
   	//Write the rest of the data in to the file.
	while((bytes_received = recv(socfd, buffer, BUFFER_SIZE, 0)) > 0) {
		fwrite(buffer,1, bytes_received,fp);
	}
	fclose(fp);
	printf("Finished writing to the file...\n");
    
    close(socfd);
	return 0;
}
