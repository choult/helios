<?php

namespace Helios\Test;

use     Helios\Collection,
        Helios\Document,
        Helios\Helios,
        Helios\Facet,
        Helios\Request;
/**
 * Test class for Helios_Collection.
 * Generated by PHPUnit on 2010-02-10 at 13:35:50.
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Helios_Collection
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Collection;

        $rawResponse = file_get_contents( dirname(__FILE__) . '/../data/simpleResponse.json' );

        $this->response = new \Apache_Solr_Response( $rawResponse, array( 'HTTP/1.0 200 OK', 'Content-Type: text/plain; charset=UTF-8' ), false );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     *
     */
    public function testGetSetRequest()
    {
        $request = new Request;

        $this->object->setRequest( $request );

        $this->assertEquals( $request, $this->object->getRequest( ) );
    }

    /**
     *
     */
    public function testSetInvalidRequest()
    {
        $this->setExpectedException("Exception");
        $this->object->setRequest( $request );
    }

    /**
     *
     */
    public function testGetSetResponse()
    {
        $this->object->setResponse( $this->response );

        $this->assertEquals( $this->response, $this->object->getResponse( ) );
    }


    /**
     *
     */
    public function testSetInvalidResponse()
    {
        $this->setExpectedException("Exception");
        $this->object->setResponse( $response );
    }



    /**
     *
     */
    public function testGetSetDocuments()
    {
        $documents = array( );

        $document = new Document( array( Helios::ID_FIELD_NAME => 1,
                                                Helios::TYPE_FIELD_NAME => 'some_type') );
        array_push( $documents, $document );

        $document = new Document( array( Helios::ID_FIELD_NAME => 2,
                                                Helios::TYPE_FIELD_NAME => 'some_type') );
        array_push( $documents, $document );



        $this->object->setDocuments( $documents );

        // test iteration
        foreach ( $this->object as $document )
        {
            $this->assertTrue( $document instanceof Document );
        }

        // test countable
        $this->assertEquals( 2, count( $this->object ) );

        $this->assertEquals( $documents, $this->object->getDocuments( ) );
    }

    /**
     *
     */
    public function testGetSetFacetFields()
    {
        $facets = array( new Facet( ) );

        $this->object->setFacetFields( $facets );

        $this->assertEquals( $facets, $this->object->getFacetFields( ) );
    }

    /**
     *
     */
    public function testGetSetFacetQueries()
    {
        $facets = array( new Facet( ) );

        $this->object->setFacetQueries( $facets );

        $this->assertEquals( $facets, $this->object->getFacetQueries( ) );
    }

    /**
     *
     */
    public function testGetSetFacetDates()
    {
        $facets = array( new Facet( ) );

        $this->object->setFacetDates( $facets );

        $this->assertEquals( $facets, $this->object->getFacetDates( ) );
    }

    /**
     *
     */
    public function testArrayAccess()
    {
        $document1 = new Document( array( Helios::ID_FIELD_NAME => 1,
                                                Helios::TYPE_FIELD_NAME => 'some_type') );
        $document2 = new Document( array( Helios::ID_FIELD_NAME => 2,
                                                Helios::TYPE_FIELD_NAME => 'some_type') );
        $document3 = new Document( array( Helios::ID_FIELD_NAME => 3,
                                                Helios::TYPE_FIELD_NAME => 'some_type') );
        $documents = array( $document1, $document2 );

        $this->object->setDocuments( $documents );


        // test offsetGet
        $this->assertEquals( 1, $this->object[ 0 ][ 'id' ] );
        $this->assertEquals( 2, $this->object[ 1 ][ 'id' ] );


        // test offsetSet
        $this->object[ 2 ] = $document3;

        $this->assertEquals( 3, $this->object[ 2 ][ 'id' ] );


        // test offsetExists
        $this->assertTrue( isset( $this->object[ 0 ] ) );


        // test offsetUnset
        unset( $this->object[ 2 ] );

        $this->assertFalse( isset( $this->object[ 2 ] ) );
    }


}