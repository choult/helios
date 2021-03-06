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
     * $q->andWhere( 'section_htag' );
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
    private $facets = array();

    /**
     *
     * @var array
     */
    private $facetFields = array();

    /**
     *
     * @var array
     */
    private $filterQuery = array();

    /**
     *
     * @var array
     */
    private $groupFields = array();

    /**
     *
     * @var array
     */
    private $groupQuery = array();

    /**
     * @var integer
     */
    private $offset;

    /**
     * @var integer
     */
    private $limit;

    /**
     * @var type
     */
    private $params;

    /**
     * Constructor
     */
    public function __construct( )
    {
        // set Default offset / limit
        $this->setFirstResult( 0 );
        $this->setMaxResults( 10 );
    }


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

        $this->where[ ] = $this->getWhereQuery( $where, $params );
    }

    /**
     *
     * @param string $where
     * @param mixed $params
     */
    public function andWhere( $where, $params )
    {
        if ( is_array( $this->where ) && count( $this->where ) )
        {
            $this->where[ ] = 'AND';
        }
        $this->where[ ] = $this->getWhereQuery( $where, $params );
    }

    /**
     *
     * @param string $where
     * @param mixed $params
     */
    public function orWhere( $where, $params )
    {
        if ( is_array( $this->where ) && count( $this->where ) )
        {
            $this->where[ ] = 'OR';
        }
        $this->where[ ] = $this->getWhereQuery( $where, $params );
    }

    /**
     *
     * @param string $where
     * @param mixed $params
     * @return string Formatted solr where query
     * @todo parse localsolr params (e.g. lat, lng, radius) out of query into params[]
     */
    private function getWhereQuery( $where, $params )
    {
        // arrayize the params value
        if ( !is_array( $params ) ) $params = array ( $params );

        // check parameters vs placeholders
        preg_match_all( '/\?/', $where, $matches );

        if ( count( $matches[ 0 ] ) != count( $params ) )
            throw new Exception( 'Too many/few parameters' );

        // replace not equal
        $where = preg_replace( '/([\w\d-_]*)\s?!=\s?\?/', '(*:* -$1:"?")', $where );

        // replace to
        $where = preg_replace( '/([\w\d-_]*)\s?=\s?\? TO ?\?/', '$1:["?" TO "?"]', $where );

        // replace equal
        $where = preg_replace( '/([\w\d-_]*)\s?=\s?\?/', '$1:"?"', $where );


        // relace placeholders
        foreach ( $params as $param )
        {
            $where = preg_replace( '/\?/', self::escape( $param ), $where, 1 );
        }


        return '(' . $where . ')';
    }

    /**
     * Filter by between
     *
     * @param string $between
     * @param mixed $params
     */
    public function between( $between, $params )
    {
        $this->filterQuery = array();

        $this->addBetween( $between, $params );
    }

    /**
     * And between
     *
     * @param string $between
     * @param mixed $params
     */
    public function andBetween( $between, $params )
    {
        $this->addBetween( $between, $params );
    }

    /**
     * or between
     *
     * @param string $between
     * @param mixed $params
     */
    public function orBetween( $between, $params )
    {
        /**
         * Current implementation of Filter Query does not support OR query
         * All quries are passed SOLR as and separated query.
         * @see $this->params() for implementation
         */
        throw new \BadMethodCallException( "This function is deprecated" );

        if ( is_array( $this->filterQuery ) && count( $this->filterQuery ) )
        {
            $this->addFilterQuery( 'OR' );
        }

        $this->addBetween( $between, $params );
    }

    /**
     * Add Range search
     *
     * @param string $between
     * @param mixed $params
     */
    private function addBetween( $between, $params )
    {
        // arrayize the params value
        if ( !is_array( $params ) ) $params = array ( $params );

        // check parameters vs placeholders
        preg_match_all( '/\?/', $between, $matches );

        if ( count( $matches[ 0 ] ) != count( $params ) )
            throw new Exception( 'Too many/few parameters' );

        // replace not between
        $between = preg_replace( '/([\w\d-_]*)\s?NOT BETWEEN\s?\? AND \?/', '(*:* -$1:[? TO ?])', $between );

        // replace between
        $between = preg_replace( '/([\w\d-_]*)\s?BETWEEN\s?\? AND \?/', '$1:[? TO ?]', $between );

        // relace placeholders
        foreach ( $params as $param )
        {
            $between = preg_replace( '/\?/', self::escape( $param ), $between, 1 );
        }


        $this->addFilterQuery( '(' . $between . ')' );
    }

    /**
     * Adds a filter query
     *
     * @param string $query
     */
    public function addFilterQuery( $query, $params = array() )
    {
        // relace placeholders
        foreach ( $params as $param )
        {
            $query = preg_replace( '/\?/', self::escape( $param ), $query, 1 );
        }
        $this->filterQuery[] = $query;
    }

    /**
     * Filter by distance
     *
     * @param string $field The field to search on
     * @param float $latitude The latitude to search around
     * @param float $longitude The longitude to search around
     * @param float $distance The radius of the search (in km)
     */
    public function within( $field, $latitude, $longitude, $distance )
    {
        $this->filterQuery = array();
        $this->addWithin( $field, $latitude, $longitude, $distance );
    }

    /**
     * And within
     *
     * @param string $field The field to search on
     * @param float $latitude The latitude to search around
     * @param float $longitude The longitude to search around
     * @param float $distance The radius of the search (in km)
     */
    public function andWithin( $field, $latitude, $longitude, $distance )
    {
        $this->addWithin( $field, $latitude, $longitude, $distance );
    }

    /**
     * Or within
     *
     * @param string $field The field to search on
     * @param float $latitude The latitude to search around
     * @param float $longitude The longitude to search around
     * @param float $distance The radius of the search (in km)
     */
    public function orWithin( $field, $latitude, $longitude, $distance )
    {
        /**
         * Current implementation of Filter Query does not support OR query
         * All quries are passed SOLR as and separated query.
         * @see $this->params() for implementation
         */
        throw new \BadMethodCallException( "This function is deprecated" );

        if ( is_array( $this->filterQuery ) && count( $this->filterQuery ) )
        {
            $this->addFilterQuery( 'OR' );
        }
        $this->addWithin( $field, $latitude, $longitude, $distance );
    }

    /**
     * Add Filter by distance
     *
     * @param string $field The field to search on
     * @param float $latitude The latitude to search around
     * @param float $longitude The longitude to search around
     * @param float $distance The radius of the search (in km)
     */
    private function addWithin( $field, $latitude, $longitude, $distance )
    {
        if ( !$field )
        {
            throw new \InvalidArgumentException( 'No field specified for within search' );
        }

        if ( !is_numeric( $latitude ) )
        {
            throw new \InvalidArgumentException( 'Latitude must be numeric' );
        }

        if ( !is_numeric( $longitude ) )
        {
            throw new \InvalidArgumentException( 'Longitude must be numeric' );
        }

        if ( !is_numeric( $distance ) || $distance <= 0 )
        {
            throw new \InvalidArgumentException( 'Distance must be a number greater than zero' );
        }


        $this->setParam( 'sfield', $field );
        $this->setParam( 'pt', $latitude . ',' . $longitude );
        $this->setParam( 'd', $distance );

        $this->addFilterQuery( '{!geofilt}' );
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
     * @return HQL
     */
    public function addFacet( $facet, $value )
    {
        if( empty( $facet ) )
        {
            throw new \InvalidArgumentException( 'addFacet expects first argument to be string value' );
        }

        $this->facets[ $facet ] = $value;

        return $this;
    }

    /**
     * Get facets Key => value pair
     * @return array
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     *
     * @param string $field
     * @return HQL
     */
    public function addFacetField( $field )
    {
        if( empty( $field ) || !is_string( $field ) )
        {
            throw new \InvalidArgumentException( 'Expect first argument to be string' );
        }

        if( !in_array( $field, $this->getFacetFields() ) )
        {
            $this->facetFields[] = $field;
        }

        return $this;
    }

    /**
     * Get facet Fields
     *
     * @return array
     */
    public function getFacetFields()
    {
        return $this->facetFields;
    }

    /**
     * Add Facet range field, Range require Start, End and Gap...
     * Eg: field: occurrence_dates, rangeStart: 2011-01-01T18:50:00Z, rangeEnd 2011-01-10T10:10:00Z, rangeGap:+1Day
     * options can be( facet.range.hardend, facet.range.other, facet.range.include )
     *
     * @see: http://wiki.apache.org/solr/SimpleFacetParameters
     *
     * @param string $field
     * @param string $rangeStart
     * @param string $rangeEnd
     * @param string $rangeGap
     * @param array $options
     * @return HQL
     */
    public function addFacetRange( $field, $rangeStart, $rangeEnd, $rangeGap, array $options = null )
    {
        $facets = $this->getFacets();
        if( !array_key_exists( 'facet.range' ,$facets ) )
        {
            $rangeFields = array();
        }else{
            $rangeFields = $facets['facet.range'];
        }

        $rangeFields[] = $field;
        $this->addFacet( 'facet.range', $rangeFields );

        $this->addFacet( 'f.' . $field . '.facet.range.start', $rangeStart );
        $this->addFacet( 'f.' . $field . '.facet.range.end', $rangeEnd );
        $this->addFacet( 'f.' . $field . '.facet.range.gap', $rangeGap );

        // add additional options
        if( is_array( $options ) && !empty( $options ) )
        {
            foreach( $options as $key => $value )
            {
                $this->addFacet( 'f.' . $field . '.' . $key , $value );
            }
        }

        return $this;
    }

    /**
     * Group search resuts by a "STRING" solr field
     * *Currently Solr 3.4 only group single fields, hence everytime user call this function old group fields will be replaced with new.*
     *
     * @param string $fieldName     field name to group records, This should be a STRING field
     * @param integer $groupLimit   No of documents limit per group, each group can have "n" number of documents in them
     * @param integer $groupOffset  Offset of group documents. This only apply to individual groups, not global results set
     * @param string $groupSort  Order group by
     *
     * @see http://wiki.apache.org/solr/FieldCollapsing for more information
     */
    public function groupBy( $fieldName, $groupLimit = 1, $groupOffset = 0, $groupSort = false )
    {
        $this->groupFields = array(
            'field'     => $fieldName,
            'limit'     => $groupLimit,
            'offset'    => $groupOffset,
        );

        if( false !== $groupSort )
        {
            $this->groupFields[ 'sort' ] = $groupSort;
        }

    }

    /**
     * Query group documents with where query
     *
     * @param string $where
     * @param mixed $params
     */
    public function groupWhere( $where, $params )
    {
        $this->groupQuery = array();

        $this->groupQuery[ ] = $this->getWhereQuery( $where, $params );
    }

    /**
     * Add another where query to group results
     *
     * @param string $where
     * @param mixed $params
     */
    public function andGroupWhere( $where, $params )
    {
        if( !empty( $this->groupQuery ) )
        {
            $this->groupQuery[ ] = 'AND';
        }

        $this->groupQuery[ ] = $this->getWhereQuery( $where, $params );
    }

    /**
     * Add an OR where query to group results
     *
     * @param string $where
     * @param mixed $params
     */
    public function orGroupWhere( $where, $params )
    {
        if( !empty( $this->groupQuery ) )
        {
            $this->groupQuery[ ] = 'OR';
        }

        $this->groupQuery[ ] = $this->getWhereQuery( $where, $params );
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
            if( trim( $query ) != '' )
            {
                $query .= ' AND '; // require when merging FROM & WHERE, they are same in SOLR query
            }

            $query .= implode( ' ', $this->where );
        }

        return $query;
    }

    /**
     * Sets a parameter value to be passed to SOLR
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function setParam( $key, $value )
    {
        $this->params[ $key ] = $value;
    }

    /**
     * Builds the query parameters
     *  - facets
     *  - filter queries
     *  - sorts
     *
     * @return array
     */
    public function params()
    {
        $params = ( $this->params ) ? $this->params : array();

        /**
         * Set Defaults
         */
        if( count( $this->getFacets() ) > 0 || count( $this->getFacetFields() ) > 0 )
        {
            $params[ 'facet' ]          = 'true';
            $params[ 'facet.sort' ]     = 'count';
            $params[ 'facet.limit' ]    = '100';
            $params[ 'facet.offset' ]   = '0';
            $params[ 'facet.mincount' ] = '1';
            $params[ 'facet.missing' ]  = 'true';
            $params[ 'facet.prefix' ]   = '';
        }

        // build sort parameters
        if ( count( $this->order ) > 0 )
        {
            $params[ 'sort' ] = implode( ',', $this->order );
        }

        // build facet fields parameters
        if ( count( $this->getFacetFields() ) > 0 )
        {
            $params[ 'facet.field' ] = $this->getFacetFields();
        }

        // apply facet overrides
        if( count( $this->getFacets() ) > 0 )
        {
            $params = array_merge( $params, $this->getFacets() );
        }

        // Apply Filter Query
        if( count( $this->filterQuery ) > 0 )
        {
            /**
             * Pass the Filter Query as Array, this will be parsed as AND filter queries..
             * it seems that OR applied to any rules within 1 FQ. When we should be using AND, it should be a separate FQ
             */
            $params[ 'fq' ] = $this->filterQuery;
        }

        /* Build Group by */
        if( is_array( $this->groupFields ) && !empty( $this->groupFields ) )
        {
            $params[ 'group' ] = "true";          // Enable Field Collapsing
            $params[ 'group.ngroups' ] = "true";  // Set to return no of matched group sets ( this === numRowsFound )

            foreach( $this->groupFields as $key => $value )
            {
                $params[ "group.{$key}" ] = $value;
            }

            // Apply any Group query
            if( is_array( $this->groupQuery ) && !empty( $this->groupQuery ) )
            {
                $params[ 'group.query' ] = \implode(' ', $this->groupQuery );
            }
        }

        return $params;
    }

    /**
     * Execute this HQL query
     * @return Collection
     */
    public function execute( )
    {
        $req = new Request( );

        $req->setQuery( $this->build( ) );

        $req->setParams( $this->params( ) );

        $req->setOffset( $this->getFirstResult() );

        $req->setLimit( $this->getMaxresults() );

        return $req->execute( );
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

    /**
     * Set results offset
     *
     * @param integer $offset
     */
    public function setFirstResult( $offset )
    {
        if( !is_numeric( $offset ) )
        {
            throw new \Exception( 'setFirstResult expects $offset to be an integer value' );
        }

        $this->offset = $offset;
    }

    /**
     * Retrieve results offset
     *
     * @return integer
     */
    public function getFirstResult( )
    {
        return $this->offset;
    }

    /**
     * Set results limit
     *
     * @param integer $limit
     */
    public function setMaxResults( $limit )
    {
        if( !is_numeric( $limit ) )
        {
            throw new \Exception( 'setMaxResults expects $limit to be an integer value' );
        }

        $this->limit = $limit;
    }

    /**
     * Retrieve results limit
     *
     * @return integer
     */
    public function getMaxResults()
    {
        return $this->limit;
    }

    /**
     * Set result offset based on page number
     *
     * @param integer $page
     */
    public function setCurrentPage( $page )
    {
        if( !is_numeric( $page ) )
        {
            throw new \Exception( 'setCurrentPage expects $page argument Type Integer' );
        }

        $page = ( $page > 1 ) ? $page : 1 ;

        // calculate Offset
        $this->offset = ( $page * $this->getMaxResults() ) - $this->getMaxResults();
    }

    /**
     * Get caculated Current page based on ( offset / Limit )
     *
     * @return integer
     */
    public function getCurrentPage( )
    {
        $page = ceil( $this->getFirstResult() / $this->getMaxResults() ) + 1;

        return ( $page > 0 ) ? $page : 1 ;
    }

    /**
     * Constructs a complex query part for the given $fieldName
     *
     * The array $terms should be either an array or a deep array
     * Each time the depth increases, the operator between the parts changes (this behaviour may be flipped by employing the $and parameter)
     *
     * For example:
     *
     *   complexWhere( 'field', array( 'a', 'b', array( 'c', array( 'd', 'e' ) ), 'f' ) )
     *
     * would return a query part equivalent to:
     *
     *   (field:"a" AND field:"b" AND (field:"c" OR (field:"d" AND field"e")) OR field:"f")
     *
     * @param string $fieldName
     * @param array $terms An array of terms
     * @param boolean $and Defaults to true
     */
    public function complexWhere( $fieldName, $terms, $and = true )
    {
        list( $where, $params ) = $this->_complexWhere( $fieldName, $terms, $and );
        if ( count( $params ) )
        {
            $this->where( $where, $params );
        }
    }

    /**
     * Appends a complex query part, using an AND operator
     *
     * @see Hql::complexWhere
     *
     * @param string $fieldName
     * @param array $terms
     * @param boolean $and Defaults to true
     */
    public function andComplexWhere( $fieldName, $terms, $and = true )
    {
        list( $where, $params ) = $this->_complexWhere( $fieldName, $terms, $and );
        if ( count( $params ) )
        {
            $this->andWhere( $where, $params );
        }
    }

    /**
     * Appends a complex query part, using an OR operator
     *
     * @see Hql::complexWhere
     *
     * @param string $fieldName
     * @param array $terms
     * @param boolean $and Defaults to true
     */
    public function orComplexWhere( $fieldName, $terms, $and = true )
    {
        list( $where, $params ) = $this->_complexWhere( $fieldName, $terms, $and );
        if ( count( $params ) )
        {
            $this->orWhere( $where, $params );
        }
    }

    /**
     * The internal mechanism for generating a complex query part
     *
     * @see Hql::complexWhere
     *
     * @param string $fieldName
     * @param array $terms
     * @param boolean $and
     *
     * @return array An array in two parts, the first is the query, the second a flattened list of parameters
     */
    private function _complexWhere( $fieldName, array $terms, $and )
    {
        $operator = ( $and ) ? 'AND' : 'OR';

        $where = '';

        $params = array();

        foreach ( $terms as $term )
        {
            $where .= ( $where ) ? " $operator " : '';
            if ( is_array( $term ) )
            {
                list( $subwhere, $subparams ) = $this->_complexWhere( $fieldName, $term, !$and );
                $where .= $subwhere;
                $params = array_merge( $params, $subparams );
            }
            else
            {
                if ( preg_match( '/^!(.+)$/', $term, $matches ) )
                {
                    $term = $matches[ 1 ];
                    $where .= "$fieldName != ?";
                }
                else
                {
                    $where .= "$fieldName = ?";
                }
                $params[] = $term;
            }
        }

        $where = "($where)";

        return array( $where, $params );
    }

}
