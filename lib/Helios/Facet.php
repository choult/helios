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

class Facet implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var array
     */
    private $tags;


    /**
     *
     */
    public function addTag( Tag $tag )
    {
        $this->tags[ ] = $tag;
    }

    /**
     *
     * @return string
     */
    public function getName( )
    {
        return $this->name;
    }

    /**
     *
     * @param string $name
     */
    public function setName( $name )
    {
        $this->name = $name;
    }

    /**
     *
     * @return string
     */
    public function getAlias( )
    {
        return $this->alias;
    }

    /**
     *
     * @param string $alias
     */
    public function setAlias( $alias )
    {
        $this->alias = $alias;
    }

    /**
     *
     * @return array
     */
    public function getTags( )
    {
        return $this->tags;
    }

    /**
     *
     * @param array $tags
     */
    public function setTags( $tags )
    {
        $this->tags = $tags;
    }


    /**
     *
     * @return array
     */
    public function toArray()
    {
        $ret = array();
        foreach ( $this->tags as $tag )
        {
            $ret[ $tag->getName() ] = $tag->getTally();
        }
        return $ret;
    }



    /**
     *
     * @return ArrayIterator
     */
    public function getIterator( )
    {
        return new ArrayIterator( (array) $this->tags );
    }

    /**
     *
     * @return int
     */
    public function count( )
    {
        if ( !is_array( $this->tags ) ) return 0;

        return count( $this->tags );
    }

    /**
     *
     * @param string $key
     */
    public function offsetGet( $key )
    {
        return $this->tags[ $key ];
    }

    /**
     *
     * @param string $key
     * @param mixed $value
     */
    public function offsetSet( $key, $value )
    {
        $this->tags[ $key ] = $value;
    }

    /**
     *
     * @param string $key
     * @return bool
     */
    public function offsetExists( $key )
    {
        return isset( $this->tags[ $key ] );
    }

    /**
     *
     * @param string $key
     */
    public function offsetUnset( $key )
    {
        unset( $this->tags[ $key ] );
    }

}

