typedef struct _WTLC_Instance WTLC_Instance;
typedef struct _WTLC_Template WTLC_Template;

enum _WTLC_ShortcutPolicy
{
    WTLC_SHORTCUT_POLICY_IGNORE = 0,
    WTLC_SHORTCUT_POLICY_REQUIRE_NO_CREATE = 1,
    WTLC_SHORTCUT_POLICY_REQUIRE_CREATE = 2
};
typedef enum _WTLC_ShortcutPolicy WTLC_ShortcutPolicy;

enum _WTLC_Error
{
    WTLC_Error_NoError = 0,
    WTLC_Error_NotInitialized,
    WTLC_Error_SystemNotSupported,
    WTLC_Error_ShellLinkNotCreated,
    WTLC_Error_InvalidAppUserModelID,
    WTLC_Error_InvalidParameters,
    WTLC_Error_InvalidHandler,
    WTLC_Error_NotDisplayed,
    WTLC_Error_UnknownError
};
typedef enum _WTLC_Error WTLC_Error;

enum _WTLC_TemplateType
{
    WTLC_TemplateType_ImageAndText01 = 0,
    WTLC_TemplateType_ImageAndText02 = 1,
    WTLC_TemplateType_ImageAndText03 = 2,
    WTLC_TemplateType_ImageAndText04 = 3,
    WTLC_TemplateType_Text01 = 4,
    WTLC_TemplateType_Text02 = 5,
    WTLC_TemplateType_Text03 = 6,
    WTLC_TemplateType_Text04 = 7
};
typedef enum _WTLC_TemplateType WTLC_TemplateType;

typedef void (*WTLC_CB_toastActivated)(void *userData);

enum _WTLC_TextField
{
    WTLC_TextField_FirstLine = 0,
    WTLC_TextField_SecondLine,
    WTLC_TextField_ThirdLine
};
typedef enum _WTLC_TextField WTLC_TextField;

enum _WTLC_CropHint
{
    WTLC_CropHint_Square,
    WTLC_CropHint_Circle
};
typedef enum _WTLC_CropHint WTLC_CropHint;

enum _WTLC_AudioSystemFile
{
    WTLC_AudioSystemFile_DefaultSound,
    WTLC_AudioSystemFile_IM,
    WTLC_AudioSystemFile_Mail,
    WTLC_AudioSystemFile_Reminder,
    WTLC_AudioSystemFile_SMS,
    WTLC_AudioSystemFile_Alarm,
    WTLC_AudioSystemFile_Alarm2,
    WTLC_AudioSystemFile_Alarm3,
    WTLC_AudioSystemFile_Alarm4,
    WTLC_AudioSystemFile_Alarm5,
    WTLC_AudioSystemFile_Alarm6,
    WTLC_AudioSystemFile_Alarm7,
    WTLC_AudioSystemFile_Alarm8,
    WTLC_AudioSystemFile_Alarm9,
    WTLC_AudioSystemFile_Alarm10,
    WTLC_AudioSystemFile_Call,
    WTLC_AudioSystemFile_Call1,
    WTLC_AudioSystemFile_Call2,
    WTLC_AudioSystemFile_Call3,
    WTLC_AudioSystemFile_Call4,
    WTLC_AudioSystemFile_Call5,
    WTLC_AudioSystemFile_Call6,
    WTLC_AudioSystemFile_Call7,
    WTLC_AudioSystemFile_Call8,
    WTLC_AudioSystemFile_Call9,
    WTLC_AudioSystemFile_Call10
};
typedef enum _WTLC_AudioSystemFile WTLC_AudioSystemFile;

enum _WTLC_AudioOption
{
    WTLC_AudioOption_Default = 0,
    WTLC_AudioOption_Silent,
    WTLC_AudioOption_Loop
};
typedef enum _WTLC_AudioOption WTLC_AudioOption;

enum _WTLC_Duration
{
    WTLC_Duration_System,
    WTLC_Duration_Short,
    WTLC_Duration_Long
};
typedef enum _WTLC_Duration WTLC_Duration;

enum _WTLC_Scenario
{
    WTLC_Scenario_Default,
    WTLC_Scenario_Alarm,
    WTLC_Scenario_IncomingCall,
    WTLC_Scenario_Reminder
};
typedef enum _WTLC_Scenario WTLC_Scenario;

