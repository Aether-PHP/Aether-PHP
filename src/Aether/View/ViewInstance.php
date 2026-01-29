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

namespace Aether\View;


final class ViewInstance implements  ViewInterface {

    /** @var string $_path */
    private string $_path;

    /** @var array $_vars */
    private array $_vars;

    /** @var string $_ext */
    private static $_ext = "php";

    public function __construct(string $path, array $vars){
        $this->_path = $path;
        $this->_vars = $vars;
    }

    /**
     * Render the provided view
     *
     * @return void
     */
    public function _render(){
        $fullpath = "public/views/" . $this->_path . "." . self::$_ext;

        # - Security check : if extension is not php then we do NOT want any php executed
        if (self::$_ext !== "php"){
            if (!file_exists($fullpath))
                die("[View] - Error - Template not found : {$fullpath}");

            echo file_get_contents($fullpath);
            return;
        }

        # - We extract and translate data from self::$_vars into php variables
        \extract($this->_vars, EXTR_SKIP);

        if (!file_exists($fullpath))
            die("[View] - Error - Template not found : {$fullpath}");

        # - We turn output bufferin on and we include the given view page
        \ob_start();
        require_once $fullpath;
        echo \ob_get_clean();
    }


    /**
     * @param string $path
     * @param array $vars
     */
    public static function _make(string $path, array $vars){
        (new self($path, $vars))->_render();
    }

    /**
     * @return string
     */
    public static function _getExtension() : string { return self::$_ext; }

    /**
     * @param string $_ext
     */
    public static function _setExtension(string $_ext){ self::$_ext = $_ext; }
}