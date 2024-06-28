typedef enum {  
    IS_FRESH = 0, // 新鲜的字符串，应该被自动释放  
    IS_LITERAL = 1, // 字面量字符串，来自 .rodata，不应该被释放  
    IS_FREED = -98761234, // 已经被释放的字符串，防止双重释放  
    IS_CORRUPTED // 其他值表示字符串已损坏  
} StringLiteralType; 
typedef struct {  
    const char *str; // 指向 C 风格以 0 结尾的字节字符串  
    size_t len; // .str 字段的长度，不包括结尾的 0 字节。它总是等于 strlen(str)（如果 str 不是 NULL）  
    StringLiteralType is_lit; // 表示字符串的类型  
} String; 

bool message(const char* str,int num);
String prompt(const char* str);
String file_dialog();
String open_dir(const char* path);
bool save_file(const char* content,const char* path,const char* filename);
