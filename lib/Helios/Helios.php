<?php

/**
 * Copyright (c) 2010 Rhodri Davies
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 *
 * Helios - PHP Solr Client
 *
 * @package Helios
 * @author Rhodri Davies (http://github.com/rhodrid)
 * @version 0.1
 *
 */

namespace Helios;

class Helios
{
    const UID_FIELD_NAME = 'uid';
    const TYPE_FIELD_NAME = 'type';
    const ID_FIELD_NAME = 'id';
    const UID_DELIMITER = '::';

    /**
     *
     * @var Helios_Config
     */
    static private $heliosConfig;

    /**
     *
     * @param string $config
     */
    static public function configure( $config )
    {
        if ( $config instanceOf Config )
        {
            self::$heliosConfig = $config;
        }
        else
        {
            self::$heliosConfig = new Config;
            self::$heliosConfig->load( $config );
        }

        return true;
    }

    /**
     *
     */
    static public function config( $section = null )
    {
        if ( !self::$heliosConfig instanceof Config )
        {
            throw new Exception( 'Configuration not loaded' );
        }

        return ( $section !== null ) ? self::$heliosConfig[ $section ] : self::$heliosConfig;
    }

    /**
     * Very simple autoloader
     *
     * @param string $className
     * @return bool
     */
    static public function autoload( $className )
    {
        if ( 0 !== stripos( $className, 'Helios_' ) )
        {
            return false;
        }

        $class = dirname( __FILE__ ) . '/' . str_replace( 'Helios_', '', $className ) . '.php';

        if ( file_exists( $class ) )
        {
            require_once $class;

            return true;
        }

        return false;
    }

    static public function destroy( )
    {
        self::$heliosConfig = null;
    }
}