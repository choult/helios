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

class HQL
{
    /**
     * $q->from( 'Films, Features, Articles' )
     *   ->where( 'name_txt = ? OR venue_name_txt = ? OR annotation_txt = ?', array( $keyword, $keyword, $keyword ) )
     *   ->orderBy( 'name_txt asc' );
     *
     * $q->where( 'occurrence_date BETWEEN ? AND ?', array() );
     * $q->where( 'occurrence_date NOT BETWEEN ? AND ?', array() );
     *
     *
     * $q->addWhere( 'section_htag' );
     */

    /**
     * @var array
     */
    private $from;

    /**
     * @var array
     */
    private $where;

    /**
     * @var array
     */
    private $order;

    /**
     * @var array
     */
    private $facet;




    /**
     * Sets document types
     *
     * @param string $from
     * @return Helios_HQL
     */
    public function from( $from )
    {
        $this->from = array( );

        return $this->addFrom( $from );
    }

    /**
     * Adds document types to the from array
     *
     * @param string $from
     * @return Helios_HQL
     */
    public function addFrom( $from )
    {
        if ( !is_array( $this->from ) ) $this->from = array( );

        $this->from = array_merge( $this->from, $this->explode( $from ) );

        return $this;
    }

    /**
     *
     * @param string $where
     * @param mixed $params
     */
    public function where( $where, $params )
    {
        $this->where = array( );

        $this->addWhere( $where, $params );
    }

    /**
     *
     * @param string $where
     * @param mixed $params
     */
    public function andWhere( $where, $params )
    {
        $this->where[ ] = 'AND';

        $this->addWhere( $where, $params );
    }

    /**
     *
     * @param string $where
     * @param mixed $params
     */
    public function orWhere( $where, $params )
    {
        $this->where[ ] = 'OR';

        $this->addWhere( $where, $params );
    }

    /**
     *
     * @param string $where
     * @param mixed $params
     * @todo parse localsolr params (e.g. lat, lng, radius) out of query into params[]
     */
    private function addWhere( $where, $params )
    {
        // arrayize the params value
        if ( !is_array( $params ) ) $params = array ( $params );

        // check parameters vs placeholders
        preg_match_all( '/\?/', $where, $matches );

        if ( count( $matches[ 0 ] ) != count( $params ) )
            throw new Exception( 'Too many/few parameters' );

        // replace not between
        $where = preg_replace( '/([\w\d-_]*)\s?NOT BETWEEN\s?\? AND \?/', '(*:* -$1:[? TO ?])', $where );

        // replace between
        $where = preg_replace( '/([\w\d-_]*)\s?BETWEEN\s?\? AND \?/', '$1:[? TO ?]', $where );

        // replace not equal
        $where = preg_replace( '/([\w\d-_]*)\s?!=\s?\?/', '(*:* -$1:?)', $where );

        // replace equal
        $where = preg_replace( '/([\w\d-_]*)\s?=\s?\?/', '$1:?', $where );


        // relace placeholders
        foreach ( $params as $param )
        {
            $where = preg_replace( '/\?/', $param, $where, 1 );
        }


        $this->where[ ] = '(' . $where . ')';
    }

    /**
     *
     * @return Helios_HQL
     */
    public function orderBy( $order )
    {
        $this->order = array( );

        return $this->addOrderBy( $order );
    }

    /**
     *
     * @return Helios_HQL
     */
    public function addOrderBy( $order )
    {
        if ( !is_array( $this->order ) ) $this->order = array( );

        $this->order = array_merge( $this->order, $this->explode( $order ) );

        return $this;
    }

    /**
     *
     * @param string $fields
     */
    public function facetOn( $fields )
    {
        return $this->addFacet( $fields );
    }

    /**
     *
     * @param string $fields
     */
    public function addFacet( $fields )
    {
        if ( !is_array( $this->facet ) ) $this->facet = array( );

        $this->facet = array_merge( $this->facet, $this->explode( $fields ) );

        return $this;
    }

    /**
     * Builds the query string
     *
     * @return string
     */
    public function build( )
    {
        $query = '';

        // build from part of query
        if ( count( $this->from ) > 0 )
        {
            foreach ( $this->from as $from )
            {
                $froms[ ] = Helios::TYPE_FIELD_NAME . ':' . $from;
            }

            // froms resolve to doc types therefore we need to OR together these fields
            $query .= '(' . implode( ' OR ', $froms ) . ')';
        }

        // build where conditions
        if ( count( $this->where ) > 0 )
        {
            $query .= implode( ' ', $this->where );
        }


        return $query;
    }

    /**
     * Builds the query parameters
     *  - facets
     *  - filter queries
     *  - sorts
     *
     * @return array
     */
    public function params( )
    {
        $params = array( );

        // build sort parameters
        if ( count( $this->order ) > 0 )
        {
            $params[ 'sort' ] = implode( ',', $this->order );
        }

        // build facet parameters
        if ( count( $this->facet ) > 0 )
        {
            $params[ 'facet' ]          = 'true';
            $params[ 'facet.sort' ]     = 'count';
            $params[ 'facet.limit' ]    = '100';
            $params[ 'facet.offset' ]   = '0';
            $params[ 'facet.mincount' ] = '1';
            $params[ 'facet.missing' ]  = 'true';
            $params[ 'facet.prefix' ]   = '';

            $params[ 'facet.field' ][ ] = $this->facet;
        }

        return $params;
    }

    /**
     *
     * @param int $offset
     * @param int $limit
     * @return Helios_Collection
     */
    public function execute( $offset = 0, $limit = 10 )
    {
        $req = new Request( );

        $req->setQuery( $this->build( ) );

        $req->setParams( $this->params( ) );

        return $req->execute( $offset, $limit );
    }

    /**
     * Escapes special characters
     *
     * @uses Apache_Solr_Service
     * @param string $value
     * @return string
     */
    static public function escape( $value )
    {
        return \Apache_Solr_Service::escape( $value );
    }

    /**
     *
     * @param string $string
     * @return array
     */
    private function explode( $string )
    {
        $string = trim( $string, ' ,' );

        $bits = preg_split( '/,\s?/', $string );

        return $bits;
    }





}