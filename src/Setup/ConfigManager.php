<?php

namespace Dupot\StaticManagementFramework\Setup;

use Exception;

class ConfigManager
{

    protected $configList = [];

    public function loadConfigFromIni(string $iniFilename): void
    {
        $configListInFile = parse_ini_file($iniFilename, true);
        foreach ($configListInFile as $sectionLoop => $configSectionListLoop) {
            $this->configList[$sectionLoop] = $configSectionListLoop;
        }
    }

    public function setSectionParam(string $section, string $param, $value)
    {
        $this->configList[$section][$param] = $value;
    }

    public function getSectionParam(string $section, string $param)
    {
        if (!isset($this->configList[$section][$param])) {
            throw new Exception('Cannot find param ' . $param . ' in section ' . $section);
        }

        return $this->configList[$section][$param];
    }
}
