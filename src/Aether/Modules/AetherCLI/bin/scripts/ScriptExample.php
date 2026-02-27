<?php

namespace bin\scripts;

use Aether\Modules\AetherCLI\Cli\CliColorEnum;
use Aether\Modules\AetherCLI\Script\BaseScript;


class ScriptExample extends BaseScript {

    public function __construct(){
        parent::__construct("Script Example", "Demonstration of how a script should be implemented in Aether CLI.");
    }

    public function _onLoad() : void {
        $this->_getLogger()->_echo("Loading script example...", CliColorEnum::FG_CYAN);
    }

    /**
     * @return bool
     */
    public function _onRun() : bool {
        $this->_getLogger()->_echo("Executing script example", CliColorEnum::FG_CYAN);
        return true;
    }
}