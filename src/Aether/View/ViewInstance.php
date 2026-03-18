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

namespace Aether\View;

use Aether\Exception\ViewTemplateException;


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
     * @throws ViewTemplateException
     */
    public function _render(){
        $viewsRoot = realpath("views");
        if ($viewsRoot === false)
            throw new ViewTemplateException("Views folder not found.");

        # - View path hardening : no traversal, no absolute paths, no weird chars
        $path = str_replace("\\", "/", trim($this->_path));
        $path = ltrim($path, "/");

        if ($path === "" || str_contains($path, "..") || !preg_match('/^[a-zA-Z0-9_\\-\\/]+$/', $path))
            throw new ViewTemplateException("Invalid view path provided.");

        $fullpath = $viewsRoot . "/" . $path . "." . self::$_ext;

        # - Security check : if extension is not PHP then we do NOT want any PHP executed
        if (self::$_ext !== "php"){
            if (!file_exists($fullpath))
                throw new ViewTemplateException("Template not found : {$fullpath}");

            $real = realpath($fullpath);
            if ($real === false || !str_starts_with($real, $viewsRoot))
                throw new ViewTemplateException("Invalid view path provided.");

            echo file_get_contents($fullpath);
            return;
        }

        # - We extract and translate data from self::$_vars into php variables
        \extract($this->_vars, EXTR_SKIP);

        if (!file_exists($fullpath))
            throw new ViewTemplateException("Template not found : {$fullpath}");

        $real = realpath($fullpath);
        if ($real === false || !str_starts_with($real, $viewsRoot))
            throw new ViewTemplateException("Invalid view path provided.");

        # - We turn output bufferin on, and we include the given view page
        \ob_start();
        require_once $fullpath;
        echo \ob_get_clean();
    }


    /**
     * @param string $path
     * @param array $vars
     * @throws ViewTemplateException
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