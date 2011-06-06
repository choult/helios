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


class Helios_Hydrator
{
    /**
     *
     * @param Helios_Request $request
     * @param Apache_Solr_Response $response
     * @return Helios_Collection 
     */
    public function hydrate( Helios_Request $request, Apache_Solr_Response $response )
    {
        $collection = new Helios_Collection( );


        // hydrate documents
        $payload = $response->response;

        if ( isset( $payload->docs ) && count( $payload->docs ) > 0 )
        {
            foreach ( $payload->docs as $doc )
            {
                $documents[ ] = new Helios_Document( (array) $doc );
            }

            $collection->setDocuments( $documents );
        }


        // check for presense of facet counts
        if ( $response->facet_counts )
        {
            // hydrate facet fields
            $payload = $response->facet_counts;

            if ( isset( $payload->facet_fields ) && count( $payload->facet_fields ) > 0 )
            {
                $facets = array( );

                foreach ( $payload->facet_fields as $name => $list )
                {
                    $facets[ ] = $this->hydrateFacet( $name, $list );
                }

                $collection->setFacetFields( $facets );
            }


            // hydrate facet queries
            if ( !isset( $payload->facet_queries ) && count( $payload->facet_queries ) > 0 )
            {
                $facets = array( );

                foreach ( $payload->facet_queries as $name => $list )
                {
                    $facets[ ] = $this->hydrateFacet( $name, $list );
                }

                $collection->setFacetQueries( $facets );
            }


            // hydrate facet dates
            if ( !isset( $payload->facet_dates ) && count( $payload->facet_dates ) > 0 )
            {
                $facets = array( );

                foreach ( $payload->facet_dates as $name => $list )
                {
                    $facets[ ] = $this->hydrateFacet( $name, $list );
                }

                $collection->setFacetDates( $facets );
            }
        }

        return $collection;
    }



    public function hydrateFacet( $name, array $list )
    {
        $facet = new Helios_Facet( );

        $facet->setName( $name );

        for ( $i = 0, $j = count( $list ); $i < $j; $i += 2 )
        {
            $tag = new Helios_Tag( );

            $tag->setName( $list[ $i ] );
            $tag->setTally( $list[ $i + 1 ] );

            $tags[ ] = $tag;
        }

        $facet->setTags( $tags );

        return $facet;
    }


}
