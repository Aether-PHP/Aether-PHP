<?php

/*
 *
 *      █████╗ ███████╗████████╗██╗  ██╗███████╗██████╗         ██████╗ ██╗  ██╗██████╗
 *     ██╔══██╗██╔════╝╚══██╔══╝██║  ██║██╔════╝██╔══██╗        ██╔══██╗██║  ██║██╔══██╗
 *     ███████║█████╗     ██║   ███████║█████╗  ██████╔╝ █████╗ ██████╔╝███████║██████╔╝
 *     ██╔══██║██╔══╝     ██║   ██╔══██║██╔══╝  ██╔══██╗ ╚════╝ ██╔═══╝ ██╔══██║██╔═══╝
 *     ██║  ██║███████╗   ██║   ██║  ██║███████╗██║  ██║        ██║     ██║  ██║██║
 *     ╚═╝  ╚═╝╚══════╝   ╚═╝   ╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝        ╚═╝     ╚═╝  ╚═╝╚═╝
 *
 *                  The divine secure, lightweight PHP framework
 *                   < 1 Mo • Zero dependencies • Pure PHP 8.3+
 *
 *  Built from scratch. No bloat. OOP Embedded.
 *
 *  @author: dawnl3ss (Alex') ©2026 — All rights reserved
 *  Source available • Commercial license required for redistribution
 *  → https://aetherphp.net
 *  → https://github.com/Aether-PHP/Aether-PHP
 *
*/
declare(strict_types=1);

namespace Aether\Modules\AetherCLI\Command\List;

use Aether\Modules\AetherCLI\Cli\CliColorEnum;
use Aether\Modules\AetherCLI\Command\Command;
use Aether\Modules\AetherCLI\Script\BaseScript;


class SourceCommand extends Command {

    /**
     * @param string $_path
     *
     * @return bool
     */
    private static function _isScriptPathAllowed(string $_path) : bool {
        $scriptsRoot = realpath("./bin/scripts");
        $scriptPath = realpath($_path);

        if ($scriptsRoot === false || $scriptPath === false)
            return false;

        $scriptsRoot = rtrim(str_replace("\\", "/", $scriptsRoot), "/") . "/";
        $scriptPath = str_replace("\\", "/", $scriptPath);

        return str_starts_with($scriptPath, $scriptsRoot);
    }

    public function __construct(array $_extra){
        parent::__construct(
            "source",
            [ "script", "db" ],
            $_extra,
            "./aether source:[script|db] {name}"
        );
    }

    public function _execute(?string $_prototype) : bool {
        if (is_null($_prototype))
            die(CliColorEnum::FG_RED->_paint("[SourceCommand] - Error - Missing prototype (script|db).") . PHP_EOL);

        if (count($this->_getExtra()) < 1)
            die(CliColorEnum::FG_RED->_paint("[SourceCommand] - Error - Missing source file.") . PHP_EOL);

        if ($_prototype === "script"){
            $script = $this->_getExtra()[0];

            if (!file_exists($script))
                die(CliColorEnum::FG_RED->_paint("[SourceCommand] - Error - Source file '{$script}' does not exists.") . PHP_EOL);

            if (strtolower(pathinfo($script, PATHINFO_EXTENSION)) !== 'php')
                die(CliColorEnum::FG_RED->_paint("[SourceCommand] - Error - Source file '{$script}' needs to be a php file.") . PHP_EOL);

            if (!self::_isScriptPathAllowed($script))
                die(CliColorEnum::FG_RED->_paint("[SourceCommand] - Error - Provided script must be located in ./bin/scripts/.") . PHP_EOL);

            require_once realpath($script);

            $fullClass = str_replace('/', "\\", preg_replace('/\.[^.]+$/', '', str_replace("\\", "/", $script)));

            if (!class_exists($fullClass))
                die(CliColorEnum::FG_RED->_paint("[SourceCommand] - Error - Script class '{$fullClass}' not found. Check namespace/class name.") . PHP_EOL);

            if (!is_subclass_of($fullClass, BaseScript::class))
                die(CliColorEnum::FG_RED->_paint("[SourceCommand] - Error - Provided script must be an instance of AetherCLI/Script/BaseScript.") . PHP_EOL);

            $script = new $fullClass();

            if (!$script instanceof BaseScript)
                die(CliColorEnum::FG_RED->_paint("[SourceCommand] - Error - Provided script must be an instance of AetherCLI/Script/BaseScript."));

            $script->_onLoad();

            if (!$script->_onRun())
                $this->_error("[SourceCommand] - Error - _onRun() function contains error. You may fix it before running.");

            echo CliColorEnum::FG_BRIGHT_GREEN->_paint("[SourceCommand] - Successfully imported source file '{$fullClass}'.") . PHP_EOL;
        } else if ($_prototype === "db"){
            echo CliColorEnum::FG_BLUE->_paint("[SourceCommand] - Not implemented yet.") . PHP_EOL;
        }

        return true;
    }
}