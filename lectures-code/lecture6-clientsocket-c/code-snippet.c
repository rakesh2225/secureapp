//All libraries needed
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <errno.h>
#include <string.h>
#include <netdb.h>
#include <sys/types.h>
#include <netinet/in.h>
#include <sys/socket.h>
#include <arpa/inet.h>

#define BUFFERSIZE 1024 //define the size of the buffer 


//snippet from Slide 10 - Lecture 6
if(strlen(servername) > 253 || strlen(port) > 5){
		printf("Servername or port is too long. Please try again!\n");
		exit(0);
	} 
	
//snippet from Slide 15 - Lecture 6
int sockfd = socket(AF_INET, SOCK_STREAM, 0);
if (sockfd < 0){ 
	perror("ERROR opening socket");
	exit(0);
}


//snippet from Slide 22 - Lecture 6
struct addrinfo hints, *serveraddr;
memset(&hints, 0, sizeof hints);
hints.ai_family = AF_INET;
hints.ai_socktype = SOCK_STREAM;
int addr_lookup = getaddrinfo(servername, port, &hints, &serveraddr);
if (addr_lookup != 0) {
	fprintf(stderr, "getaddrinfo: %s\n", gai_strerror(addr_lookup));
	exit(1);
}
	

//snippet from Slide 25 - Lecture 6
int connected = connect(sockfd, serveraddr->ai_addr, serveraddr->ai_addrlen);
if(connected < 0){
	perror("Cannot connect to the server\n");
	exit(3);
}else
	printf("Connected to the server %s at port %s\n",servername, port);
freeaddrinfo(serveraddr);
	


