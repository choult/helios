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

class Connection
{
    /**
     * @var Helios_Connection instance
     */
    static private $instance;

    /**
     * @var Apache_Solr_Service_Balancer
     */
    private $service;

    /**
     * @var bool autoCommit
     */
    private $autoCommit;

    /**
     * @var Config config
     */
    private $config;

    /**
     *
     */
    public function __construct( Config $config = null, $autoCommit = false )
    {
        $this->setConfig( $config );

        // init auto commit
        $this->autoCommit = $autoCommit;
    }

    /**
     * Returns the Config for this Connection, statically if not set
     *
     * @return Config
     */
    public function getConfig()
    {
        return ( $this->config !== null ) ? $this->config : Helios::config();
    }

    /**
     * Sets the Config for this Connection
     *
     * @param Config $config
     */
    public function setConfig( Config $config = null )
    {
        $this->config = $config;
    }

    /**
     *
     * @return Helios_Connection
     */
    public function getInstance( )
    {
        if ( !isset( self::$instance ) )
        {
            self::$instance = new Connection( );
        }

        return self::$instance;
    }

    /**
     *
     */
    public function initialize( )
    {
        $config = $this->getConfig();
        $servers = $config[ 'servers' ];

        foreach ( $servers[ 'readable' ] as $server )
        {
            $readableServices[ ] = new \Apache_Solr_Service( $server[ 'host' ], $server[ 'port' ], $server[ 'path' ] );
        }

        foreach ( $servers[ 'writable' ] as $server )
        {
            $writeableServices[ ] = new \Apache_Solr_Service( $server[ 'host' ], $server[ 'port' ], $server[ 'path' ] );
        }

        $this->service = new \Apache_Solr_Service_Balancer( $readableServices, $writeableServices );

        return true;
    }

    /**
     * Returns the connection
     *
     * @return Apache_Solr_Service_Balancer
     */
    public function getService( )
    {
        return $this->service;
    }

    /**
     * Gets auto commit value
     *
     * @return bool
     */
    public function getAutoCommit( )
    {
        return $this->autoCommit;
    }

    /**
     * Sets auto commit value
     * @param bool $autoCommit
     */
    public function setAutoCommit( $autoCommit )
    {
        $this->autoCommit = $autoCommit;
    }

}