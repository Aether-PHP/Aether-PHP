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
 *  → https://github.com/Aether-PHP/Aether-PHP
 *
*/
declare(strict_types=1);

namespace Aether\Http;


class HttpParameterUnpacker {

    /** @var array $_decoded */
    private array $_decoded;


    /**
     * The goal here is to extract data from streams to translate it to accessible array.
     */
    public function __construct(HttpParameterTypeEnum $_type){
        if ($_type === HttpParameterTypeEnum::PHP_INPUT)
            $this->_decoded = json_decode(file_get_contents('php://input'), true);
        elseif ($_type === HttpParameterTypeEnum::FORM_INPUT)
            $this->_decoded = $_POST;
    }

    /**
     * Check if _attr is in the decoded arr before returning it.
     *
     * @param string $_attr
     *
     * @return mixed
     */
    public function _getAttribute(string $_attr) : mixed {
        if (!isset($this->_decoded[$_attr]))
            return false;

        return $this->_decoded[$_attr];
    }
}