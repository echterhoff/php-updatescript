<?php

namespace updatesystem;

class Config
{

    private $config;

    public function __construct($basedir,$data)
    {
        $this->basedir = $basedir;
        $this->config = $data;
//        $this->printConfig();
    }

    private function printConfig()
    {
        echo "<pre>";
        print_r($this->config);
        echo "</pre>";
    }

    public function hasValidRoot()
    {
        $path = realpath($this->basedir."/".$this->config->systemroot);
        if( file_exists($path) && is_dir($path) )
        {
            $this->config->systemroot = $path;
            return true;
        }
        return false;
    }

    public function getTargetUpdater()
    {
        return $this->config->targetsystem->updater;
    }

    public function getRoot()
    {
        return $this->config->systemroot;
    }

    public function excludeFiletypes()
    {
        return $this->config->exclude->filetypes;
    }

    public function excludeFiles()
    {
        return $this->config->exclude->files;
    }

    public function excludeDirectorys()
    {
        return $this->config->exclude->directorys;
    }

}

?>
