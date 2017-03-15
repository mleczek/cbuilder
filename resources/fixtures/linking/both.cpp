#include "codes.h"
#include "console.h"

int main(int argc, char** argv)
{
    console::writeln("Static and dynamic linking works!");
    return codes::success();
}
