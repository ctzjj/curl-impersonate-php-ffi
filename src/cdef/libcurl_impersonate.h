int curl_global_init(long flags);
void *curl_easy_init();
int curl_easy_setopt(void *curl, int option, ...);
int curl_easy_perform(void *curl);
void curl_easy_cleanup(void *handle);
int curl_easy_getinfo(void *curl, int option, ...);
void curl_easy_reset(void *curl);
char *curl_version();
int curl_easy_impersonate(void *curl, char *target, int default_headers);
struct curl_slist *curl_slist_append(struct curl_slist *list, char *string);
void curl_slist_free_all(struct curl_slist *list);
struct curl_version_info_data *curl_version_info(int version);
void curl_global_cleanup(void);

void *curl_multi_init();
int curl_multi_cleanup(void *curlm);
int curl_multi_add_handle(void *curlm, void *curl);
int curl_multi_remove_handle(void *curlm, void *curl);
int curl_multi_socket_action(void *curlm, int sockfd, int ev_bitmask, int *running_handle);
int curl_multi_setopt(void *curlm, int option, ...);
int curl_multi_assign(void *curlm, int sockfd, void *sockptr);
struct CURLMsg *curl_multi_info_read(void* curlm, int *msg_in_queue);

typedef struct curl_slist {
  char *data;
  struct curl_slist *next;
} curl_slist;

typedef struct curl_certinfo {
  int num_of_certs;             /* number of certificates with information */
  struct curl_slist **certinfo; /* for each index in this array, there's a
                                   linked list with textual information in the
                                   format "name: value" */
} curl_certinfo;

typedef struct curl_version_info_data {
  int age;          /* age of the returned struct */
  const char *version;      /* LIBCURL_VERSION */
  unsigned int version_num; /* LIBCURL_VERSION_NUM */
  const char *host;         /* OS/host/cpu/machine when configured */
  int features;             /* bitmask, see defines below */
  const char *ssl_version;  /* human readable string */
  long ssl_version_num;     /* not used anymore, always 0 */
  const char *libz_version; /* human readable string */
  /* protocols is terminated by an entry with a NULL protoname */
  const char * const *protocols;

  /* The fields below this were added in CURLVERSION_SECOND */
  const char *ares;
  int ares_num;

  /* This field was added in CURLVERSION_THIRD */
  const char *libidn;

  /* These field were added in CURLVERSION_FOURTH */

  /* Same as '_libiconv_version' if built with HAVE_ICONV */
  int iconv_ver_num;

  const char *libssh_version; /* human readable string */

  /* These fields were added in CURLVERSION_FIFTH */
  unsigned int brotli_ver_num; /* Numeric Brotli version
                                  (MAJOR << 24) | (MINOR << 12) | PATCH */
  const char *brotli_version; /* human readable string. */

  /* These fields were added in CURLVERSION_SIXTH */
  unsigned int nghttp2_ver_num; /* Numeric nghttp2 version
                                   (MAJOR << 16) | (MINOR << 8) | PATCH */
  const char *nghttp2_version; /* human readable string. */
  const char *quic_version;    /* human readable quic (+ HTTP/3) library +
                                  version or NULL */

  /* These fields were added in CURLVERSION_SEVENTH */
  const char *cainfo;          /* the built-in default CURLOPT_CAINFO, might
                                  be NULL */
  const char *capath;          /* the built-in default CURLOPT_CAPATH, might
                                  be NULL */

  /* These fields were added in CURLVERSION_EIGHTH */
  unsigned int zstd_ver_num; /* Numeric Zstd version
                                  (MAJOR << 24) | (MINOR << 12) | PATCH */
  const char *zstd_version; /* human readable string. */

  /* These fields were added in CURLVERSION_NINTH */
  const char *hyper_version; /* human readable string. */

  /* These fields were added in CURLVERSION_TENTH */
  const char *gsasl_version; /* human readable string. */

  /* These fields were added in CURLVERSION_ELEVENTH */
  /* feature_names is terminated by an entry with a NULL feature name */
  const char * const *feature_names;
} curl_version_info_data;


typedef struct CURLMsg {
   int msg;       /* what this message means */
   void *easy_handle; /* the handle it concerns */
   union {
     void *whatever;    /* message-specific data */
     int result;   /* return code for transfer */
   } data;
} CURLMsg;