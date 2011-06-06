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
 * @author Rhodri Davies
 * @version 0.1
 *
 */

class Helios_Value extends Exception
{

    /**
     * $q->where( 'foo = ?', new Helios_Value( 'tree' )->boost( 3 ) );
     *
     * $q->where( 'foo = ?', new Helios_Value( 'tree' )->fuzz( 1.2 ) );
     *
     */

    private $value;

    private $boost;

    private $fuzz;

    /**
     * 
     */
    public function __construct( $value )
    {
        $this->value = $value;
    }

    /**
     *
     * @return string
     */
    public function  __toString()
    {
        return $this->toString( );
    }

    /**
     *
     * @return string
     */
    public function toString( )
    {
        return "{$this->value}{$this->boost}{$this->fuzz}";
    }

    /**
     *
     * @see http://lucene.apache.org/java/2_4_0/queryparsersyntax.html#Boosting%20a%20Term
     * @param integer $boost
     */
    public function boost( $boost )
    {
        if ( $boost < 1 ) throw new Helios_Exception( 'Boost must be greater than 1' );

        $this->boost = "^$boost";
    }

    /**
     *
     * @see http://lucene.apache.org/java/2_4_0/queryparsersyntax.html#Fuzzy%20Searches
     * @param float $fuzz
     */
    public function fuzz( $fuzz = null )
    {
        // check for correct value
        if ( $fuzz !== null && ( $fuzz < 0 || $fuzz > 1 ) ) throw new Helios_Exception( 'Fuzz must between 0 and 1' );

        if ( $fuzz == null ) $fuzz = '';

        $this->fuzz = "~$fuzz";
    }

}