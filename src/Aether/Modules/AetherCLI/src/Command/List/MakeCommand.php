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
namespace Aether\Modules\AetherCLI\Command\List;

use Aether\IO\IOFile;
use Aether\IO\IOTypeEnum;
use Aether\Modules\AetherCLI\Cli\CliColorEnum;
use Aether\Modules\AetherCLI\Command\Command;

class MakeCommand extends Command {

    public function __construct(array $_extra){
        parent::__construct(
            "make",
            [ "controller", "file", "folder" ],
            $_extra,
            "./aether make:[controller|file|folder] {name}"
        );
    }

    public function _execute(?string $_prototype) : bool {
        if (is_null($_prototype))
            die(CliColorEnum::FG_RED->_paint("[MakeCommand] - Error - Missing argument(s).") . PHP_EOL);

        # - Controller prototype
        if ($_prototype === "controller"){
            if ($this->_getExtra() === [])
                die(CliColorEnum::FG_RED->_paint("[MakeCommand:Controller] - Error - Missing controller name.") . PHP_EOL);

            $path = $this->_getExtra()[0];
            $name = explode("/", $path)[count(explode("/", $path))-1];

            $baseControllerContent = <<<'PHP'
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
             *  Built from scratch. No bloat. OOP Embedded.
             *
             *  @author: dawnl3ss (Alex') ©2026 — All rights reserved
             *  Source available • Commercial license required for redistribution
             *  → github.com/dawnl3ss/Aether-PHP
             *
            */
            declare(strict_types=1);
            
            namespace App\Controller;
            
            use Aether\Router\Controller\Controller;
            
            
            class {{CLASSNAME}} extends Controller {
            
                /**
                 * [@method] => GET
                 * [@route] => /{{ROUTE}}
                 */
                public function {{FUNCNAME}}(){
                    echo "{{CLASSNAME}} Route created.";
                }
            
            }
            PHP;

            $baseControllerContent = str_replace("{{CLASSNAME}}", $name . "Controller", $baseControllerContent);
            $baseControllerContent = str_replace("{{FUNCNAME}}", strtolower($name), $baseControllerContent);
            $baseControllerContent = str_replace("{{ROUTE}}", strtolower($path), $baseControllerContent);


            IOFile::_open(
                IOTypeEnum::PHP,
                __DIR__ . "/../../../../../../../app/App/Controller/{$path}Controller.php"
            )->_write($baseControllerContent);

            echo CliColorEnum::FG_BRIGHT_GREEN->_paint("[MakeCommand:Controller] - Successfully created controller '{$name}'." . PHP_EOL);
            return true;
        } else if ($_prototype === "file"){
            if ($this->_getExtra() === [])
                $this->_error("[MakeCommand:File] - Error - Missing file name.");

            $path = $this->_getExtra()[0];

            if (file_exists($path))
                $this->_error("[MakeCommand:File] - Error - File already exists.");

            if (is_null(Aether()->_io()->_file($path, IOTypeEnum::OTHER)->_write('')))
                $this->_error("[MakeCommand:File] - Error - Could not create the file. The path is probably wrong or folders are missing.");

            echo CliColorEnum::FG_BRIGHT_GREEN->_paint("[MakeCommand:File] - Successfully created file '{$path}'." . PHP_EOL);
            return true;

        }  else if ($_prototype === "folder"){
            if ($this->_getExtra() === [])
                $this->_error("[MakeCommand:Folder] - Error - Missing folder name.");

            $path = $this->_getExtra()[0];

            $folder = Aether()->_io()->_folder($path);

            if ($folder->_exist())
                $this->_error("[MakeCommand:Folder] - Error - Folder already exists.");

            $folder->_create();
            echo CliColorEnum::FG_BRIGHT_GREEN->_paint("[MakeCommand:Folder] - Successfully created folder '{$path}'." . PHP_EOL);
            return true;
        }
        return false;
    }
}