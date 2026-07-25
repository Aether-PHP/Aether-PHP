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


class DatabaseCommand extends Command {

    public function __construct(array $_extra){
        parent::__construct(
            "database",
            [ "shell", "import", "export" ],
            $_extra,
            "./aether database:[shell|import|export] {name}"
        );
    }

    public function _execute(?string $_prototype) : bool {
        if (is_null($_prototype))
            die(CliColorEnum::FG_RED->_paint("[DatabaseCommand] - Error - Missing argument(s).") . PHP_EOL);

        # - shell prototype
        if ($_prototype === "shell"){
            if ($this->_getExtra() !== [])
                die(CliColorEnum::FG_RED->_paint("[DatabaseCommand:Shell] - Error - Extra arguments are not required.") . PHP_EOL);

            $state = true;

            $databases = array_map(function($db){
                return $db["Database"];
            }, Aether()->_db()->_mysql("mysql")->_raw("SHOW DATABASES"));
            echo "Databases : " . implode(", ", $databases) . PHP_EOL . PHP_EOL;

            $tables = [];
            foreach ($databases as $dbname){
                $tables[$dbname] = array_map(function($table) use ($dbname){
                    return $table["Tables_in_" . $dbname];
                }, Aether()->_db()->_mysql($dbname)->_raw("SHOW TABLES"));
            }
            var_dump($tables);



            #todo: inprogress
            /*while ($state){
                echo "Enter : ";
                $input = fgets(fopen("php://stdin","r"));
                echo shell_exec($input);
            }*/
            return true;
        } else if ($_prototype === "import"){

            return true;
        }  else if ($_prototype === "export"){

            return true;
        }
        return false;
    }
}