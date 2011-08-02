#pragma link "./libraries/v8/libv8wrapper.so"
#pragma include "./libraries/v8"

#include <stdlib.h>
#include <stdio.h>

#include <gwan.h>

// ============================================================================
// main
// ----------------------------------------------------------------------------
int main(int argc, char **argv)
{
xbuf_t *reply = get_reply(argv);

// build the top of our HTML page
char b[256];
sprintf(b,"%s",runv8("'Hello'+' world!'"));
xbuf_cat(reply, b);


return 200;
}
// ============================================================================
// End of Source Code
// ============================================================================