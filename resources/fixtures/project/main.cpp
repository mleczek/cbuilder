#include <stdio.h>

#ifndef MESSAGE
#   define MESSAGE "Default message"
#endif

int main(int argc, char** argv)
{
    printf("%s", MESSAGE);
    return 0;
}
