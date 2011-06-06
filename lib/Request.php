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

require_once 'Helios.php';

class Helios_Request
{
    /**
     * @var string
     */
    private $query;

    /**
     * @var array
     */
    private $params = array( );

    /**
     * Execute query, create collection
     *
     * @param int $offset
     * @param int $limit
     */
    public function execute( $offset = 0, $limit = 10 )
    {
        $hydrator = new Helios_Hydrator( );

        $params = array( );

        // check for query before passing to Apache lib
        if ( empty( $this->query ) ) throw new Helios_Exception( 'Query not defined' );
        
        // Helios uses it's own documents so turn this off
        Helios_Connection::getService( )->setCreateDocuments( false );

        $response = Helios_Connection::getService( )->search( $this->query, $offset, $limit, $this->params );

        $collection = $hydrator->hydrate( $this, $response );

        return $collection;
    }
    
    /**
     *
     * @return string
     */
    public function getQuery( )
    {
        return $this->query;
    }

    /**
     *
     * @param string $query
     */
    public function setQuery( $query )
    {
        if ( !is_string( $query ) )
            throw new Helios_Exception( 'Query must be a string, ' . gettype( $query ) . ' given' );

        $this->query = $query;
    }

    /**
     *
     * @return array
     */
    public function getParams( )
    {
        return $this->params;
    }

    /**
     *
     * @param array $params
     */
    public function setParams( $params )
    {
        if ( !is_array( $params ) )
            throw new Helios_Exception( 'Params must be an array, ' . gettype( $params ) . ' given' );

        $this->params = $params;
    }


}