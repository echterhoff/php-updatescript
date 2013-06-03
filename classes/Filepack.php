<?php

namespace updatesystem;

class Filepack
{

    public $name;
    public $items;

    /**
     *
     * @param string $name
     */
    function __construct($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @param array $array
     * @return \filepack
     */
    function setFiles($array)
    {
        $this->items = $array;
        return $this;
    }

    /**
     *
     * @param string $id
     * @return boolean
     */
    function hasFile($id)
    {
        if( isset($this->items[$id]) ) return true;
        return false;
    }

    /**
     *
     * @param string $id
     * @return file
     */
    function getFile($id)
    {
        return $this->items[$id];
    }

    /**
     *
     * @return array
     */
    function getFiles()
    {
        return $this->items;
    }

    /**
     *
     * @return integer
     */
    function count()
    {
        return count($this->items);
    }

}

?>
