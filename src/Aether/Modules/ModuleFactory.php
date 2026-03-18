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

namespace Aether\Modules;


abstract class ModuleFactory {

    /** @var array $_modules */
    private static array $_modules = [];


    /**
     * Function triggered when Aether Core is initialized
     * Scan all modules and trigger _onLoad()
     *
     * @param array $_modules
     */
    public static function _load(array $_modules) : void {

        foreach ($_modules as $module){
            $class = new $module();

            if (!$class instanceof AetherModule){
                echo "[ModuleFactory] - Error - {$module} does not implement AetherModule.";
                die();
            }
            $class->_onLoad();
        }

        self::$_modules = array_merge(self::$_modules, $_modules);
    }

    /**
     * @param string $_class
     *
     * @return bool
     */
    public static function _isLoaded(string $_class) : bool {
        return in_array($_class, self::$_modules);
    }

    /**
     * Permits to easily instantiate the class
     *
     * @return AetherModule
     */
    abstract public static function _make() : AetherModule;

}