enum _WTLC_ShortcutResult
{
    WTLC_SHORTCUT_UNCHANGED = 0,
    WTLC_SHORTCUT_WAS_CHANGED = 1,
    WTLC_SHORTCUT_WAS_CREATED = 2,
    WTLC_SHORTCUT_MISSING_PARAMETERS = -1,
    WTLC_SHORTCUT_INCOMPATIBLE_OS = -2,
    WTLC_SHORTCUT_COM_INIT_FAILURE = -3,
    WTLC_SHORTCUT_CREATE_FAILED = -4
};
typedef enum _WTLC_ShortcutResult WTLC_ShortcutResult;

int isCompatible();
void InitializeEx();
void Uninitialize();
WTLC_Instance *Instance_Create();
void setAppName(WTLC_Instance *instance, const char *appname);
void setAppUserModelId(WTLC_Instance *instance, const char *aumi);
void setShortcutPolicy(WTLC_Instance *instance, WTLC_ShortcutPolicy policy);
int initialize(WTLC_Instance *instance, WTLC_Error *error);
WTLC_Template *Template_Create(WTLC_TemplateType type);
void Template_setFirstLine(WTLC_Template *toast, const char *text);
int *showToast(WTLC_Instance *instance,
               WTLC_Template *toast,
               void *userData,
               WTLC_CB_toastActivated toastActivated);

void Template_setSecondLine(WTLC_Template *toast, const char *text);
void Template_setThirdLine(WTLC_Template *toast, const char *text);
void Template_setTextField(WTLC_Template *toast, const char *text, WTLC_TextField pos);
void Template_setAttributionText(WTLC_Template *toast, const char *attributionText);
void Template_setImagePath(WTLC_Template *toast, const char *imgPath);
void Template_setImagePathWithCropHint(WTLC_Template *toast, const char *imgPath, WTLC_CropHint cropHint);
void Template_setHeroImagePath(WTLC_Template *toast, const char *imgPath, bool inlineImage);
void Template_setAudioSystemFile(WTLC_Template *toast, WTLC_AudioSystemFile audio);
void Template_setAudioPath(WTLC_Template *toast, const char *audioPath);
void Template_setAudioOption(WTLC_Template *toast, WTLC_AudioOption audioOption);
void Template_setDuration(WTLC_Template *toast, WTLC_Duration duration);
void Template_setExpiration(WTLC_Template *toast, int millisecondsFromNow);
void Template_setScenario(WTLC_Template *toast, WTLC_Scenario scenario);
void Template_addAction(WTLC_Template *toast, const char *label);

size_t Template_textFieldsCount(WTLC_Template *toast);
size_t Template_actionsCount(WTLC_Template *toast);
int Template_hasImage(WTLC_Template *toast);
int Template_hasHeroImage(WTLC_Template *toast);
const char *Template_textField(WTLC_Template *toast, WTLC_TextField pos);
const char *Template_actionLabel(WTLC_Template *toast, size_t pos);
const char *Template_imagePath(WTLC_Template *toast);
const char *Template_heroImagePath(WTLC_Template *toast);
const char *Template_audioPath(WTLC_Template *toast);
const char *Template_attributionText(WTLC_Template *toast);
const char *Template_scenario(WTLC_Template *toast);
int Template_expiration(WTLC_Template *toast);
WTLC_TemplateType Template_type(WTLC_Template *toast);
WTLC_AudioOption Template_audioOption(WTLC_Template *toast);
WTLC_Duration Template_duration(WTLC_Template *toast);
int Template_isToastGeneric(WTLC_Template *toast);
int Template_isInlineHeroImage(WTLC_Template *toast);
int Template_isCropHintCircle(WTLC_Template *toast);
void Template_Destroy(WTLC_Template *toast);

void Instance_Destroy(WTLC_Instance *instance);
int isSupportingModernFeatures();
int isWin10AnniversaryOrHigher();
int isInitialized(WTLC_Instance *instance);
void clear(WTLC_Instance *instance);
WTLC_ShortcutResult createShortcut(WTLC_Instance *instance);
const char *appName(WTLC_Instance *instance);
const char *appUserModelId(WTLC_Instance *instance);