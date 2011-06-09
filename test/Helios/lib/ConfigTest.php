<?php

namespace Helios\Test;

use Helios\Config;
/**
 * Test class for Helios_Config.
 * Generated by PHPUnit on 2010-02-07 at 18:45:05.
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Helios_Config
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Config;
        $this->object->load( dirname( __FILE__ ) . '/../../config/good.yml' );
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
    public function testLoadSuccess( )
    {
        $this->object->load( dirname( __FILE__ ) . '/../../config/good.yml' );
        $this->assertTrue( $this->object->isLoaded( ) );
    }

    /**
     *
     */
    public function testLoadFailure( )
    {
        $this->setExpectedException( 'Exception' );
        $this->object->load( dirname( __FILE__ ) . '/../../config/fake.yml' );
        $this->assertFalse( $this->object->isLoaded( ) );
    }

    /**
     *
     */
    public function testSanitizePass( )
    {
        $this->object->load( dirname( __FILE__ ) . '/../../config/good.yml' );
        $this->assertTrue( $this->object->sanitize( ) );
    }

    /**
     *
     */
    public function testSanitizeFail( )
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     *
     */
    public function testOffsetSet( )
    {
        $this->setExpectedException( 'Exception' );
        $this->object[ 'test' ] = 'not allowed';
    }

    /**
     *
     */
    public function testOffsetUnset( )
    {
        $this->setExpectedException( 'Exception' );
        unset( $this->object[ 'test' ] );
    }

    /**
     *
     */
    public function testOffsetExists( )
    {
        // check real value
        $this->assertTrue( isset( $this->object[ 'test' ] ) );

        // check fake value
        $this->assertFalse( isset( $this->object[ 'foobar' ] ) );
    }

    /**
     *
     */
    public function testOffsetGet( )
    {
        // check real value
        $this->assertEquals( 'initialized', $this->object[ 'test' ] );

        // check fake value
        $this->assertNull( $this->object[ 'foobar' ] );
    }
}
