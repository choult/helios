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
 * @author Rhodri Davies
 * @version 0.1
 *
 */

namespace Helios;

class Config implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $config;

    /**
     *
     */
    public function __construct( $config = null )
    {
        $this->setConfig( $config );
    }

    public function setConfig( $config = null )
    {
        $this->config = $config;
    }

    /**
     * Uses sfYaml to load and parse a yaml file into Helios_Config::config
     *
     * @param string $yamlPath
     */
    public function load( $yamlPath )
    {
        $this->config = \sfYaml::load( $yamlPath );

        if ( !is_array( $this->config ) ) throw new Exception( 'Could not load config' );

        $this->sanitize( );
    }

    /**
     * checks config is loaded
     *
     * @return bool
     */
    public function isLoaded( )
    {
        return ( is_array( $this->config ) );
    }

    /**
     * checks config has required values
     *
     * @return bool
     */
    public function sanitize( )
    {
        // check loaded
        if ( !$this->isLoaded( ) ) throw new Exception( 'Configuration not loaded' );

        // check servers
        if ( !isset( $this->config[ 'servers' ] ) ) throw new Exception( 'Servers not found in config' );

        if ( !is_array( $this->config[ 'servers' ][ 'readable' ] ) )
        {
            throw new Exception( 'Readable servers not found in config' );
        }

        if ( !is_array( $this->config[ 'servers' ][ 'writable' ] ) )
        {
            throw new Exception( 'Writable servers not found in config' );
        }

        if ( count( $this->config[ 'servers' ][ 'readable' ] ) === 0 )
        {
            throw new Exception( 'There must be at least one readable server' );
        }

        if ( count( $this->config[ 'servers' ][ 'writable' ] ) === 0 )
        {
            throw new Exception( 'There must be at least one writable server' );
        }

        return true;
    }

    /**
     * Denys mutating of config values
     *
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet( $offset, $value )
    {
        throw new Exception( 'Setting of configuration values at runtime is not allowed, sorry' );
    }

    /**
     * Denys unsetting of config values
     *
     * @param string $offset
     */
    public function offsetUnset( $offset )
    {
        throw new Exception( 'Setting of configuration values at runtime is not allowed, sorry' );
    }

    /**
     * Checks if $offset exists in config array
     *
     * @param string $offset
     * @return bool
     */
    public function offsetExists( $offset )
    {
        return isset( $this->config[ $offset ] );
    }

    /**
     * Returns $offset's value from config array or null if not found
     *
     * @param string $offset
     * @return mixed, null
     */
    public function offsetGet( $offset )
    {
        return isset( $this->config[ $offset ] ) ? $this->config[ $offset ] : null;
    }

}

