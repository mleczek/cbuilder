#include "console.h"
#include <stdio.h>

void console::writeln(const char* str)
{
    printf("%s\n", str);
}
