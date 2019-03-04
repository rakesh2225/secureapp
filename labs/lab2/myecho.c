#include <stdio.h>
#include <stdlib.h>
#include <string.h>

int main(int argc, char * argv[]) {
    char buffer[126];
 	printf("%s", argv[1]); 
	strncpy(buffer,argv[1], 126);
    printf("%s\n", buffer);
}

