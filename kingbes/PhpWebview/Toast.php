<?php

declare(strict_types=1);

namespace app\PhpWebview;

use FFI\WorkDirectory\WorkDirectory;

/**
 * 吐司 class
 */
class Toast
{
    private \FFI $co;

    private $Instance;

    private $Template;

    private $error;

    public function __construct()
    {
        WorkDirectory::set(dirname(__DIR__)  . DIRECTORY_SEPARATOR . "dll");
        $HeaderCo = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "toast_php.h");
        $CoDll = dirname(__DIR__) . DIRECTORY_SEPARATOR . "dll" . DIRECTORY_SEPARATOR . "co.dll";
        $this->co = \FFI::cdef($HeaderCo, $CoDll);
    }

    /**
     * 获取ffi function
     *
     * @return \FFI
     */
    public function getFFi(): \FFI
    {
        return $this->co;
    }

    /**
     * 是否兼容 function
     *
     * @return int
     */
    public function isCompatible(): int
    {
        return $this->co->isCompatible();
    }

    /**
     * 创建接口实例 function
     *
     * @return self
     */
    public function Instance_Create(): self
    {
        $this->co->InitializeEx();
        $this->Instance = $this->co->Instance_Create();
        return $this;
    }

    /**
     * 设置app名称 function
     *
     * @param string $str
     * @return self
     */
    public function setAppName(string $str): self
    {
        $this->co->setAppName($this->Instance, $str);
        return $this;
    }

    /**
     * 设置app用户模块id function
     *
     * @param string $modeId 模块id
     * @return self
     */
    public function setAppUserModelId(string $modeId = "Microsoft.Windows.Explorer"): self
    {
        $this->co->setAppUserModelId($this->Instance, $modeId);
        return $this;
    }

    /**
     * 设置快捷方式的政策 function
     *
     * @param integer $num 0=>忽略快捷策略,1=>无需创建快捷策略,2=>快捷策略需要创建
     * @return self
     */
    public function setShortcutPolicy(int $num = 0): self
    {
        if ($num > 2 || $num < 0) {
            $num = 0;
        }
        $this->co->setShortcutPolicy($this->Instance, $num);
        return $this;
    }

    /**
     * 创建快捷方式 function
     *
     * @return self
     */
    public function createShortcut(): self
    {
        $shortcut = $this->co->createShortcut($this->Instance);
        if (!$shortcut) {
            throw new \Exception("create Shortcut failed");
        }
        return $this;
    }

    /**
     * 初始化 function
     *
     * @return self
     */
    public function initialize(): self
    {
        $initialize = $this->co->initialize($this->Instance, $this->error);
        if (!$initialize) {
            throw new \Exception("initialization failure");
        }
        return $this;
    }

    /**
     * 创建模板 function
     *
     * @param integer $type 
     * 0=>TemplateType_ImageAndText01
     * 
     * 1=>TemplateType_ImageAndText02
     * 
     * 2=>TemplateType_ImageAndText03
     * 
     * 3=>TemplateType_ImageAndText04
     * 
     * 4=>TemplateType_Text01
     * 
     * 5=>TemplateType_Text02
     * 
     * 6=>TemplateType_Text03
     * 
     * 7=>TemplateType_Text04
     * @return self
     */
    public function Template_Create(int $type): self
    {
        if ($type < 0 || $type > 7) {
            throw new \Exception("The `type` field must range from 0 to 7");
        }
        $this->Template = $this->co->Template_Create($type);
        return $this;
    }

    /**
     * 模板设置第一行内容 function
     *
     * @param string $str
     * @return self
     */
    public function Template_setFirstLine(string $str): self
    {
        $this->co->Template_setFirstLine($this->Template, $str);
        return $this;
    }

    /**
     * 显示吐司 function
     *
     * @return mixed
     */
    public function showToast(): mixed
    {
        $show = $this->co->showToast($this->Instance, $this->Template, null, null);
        var_dump($show); //不加这个无法显示，太神奇了
        return $show;
    }

    /**
     * 模板设置第二行内容 function
     *
     * @param string $str
     * @return self
     */
    public function Template_setSecondLine(string $str): self
    {
        $this->co->Template_setSecondLine($this->Template, $str);
        return $this;
    }

    /**
     * 模板设置第三行内容 function
     *
     * @param string $str
     * @return self
     */
    public function Template_setThirdLine(string $str): self
    {
        $this->co->Template_setThirdLine($this->Template, $str);
        return $this;
    }

    /**
     * 模板设置文本字段 function
     *
     * @param string $str
     * @param integer $pos
     * 0=>第一行
     * 
     * 1=>第二行
     * 
     * 2=>第三行
     * @return self
     */
    public function Template_setTextField(string $str, int $pos): self
    {
        if ($pos < 0 || $pos > 2) {
            throw new \Exception("The `pos` field must range from 0 to 2");
        }
        $this->co->Template_setTextField($this->Template, $str, $pos);
        return $this;
    }

    /**
     * 模板设置属性文本 function
     *
     * @param string $str
     * @return self
     */
    public function Template_setAttributionText(string $str): self
    {
        $this->co->Template_setAttributionText($this->Template, $str);
        return $this;
    }

    /**
     * 模板设置图片路径 function
     *
     * @param string $path 图像路径
     * @return self
     */
    public function Template_setImagePath(string $path): self
    {
        $this->co->Template_setImagePath($this->Template, $path);
        return $this;
    }

    /**
     * 模板设置图像路径与裁剪提示 function
     *
     * @param string $path 图像路径
     * @param integer $CropHint
     * 0=>裁剪提示方块
     * 
     * 1=>裁剪提示圈
     * @return self
     */
    public function Template_setImagePathWithCropHint(string $path, int $CropHint): self
    {
        if ($CropHint < 0 || $CropHint > 1) {
            throw new \Exception("The `CropHint` field must range from 0 to 1");
        }
        $this->co->Template_setImagePathWithCropHint($this->Template, $path, $CropHint);
        return $this;
    }

    /**
     * 模板设置英雄图像路径 function
     *
     * @param string $path 图像路径
     * @param boolean $inlineImage 位置
     * @return self
     */
    public function Template_setHeroImagePath(string $path, bool $inlineImage): self
    {
        $this->co->Template_setHeroImagePath($this->Template, $path, $inlineImage);
        return $this;
    }

    /**
     * 模板设置音频系统文件 function
     *
     * @param integer $audio 0~25
     * @return self
     */
    public function Template_setAudioSystemFile(int $audio): self
    {
        if ($audio < 0 || $audio > 25) {
            throw new \Exception("The `audio` field must range from 0 to 25");
        }
        $this->co->Template_setAudioSystemFile($this->Template, $audio);
        return $this;
    }

    /**
     * 模板设置音频路径 function
     *
     * @param string $audioPath 音频路径
     * @return self
     */
    public function Template_setAudioPath(string $audioPath): self
    {
        $this->co->Template_setAudioPath($this->Template, $audioPath);
        return $this;
    }

    /**
     * 模板设置音频选项 function
     *
     * @param integer $audioOption 
     * 0=>TextField_FirstLine
     * 
     * 1=>TextField_SecondLine
     * 
     * 2=>TextField_ThirdLine
     * @return self
     */
    public function Template_setAudioOption(int $audioOption): self
    {
        if ($audioOption < 0 || $audioOption > 2) {
            throw new \Exception("The `audioOption` field must range from 0 to 2");
        }
        $this->co->Template_setAudioOption($this->Template, $audioOption);
        return $this;
    }

    /**
     * 设置模板时间 function
     *
     * @param integer $duration 0~2
     * 0=>Duration_System
     * 
     * 1=>Duration_Short
     * 
     * 2=>Duration_Long
     * @return self
     */
    public function Template_setDuration(int $duration): self
    {
        if ($duration < 0 || $duration > 2) {
            throw new \Exception("The `duration` field must range from 0 to 2");
        }
        $this->co->Template_setDuration($this->Template, $duration);
        return $this;
    }

    /**
     * 模板设置过期 function
     *
     * @param integer $millisecondsFromNow
     * @return self
     */
    public function Template_setExpiration(int $millisecondsFromNow): self
    {
        $this->co->Template_setExpiration($this->Template, $millisecondsFromNow);
        return $this;
    }

    /**
     * 模板设置场景 function
     *
     * @param integer $scenario 0~3
     * 0=>Scenario_Default
     * 
     * 1=>Scenario_Alarm
     * 
     * 2=>Scenario_IncomingCall
     * 
     * 3=>Scenario_Reminder
     * @return self
     */
    public function Template_setScenario(int $scenario): self
    {
        if ($scenario < 0 || $scenario > 3) {
            throw new \Exception("The `scenario` field must range from 0 to 3");
        }
        $this->co->Template_setScenario($this->Template, $scenario);
        return $this;
    }

    /**
     * 添加操作 function
     *
     * @param string $label
     * @return self
     */
    public function Template_addAction(string $label): self
    {
        $this->co->Template_addAction($this->Template, $label);
        return $this;
    }


    /**
     * 模板是否过期 function
     *
     * @return integer
     */
    public function Template_expiration(): int
    {
        return $this->co->Template_expiration($this->Template);
    }

    /**
     * 消除 function
     *
     * @return void
     */
    public function DestroyToast(): void
    {
        $this->co->Template_Destroy($this->Template);
        $this->co->Instance_Destroy($this->Instance);
        $this->co->Uninitialize();
    }
}
