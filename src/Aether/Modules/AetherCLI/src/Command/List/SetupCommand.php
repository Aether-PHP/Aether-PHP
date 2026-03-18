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
 *                      The divine lightweight PHP framework
 *                  < 1 Mo • Zero dependencies • Pure PHP 8.3+
 *
 *  Built from scratch. No bloat. POO Embedded.
 *
 *  @author: dawnl3ss (Alex') ©2025 — All rights reserved
 *  Source available • Commercial license required for redistribution
 *  → github.com/dawnl3ss/Aether-PHP
 *
*/
declare(strict_types=1);

namespace Aether\Modules\AetherCLI\src\Command\List;

use Aether\IO\IOFile;
use Aether\IO\IOTypeEnum;
use Aether\Modules\AetherCLI\Cli\CliColorEnum;
use Aether\Modules\AetherCLI\Command\Command;


class SetupCommand extends Command {

    /** @var string $_binPath */
    private static $_binPath = "./bin/aether";

    /**
     * @param string $_line
     *
     * @return bool
     */
    private static function _isSafeLine(string $_line) : bool {
        $line = trim($_line);
        if ($line === "")
            return false;

        # - Block classic shell injection chars
        if (preg_match('/[;&|`$<>\\r\\n]/', $line))
            return false;

        return true;
    }

    public function __construct(array $_extra){
        parent::__construct(
            "setup",
            [ "dev", "prod" ],
            $_extra,
            "./aether setup | ./aether setup:dev|prod"
        );
    }

    public function _execute(?string $_prototype) : bool {
        $ext = "";

        if ($_prototype === "dev" || $_prototype === "prod")
            $ext = '.' . $_prototype;

        if (!file_exists("./Aetherfile" . $ext))
            die(CliColorEnum::FG_RED->_paint("[SetupCommand] - Error - Aetherfile{$ext} not found.") . PHP_EOL);

        $lines = IOFile::_open(IOTypeEnum::OTHER, "./Aetherfile" . $ext)->_readLines();

        foreach ($lines as $line){
            $line = trim($line);

            if ($line === "")
                continue;

            if (str_starts_with($line, "#") || str_starts_with($line, "//"))
                continue;

            if (!self::_isSafeLine($line))
                die(CliColorEnum::FG_RED->_paint("[SetupCommand] - Error - Unsafe command detected in Aetherfile{$ext}.") . PHP_EOL);

            # - We keep it simple: setup file executes only AetherCLI commands (no shell features)
            $cmd = self::$_binPath . " " . $line;

            if (!shell_exec($cmd))
                return false;
        }

        echo CliColorEnum::FG_BRIGHT_GREEN->_paint("[SetupCommand] - Aetherfile{$ext} has been successfuly setup.") . PHP_EOL;
        return true;
    }
}