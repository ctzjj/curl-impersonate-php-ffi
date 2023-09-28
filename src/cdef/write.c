#include <stdlib.h>
#include <string.h>
#include "write.h"

size_t write_callback(void *contents, size_t size, size_t nmemb, void *userp) {
	size_t total_size = size * nmemb;
	memory_data *md = (memory_data*) userp;
	md->data = realloc(md->data, md->size + total_size + 1);
	if (md->data == NULL) {
		/* out of memory! */
		return 0;
	}
	memcpy(&(md->data[md->size]), contents, total_size);
	md->size += total_size;
	md->data[md->size] = 0;
	return total_size;
}	

void * init() {
	return &write_callback;
}
