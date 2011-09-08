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

class Document extends \Apache_Solr_Document implements \ArrayAccess
{
    /**
     * container for facets
     * @var array
     */
    private $facets;

    /**
     *
     * @param array $fields
     */
    public function __construct( array $fields = array( ) )
    {
        // init properties
        $this->clear( );

        if ( !isset( $fields[ Helios::ID_FIELD_NAME ] ) )
            throw new Exception( 'Document id not found' );

        if ( !isset( $fields[ Helios::TYPE_FIELD_NAME ] ) )
            throw new Exception( 'Document type not found' );

        $this->_fields = $fields;

        if ( !isset( $fields[ Helios::UID_FIELD_NAME ] ) )
            $this->setUid( );
    }

    /**
     * clear properties
     */
    public function clear( )
    {
        $this->facets = array( );
    }

    /**
     * Retrieves document type
     *
     * @return string
     */
    public function getUid( )
    {
        return $this->_fields[ Helios::UID_FIELD_NAME ];
    }

    /**
     * Sets document type
     *
     * @param string $type
     * @todo improve name formatting
     */
    private function setUid( )
    {
        $delimiter = Helios::UID_DELIMITER;

        $type = $this->_fields[ Helios::TYPE_FIELD_NAME ];
        $id = $this->_fields[ Helios::ID_FIELD_NAME ];

        $this->setField( Helios::UID_FIELD_NAME, "$type$delimiter$id" );
    }

    /**
     * Retrieves document type
     *
     * @return string
     */
    public function getType( )
    {
        return $this->_fields[ Helios::TYPE_FIELD_NAME ];
    }

    /**
     * Sets document type
     *
     * @param string $type
     * @todo improve name formatting
     */
    public function setType( $type )
    {
        $type = strtolower( $type );
        $type = preg_replace( '/[^A-Z0-9]/i', '_', $type );

        $this->setField( Helios::TYPE_FIELD_NAME, $type );

        $this->setUid( );
    }

    /**
     *
     * @param string $key
     */
    public function offsetGet( $key )
    {
        return $this->_fields[ $key ];
    }

    /**
     *
     * @param string $key
     * @param mixed $value
     */
    public function offsetSet( $key, $value )
    {
        if ( $key == Helios::TYPE_FIELD_NAME )
        {
            $this->setType( $value );
        }
        else
        {
            $this->setField( $key, $value );
        }

        // recalculate the uid if the id change
        if ( $key == Helios::ID_FIELD_NAME )
            $this->setUid( );
    }

    /**
     *
     * @param string $key
     * @return bool
     */
    public function offsetExists( $key )
    {
        return isset( $this->_fields[ $key ] );
    }

    /**
     *
     * @param string $key
     */
    public function offsetUnset( $key )
    {
        if ( $key == Helios::TYPE_FIELD_NAME ) return false;

        unset( $this->_fields[ $key ] );
    }

    /**
     * Return fields as Array key => value pair
     * @return array
     */
    public function toArray()
    {
        $document = array();
        
        foreach( $this->_fields as $key => $value )
        {
            $document[ $key ] = $value;
        }

        return $document;
    }

}
