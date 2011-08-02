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

use Helios\Document;
use Helios\Collection;


class Hydrator
{
    private $documentClass;
    private $collectionClass;

    /**
     * Inject document class name
     *
     * @param string
     */
    public function setDocumentClass( $document )
    {
        if( !class_exists( $document ) )
        {
            throw new \InvalidArgumentException( "class '{$document}' could not be loaded" );
        }

        $this->documentClass = $document;
    }

    /**
     * Inject collection class name
     *
     * @param string $collection
     */
    public function setCollectionClass( $collection )
    {
        if( !class_exists( $collection ) )
        {
            throw new \InvalidArgumentException( "class '{$collection}' could not be loaded" );
        }

        $this->collectionClass = $collection;
    }

    /**
     * constructor
     */
    public function __construct()
    {
        $this->setCollectionClass( '\\Helios\\Collection' );
        $this->setDocumentClass( '\\Helios\\Document' );
    }

    /**
     *
     * @param Helios_Request $request
     * @param Apache_Solr_Response $response
     * @return Helios_Collection
     */
    public function hydrate( Request $request, \Apache_Solr_Response $response )
    {
        // Create collection class
        $collection = new $this->collectionClass();
        $collection->setResponse( $response );
        $collection->setRequest( $request );

        $requestParams = $request->getParams();

        $documents = array();
        if( isset( $requestParams['group'] ) && $requestParams['group'] == true)
        {
            if( !empty( $response->grouped ) )
            {
                $groupField = array_shift( $response->grouped );
                $documents = $this->hydrateGroups( $groupField->groups );
                $collection->setNumRecords( $groupField->ngroups );
            }
        }
        else
        {
            $documents = $this->hydrateDocuments( $response->response );
            $collection->setNumRecords( $response->numFound );
        }

        // Save the result
        $collection->setRecords( $documents );

        if( isset( $response->facet_counts ) )
        {
            $payload = $response->facet_counts;
            $facetFields = array(
                // Facet        =>  function to set data in collection
                'facet_fields'  => 'setFacetFields',
                'facet_queries' => 'setFacetQueries',
                'facet_dates'   => 'setFacetDates',
                'facet_ranges'  => 'setFacetRanges',
            );

            foreach( $facetFields as $facetFieldName => $method )
            {
                $facets = $this->hydrateFacetFields( $facetFieldName, $payload );

                if( is_array( $facets ) && !empty( $facets ) )
                {
                    $collection->$method( $facets );
                }
            }
        }

        return $collection;
    }

    /**
     * Hydrate Facet Fields
     * @param string $field Facet field name
     * @param array $payload
     * @return mixed array||null
     */
    protected function hydrateFacetFields( $field, $payload )
    {
        if( !isset( $payload->$field ) || empty( $payload->$field ) )
        {
            return;
        }

        $facets = array();
        foreach( $payload->$field as $name => $list )
        {
            $facets[ ] = $this->hydrateFacet( $name, $list );
        }

        return $facets;
    }

    /**
     * Generate documents
     *
     * @param object Solr JSON Response object
     * @return array
     */
    protected function hydrateDocuments( $response )
    {
        if( !isset( $response->docs ) || empty( $response->docs ) )
        {
            return array();
        }

        $documents = array();
        foreach( $response->docs as $document )
        {
            $documents[ ] = new $this->documentClass( (array)$document );
        }
        return $documents;
    }


    /**
     * Generate documents for grouped results
     *
     * @param object $groups Solr JSON object for groups
     * @return array
     */
    protected function hydrateGroups( $groups )
    {
        if( empty( $groups ) )
        {
            return array();
        }

        $groupDocuments = array();
        foreach( $groups as $group )
        {
            if( !isset( $group->doclist->docs ) || empty( $group->doclist->docs ) )
            {
                continue;
            }

            $groupDocuments[] = $this->hydrateDocuments( $group->doclist );
        }

        return $groupDocuments;
    }

    /**
     * Hydrate facet
     *
     * @param string $name
     * @param array $list
     * @return Facet
     */
    protected function hydrateFacet( $name, $list )
    {
        $facet = new Facet( );

        $facet->setName( $name );

        foreach ( $list as $name => $count )
        {
            $tag = new Tag( );

            $tag->setName( $name );
            $tag->setTally( $count );

            $tags[ ] = $tag;
        }

        $facet->setTags( $tags );

        return $facet;
    }
}
