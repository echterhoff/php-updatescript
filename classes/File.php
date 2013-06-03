<?php

namespace updatesystem;

use updatesystem\Filecompare;

class File
{

    public $basename;
    public $id;
    public $type;
    public $filetype;
    public $name;
    public $fullpath;
    public $path;
    public $mtime;
    public $md5;
    public $size;
    public $is_readonly;
    public $is_writable;
    public $full_relative;
    public $comparison;

    public function fromArray($filedata)
    {
        if( $filedata ) foreach( $filedata as $key => $value )
            {
                $this->$key = $value;
            }
    }

    public function fromActualFile($file, $root = "")
    {
        $data = pathinfo($file);
        $this->name = $data["filename"];
        $this->basename = $data["basename"];
        if( !is_file($file) )
        {
            $this->type = "directory";
            $this->filetype = "";
            $this->fullpath = $data["dirname"];
            $this->path = substr($data["dirname"], strlen($root."/"));
            $this->md5 = "";
            $this->mtime = 0;
            $this->size = 0;
            $this->is_readonly = !is_writable($file);
            $this->is_writable = is_writable($file);
            $this->id = md5($this->md5.$this->name.$this->path);
        }
        else
        {
            $this->type = "file";
            $this->filetype = $data["extension"];
            $this->fullpath = $data["dirname"];
            $this->path = substr($data["dirname"], strlen($root."/"));
            $this->md5 = md5_file($file);
            $this->mtime = filemtime($file);
            $this->size = filesize($file);
            $this->is_readonly = !is_writable($file);
            $this->is_writable = is_writable($file);
            $this->id = md5($this->md5.$this->name.$this->path);
        }
        $this->full_relative = ($this->path ? $this->path."/" : '').$this->basename;
    }

    public function isDirectory()
    {
        if( $this->type === "directory" ) return true;
        return false;
    }

    public function getId()
    {
        return $this->full_relative;
    }

    /**
     *
     * @return filecompare
     */
    public function getComparison()
    {
        return $this->comparison;
    }

    public function pathMatchesAny($array)
    {
        if( $array ) foreach( $array as $trytomatch )
            {
                if( !$this->isDirectory() && strpos($this->full_relative, $trytomatch) === 0 )
                {
                    return true;
                }
            }
        return false;
    }

    public function fileMatchesAny($array)
    {
        if( $array ) foreach( $array as $trytomatch )
            {
                if( !$this->isDirectory() && preg_match("#".$trytomatch."#", $this->basename) !== 0 )
                {
                    return true;
                }
            }
        return false;
    }

    public function filetypeMatchesAny($array)
    {
        if( $array ) foreach( $array as $trytomatch )
            {
                if( !$this->isDirectory() && strtolower($this->filetype) === strtolower($trytomatch) )
                {
                    return true;
                }
            }
        return false;
    }

    public function compareTo(file $object)
    {
        $this->comparison = new Filecompare($this, $object);
    }

    public function setMissing()
    {
        $this->comparison = new Filecompare($this);
    }

    public function properties()
    {
        return array(
            'basename' => $this->basename,
            'type' => $this->type,
            'filetype' => $this->filetype,
            'name' => $this->name,
            'path' => $this->path,
            'mtime' => $this->mtime,
            'md5' => $this->md5,
            'size' => $this->size,
            'is_readonly' => $this->is_readonly,
            'is_writable' => $this->is_writable,
            'full_relative' => $this->full_relative
        );
    }

    public function property($key)
    {
        $props = $this->properties();
        return $props[$key];
    }

}

?>