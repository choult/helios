<?php

namespace Helios\Test;

use Helios\Helios;
/**
 * Test class for Helios.
 * Generated by PHPUnit on 2010-02-07 at 19:11:55.
 */
class HeliosTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Helios
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        Helios::destroy( );
    }

    /**
     *
     */
    public function testConfigured( )
    {
        $this->assertTrue( Helios::configure( dirname( __FILE__ ) . '/../../config/good.yml' ) );
    }

    /**
     *
     */
    public function testNotConfigured( )
    {
        $this->setExpectedException( "Exception" );
        Helios::config( 'test' );
    }

    /**
     *
     */
    public function testAutoload()
    {
        $this->assertFalse( Helios::autoload( 'FooBarClass' ) );
        $this->assertFalse( Helios::autoload( 'Helios_FooBar' ) );

        $this->assertTrue( class_exists( 'Exception' ) );
    }

    /**
     *
     */
    public function testDestroy( )
    {
        Helios::configure( dirname( __FILE__ ) . '/../../config/good.yml' );

        Helios::destroy( );

        $this->setExpectedException( "Exception" );
        Helios::config( 'test' );
    }
}
