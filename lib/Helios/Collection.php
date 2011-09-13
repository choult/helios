<?php

/**
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
 * @version 0.1
 *
 */


/**
 * Collection
 *
 * Is a collection of
 *  -
 *
 */

namespace Helios;

class Collection
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
    private $records;

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
     * @var type
     */
    private $facetRanges;

    /**
     *
     * @var integer
     */
    private $numRecords = 0;

    /**
     *
     * @return Request
     */
    public function getRequest( )
    {
        return $this->request;
    }

    /**
     *
     * @param Request $query
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
     * Get array of records
     *
     * @return array When group query hydrated, it would be array( n => array( docs, .. ) ). Otherwise it would be array( docs, ...)
     */
    public function getRecords( )
    {
        return $this->records;
    }

    /**
     *
     * @param array $records
     */
    public function setRecords( array $records )
    {
        $this->records = $records;
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
     * Pagination: Get result limit
     * @return integer
     */
    public function getPageSize( )
    {
        if( null === $this->getRequest() ) return 0;

        return $this->getRequest()->getLimit( );
    }

    /**
     * Pagination: Get current page
     * @return integer
     */
    public function getCurrentPage( )
    {
        if( null === $this->getRequest() ) return null;

        if( $this->getRequest()->getLimit( ) == 0 )
        {
            return 1;
        }

        $page = ( $this->getRequest()->getOffset( ) / $this->getRequest()->getLimit( ) ) + 1;

        return ( $page > 0 ) ? $page : 1;
    }

    /**
     * Get Total pages
     *
     * @return integer
     */
    public function getPageCount()
    {
        if( null === $this->getRequest() ) return null;

        $limit = $this->getPageSize( );
        $totalResults = $this->getNumRecords();
        
        //Division by zero
        if( $limit == 0 )
        {
            return 0;
        }
        return ceil( $totalResults / $limit );
    }

    /**
     * Pagibnation: Get Total results count
     * @return integer
     */
    public function getNumRecords( )
    {
        return $this->numRecords;
    }

    /**
     * Set number of records/documents Found
     *
     * @param integer $records
     */
    public function setNumRecords( $recordsCount )
    {
        $this->numRecords = $recordsCount;
    }


    /**
     * Get Facet ranges
     * @return array
     */
    public function getFacetRanges()
    {
        return $this->facetRanges;
    }

    /**
     * Set facet ranges
     *
     * @param array $facetRanges
     */
    public function setFacetRanges( $facetRanges )
    {
        $this->facetRanges = $facetRanges;
    }


    /**
     * Get are records groupped
     *
     * @return boolean It will be true for "Group By" query
     */
    public function areRecordsGrouped()
    {
        $params = $this->getRequest()->getParams();
        return ( isset( $params[ 'group' ] ) && $params[ 'group' ] == true );
    }

}
