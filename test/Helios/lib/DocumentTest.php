<?php

namespace Helios\Test;

use     Helios\Helios,
        Helios\HQL,
        Helios\Exception,
        Helios\Document;
/**
 * Test class for Helios_Document.
 * Generated by PHPUnit on 2010-05-09 at 17:32:19.
 */
class DocumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Document
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp( )
    {
        $this->docType = 'test_type';
        $this->docId = 123;

        $fields = array( Helios::TYPE_FIELD_NAME => $this->docType,
                         Helios::ID_FIELD_NAME => $this->docId );

        $this->object = new Document( $fields );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown( )
    {
    }


    public function testConstructLogic( )
    {
        $this->setExpectedException( "Exception" );
        $doc = new Document( );
    }

    public function testConstructLogicWidthDocType()
    {
        $this->setExpectedException( "Exception" );

        $fields = array( Helios::TYPE_FIELD_NAME => $this->docType );
        $doc = new Document( $fields );
    }

    public function testConstructLogicWidthDocId()
    {
        $this->setExpectedException( "Exception" );

        $fields = array( Helios::ID_FIELD_NAME => $this->docId );
        $doc = new Document( $fields );
    }


    /**
     *
     */
    public function testGetUid( )
    {
        $delimiter = Helios::UID_DELIMITER;

        $uid = "{$this->docType}$delimiter{$this->docId}";

        $this->assertEquals( $uid, $this->object->getUid( ) );
    }

    /**
     *
     */
    public function testSetUid( )
    {
        $delimiter = Helios::UID_DELIMITER;

        $type = 'other_type';

        $uid = "$type$delimiter{$this->docId}";

        $this->object->setType( $type );

        $this->assertEquals( $uid, $this->object[ Helios::UID_FIELD_NAME ] );
    }

    /**
     *
     */
    public function testGetType( )
    {
        $this->assertEquals( $this->docType, $this->object->getType( ) );
    }

    /**
     *
     */
    public function testSetType( )
    {
        $type = 'other_type';

        $this->object->setType( $type );

        $this->assertEquals( $type, $this->object->getType( ) );

        $this->assertEquals( $type, $this->object[ Helios::TYPE_FIELD_NAME ] );
    }

    /**
     *
     */
    public function testOffsetGetSet( )
    {
        $field = 'foo';
        $value = 'bar';

        $this->object[ $field ] = $value;

        $this->assertEquals( $value, $this->object[ $field ] );
    }

    /**
     *
     */
    public function testOffsetSetSetsTypeAndUid( )
    {
        $delimiter = Helios::UID_DELIMITER;

        $type = 'foo_bar';

        $uid = "$type$delimiter{$this->docId}";

        $this->object[ Helios::TYPE_FIELD_NAME ] = $type;

        $this->assertEquals( $type, $this->object->getType( ) );
        $this->assertEquals( $uid, $this->object[ Helios::UID_FIELD_NAME ] );
    }

    /**
     *
     */
    public function testOffsetSetSetsIdAndUid( )
    {
        $delimiter = Helios::UID_DELIMITER;

        $id = 345;

        $uid = "{$this->docType}$delimiter$id";

        $this->object[ Helios::ID_FIELD_NAME ] = $id;

        $this->assertEquals( $id, $this->object[ Helios::ID_FIELD_NAME ] );
        $this->assertEquals( $uid, $this->object[ Helios::UID_FIELD_NAME ] );
    }

    /**
     *
     */
    public function testOffsetExists( )
    {
        $field = 'foo';
        $value = 'bar';

        $this->object[ $field ] = $value;

        $this->assertTrue( isset( $this->object[ $field ] ) );
    }

    /**
     *
     */
    public function testOffsetUnset( )
    {
        $field = 'foo';
        $value = 'bar';

        $this->object[ $field ] = $value;

        unset( $this->object[ $field ] );

        $this->assertFalse( isset( $this->object[ $field ] ) );
    }

    /**
     *
     */
    public function testOffsetUnsetCantUnsetDocType( )
    {
        unset( $this->object[ Helios::TYPE_FIELD_NAME ] );

        $this->assertTrue( isset( $this->object[ Helios::TYPE_FIELD_NAME ] ) );
    }

    /**
     *
     */
    public function testToArray( )
    {
        $this->assertEquals( 3, count( $this->object->toArray() ) );

        $fields = array( 'type' => $this->docType, 'id' => $this->docId, 'uid' => "{$this->docType}::{$this->docId}" );
        $this->assertEquals( $fields, $this->object->toArray() );

        // add more fields
        $this->object->name = 'Test name';
        $this->object->annotation = 'Annotation';
        $this->assertEquals( 5, count( $this->object->toArray() ) );

        $fields['name'] = 'Test name';
        $fields['annotation'] = 'Annotation';
        $this->assertEquals( $fields, $this->object->toArray() );
    }

}