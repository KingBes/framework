typedef struct
{
  // Major version.
  unsigned int major;
  // Minor version.
  unsigned int minor;
  // Patch version.
  unsigned int patch;
} webview_version_t;

// Holds the library's version information.
typedef struct
{
  // The elements of the version number.
  webview_version_t version;
  // SemVer 2.0.0 version number in MAJOR.MINOR.PATCH format.
  char version_number[32];
  // SemVer 2.0.0 pre-release labels prefixed with "-" if specified, otherwise
  // an empty string.
  char pre_release[48];
  // SemVer 2.0.0 build metadata prefixed with "+", otherwise an empty string.
  char build_metadata[48];
} webview_version_info_t;

typedef void *webview_t;
typedef void *HMENU;

// Creates a new webview instance. If debug is non-zero - developer tools will
// be enabled (if the platform supports them). Window parameter can be a
// pointer to the native window handle. If it's non-null - then child WebView
// is embedded into the given parent window. Otherwise a new window is created.
// Depending on the platform, a GtkWindow, NSWindow or HWND pointer can be
// passed here. Returns null on failure. Creation can fail for various reasons
// such as when required runtime dependencies are missing or when window creation
// fails.
webview_t webview_create(int debug, int winWidth, int winHeight, void *window);

// Destroys a webview and closes the native window.
void webview_destroy(webview_t w);

// Runs the main loop until it's terminated. After this function exits - you
// must destroy the webview.
void webview_run(webview_t w);

// Stops the main loop. It is safe to call this function from another other
// background thread.
void webview_terminate(webview_t w);

// Posts a function to be executed on the main thread. You normally do not need
// to call this function, unless you want to tweak the native window.
void webview_dispatch(webview_t w, void (*fn)(webview_t w, void *arg), void *arg);

// Returns a native window handle pointer. When using GTK backend the pointer
// is GtkWindow pointer, when using Cocoa backend the pointer is NSWindow
// pointer, when using Win32 backend the pointer is HWND pointer.
void *webview_get_window(webview_t w);

// Updates the title of the native window. Must be called from the UI thread.
void webview_set_title(webview_t w, const char *title);

void webview_notify_icon(webview_t w, const char *title);

// Updates native window size. See WEBVIEW_HINT constants.
void webview_set_size(webview_t w, int width, int height,
                      int hints);

// Navigates webview to the given URL. URL may be a properly encoded data URI.
// Examples:
// webview_navigate(w, "https://github.com/webview/webview");
// webview_navigate(w, "data:text/html,%3Ch1%3EHello%3C%2Fh1%3E");
// webview_navigate(w, "data:text/html;base64,PGgxPkhlbGxvPC9oMT4=");
void webview_navigate(webview_t w, const char *url);

// Set webview HTML directly.
// Example: webview_set_html(w, "<h1>Hello</h1>");
void webview_set_html(webview_t w, const char *html);

// Injects JavaScript code at the initialization of the new page. Every time
// the webview will open a the new page - this initialization code will be
// executed. It is guaranteed that code is executed before window.onload.
void webview_init(webview_t w, const char *js);

// Evaluates arbitrary JavaScript code. Evaluation happens asynchronously, also
// the result of the expression is ignored. Use RPC bindings if you want to
// receive notifications about the results of the evaluation.
void webview_eval(webview_t w, const char *js);

// Binds a native C callback so that it will appear under the given name as a
// global JavaScript function. Internally it uses webview_init(). Callback
// receives a request string and a user-provided argument pointer. Request
// string is a JSON array of all the arguments passed to the JavaScript
// function.
void webview_bind(webview_t w, const char *name,
                  void (*fn)(const char *seq, const char *req,
                             void *arg),
                  void *arg);

// Removes a native C callback that was previously set by webview_bind.
void webview_unbind(webview_t w, const char *name);

// Allows to return a value from the native binding. Original request pointer
// must be provided to help internal RPC engine match requests with responses.
// If status is zero - result is expected to be a valid JSON result value.
// If status is not zero - result is an error JSON object.
void webview_return(webview_t w, const char *seq, int status,
                    const char *result);

// Get the library's version information.
// @since 0.10
const webview_version_info_t *webview_version();

void webview_show_win(webview_t w);

void webview_destroy_win(webview_t w);

// 创建图标菜单
HMENU webview_creat_icon_menu(webview_t w);

// 添加图标菜单项
void webview_icon_menu_text(webview_t w, HMENU hp, int num, const char *title);

// 弹出图标菜单
int webview_track_icon_menu(webview_t w, HMENU hp);

// 销毁图标菜单
void webview_destory_icon_menu(webview_t w, HMENU hp);

void webview_icon_menu(webview_t w, void(*fn)());