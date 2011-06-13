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


/**
 * Helios_Collection
 *
 * Is a collection of
 *  -
 *
 */

namespace Helios;

class Collection implements \IteratorAggregate, \Countable, \ArrayAccess
{

    /**
     * A clone of the request that created this collection
     * @var Request
     */
    private $request;

    /**
     * @var Apache_Solr_Response
     */
    private $response;

    /**
     * @var array
     */
    private $documents;

    /**
     * @var array
     */
    private $facetFields;

    /**
     * @var array
     */
    private $facetQueries;

    /**
     * @var array
     */
    private $facetDates;





    /**
     *
     * @return Helios_Request
     */
    public function getRequest( )
    {
        return $this->request;
    }

    /**
     *
     * @param Helios_Request $query
     */
    public function setRequest( Request $request )
    {
        $this->request = clone $request;
    }

    /**
     *
     * @return Apache_Solr_Response
     */
    public function getResponse( )
    {
        return $this->response;
    }

    /**
     *
     * @param Apache_Solr_Response $response
     */
    public function setResponse( \Apache_Solr_Response $response )
    {
        $this->response = $response;
    }

    /**
     *
     * @return array
     */
    public function getDocuments( )
    {
        return $this->documents;
    }

    /**
     *
     * @param array $documents
     */
    public function setDocuments( $documents )
    {
        $this->documents = $documents;
    }

    /**
     *
     * @return array
     */
    public function getFacetFields( )
    {
        return $this->facetFields;
    }

    /**
     *
     * @param array $facetFields
     */
    public function setFacetFields( $facetFields )
    {
        $this->facetFields = $facetFields;
    }

    /**
     *
     * @return array
     */
    public function getFacetQueries( )
    {
        return $this->facetQueries;
    }

    /**
     *
     * @param array $facetQueries
     */
    public function setFacetQueries( $facetQueries )
    {
        $this->facetQueries = $facetQueries;
    }

    /**
     *
     * @return array
     */
    public function getFacetDates( )
    {
        return $this->facetDates;
    }

    /**
     *
     * @param array $facetDates
     */
    public function setFacetDates( $facetDates )
    {
        $this->facetDates = $facetDates;
    }

    /**
     *
     * @return ArrayIterator
     */
    public function getIterator( )
    {
        return new \ArrayIterator( (array) $this->documents );
    }

    /**
     *
     * @return int
     */
    public function count( )
    {
        if ( !is_array( $this->documents ) ) return 0;

        return count( $this->documents );
    }

    /**
     *
     * @param string $key
     */
    public function offsetGet( $key )
    {
        return $this->documents[ $key ];
    }

    /**
     *
     * @param string $key
     * @param mixed $value
     */
    public function offsetSet( $key, $value )
    {
        $this->documents[ $key ] = $value;
    }

    /**
     *
     * @param string $key
     * @return bool
     */
    public function offsetExists( $key )
    {
        return isset( $this->documents[ $key ] );
    }

    /**
     *
     * @param string $key
     */
    public function offsetUnset( $key )
    {
        unset( $this->documents[ $key ] );
    }

    /**
     * Pagination: Get total pages
     * @return integer|null
     */
    public function getPageSize( )
    {
        if( null === $this->getRequest() ) return null;

        $limit = $this->getRequest()->getLimit( );
        $totalResults = $this->resultsCount();

        return ceil( $totalResults / $limit );
    }

    /**
     * Pagination: Get current page
     * @return integer|null
     */
    public function getCurrentPage( )
    {
        if( null === $this->getRequest() ) return null;

        return ( $this->getRequest()->getOffset( ) / $this->getRequest()->getLimit( ) );
    }

    /**
     * Pagibnation: Get Total results count
     * @return integer
     */
    public function getRecordsFound( )
    {
        return isset( $this->getResponse()->response->numFound ) ? intval( $this->getResponse()->response->numFound ) : 0;
    }

}