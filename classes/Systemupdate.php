<?php

namespace updatesystem;

use \Exception;

require("Config.php");
require("File.php");
require("Filecompare.php");
require("Filepack.php");
require("Report.php");
require("Updatepack.php");

class Systemupdate
{

    private $config;
    private $start_directory;
    private $packs = array();

    public function __construct($startdir)
    {
        $this->start_directory = $startdir;
    }

    private function getBasedir()
    {
        return $this->start_directory;
    }

    public function loadConfigData()
    {
        $setupdata = json_decode(str_replace("\\", "\\\\", file_get_contents("filedata.json")));
        if( json_last_error() )
        {
            throw new Exception(json_last_error());
        }
        $this->config = new Config($this->getBasedir(), $setupdata);
        Report::state("Config loaded!");
        $this->preChecks();
    }

    private function preChecks()
    {
        Report::state("Run pre-checks!");

        Report::state("Is Basepath valid?");
        Report::boolAnswer($this->config->hasValidRoot());
    }

    public function getFullFileList()
    {
        $files = $this->loadAllFiles();
        $files = $this->formFiles($files);
        return $files;
    }

    private function newPack($name, $array)
    {
        $this->packs[$name] = new Filepack($name);
        $this->packs[$name]->setFiles($array);
        return $this;
    }

    /**
     *
     * @param string $name
     * @return Filepack
     */
    private function getPack($name)
    {
        return $this->packs[$name];
    }

    public function scanFiles()
    {
        $files = $this->loadAllFiles();

        $this->newPack("this", $this->formFiles($files));

        $this->newPack("target", $this->getTargetFileData());

//        echo "<pre>";
//        print_r($this->getPack("this")->count());
//        print_r($this->getPack("target")->count());
        $differences = $this->getDifference();
        print_r($differences->getFiles());

        $pack = new Updatepack();
        $pack->addFilePack($differences);
        $pack->createUpdatePackage();
        echo "</pre>";
    }

    private function getTargetFileData()
    {
        $url = $this->getConfig()->getTargetUpdater()->getfilelist;
        Report::state("Query: ".$url);
        $targetfiles = json_decode(file_get_contents($url), true);
        if( !$targetfiles )
        {
            Report::state("Error fetching data from target: ".$url);
            exit;
        }
        foreach( $targetfiles as $key => $filedata )
        {
            $files[$key] = new file();
            $files[$key]->fromArray($filedata);
        }
        return $files;
    }

    private function formFiles($files)
    {
        foreach( $files as $id => $file )
        {
            $fileobject = new File();
            $fileobject->fromActualFile($file, $this->getConfig()->getRoot());

            if( !$this->exclude($fileobject) )
            {
                $clean_files[$fileobject->getId()] = $fileobject;
            }
        }
        $files = array();
        $files = $clean_files;
        return $files;
    }

    private function exclude(File $file)
    {
        if( $file->pathMatchesAny($this->getConfig()->excludeDirectorys()) )
        {
            return true;
        }
        if( $file->fileMatchesAny($this->getConfig()->excludeFiletypes()) )
        {
            return true;
        }
        if( $file->fileMatchesAny($this->getConfig()->excludeFiles()) )
        {
            return true;
        }
        return false;
    }

    /**
     *
     * @return Config
     */
    private function getConfig()
    {
        return $this->config;
    }

    public function loadAllFiles()
    {

        $items = glob($this->getConfig()->getRoot().'/*');


        for( $i = 0; $i < count($items); $i++ )
        {
            if( is_dir($items[$i]) )
            {
                $add = glob($items[$i].'/*');
                $items = array_merge($items, $add);
            }
        }

        return $items;
    }

    /**
     *
     * @return Filepack
     */
    private function getDifference()
    {
        /* @var $source File */
        /* @var $target File */
        foreach( $this->getPack("this")->getFiles() as $id => $source )
        {
            if( $this->getPack("target")->hasFile($id) )
            {
                $target = $this->getPack("target")->getFile($id);
                $source->compareTo($target);
            }
            else
            {
                $source->setMissing();
            }

            if( $source->getComparison()->hasDifferences() )
            {
                $differences[$source->getId()] = $source;
            }
        }

        $this->newPack("differences", $differences);

        return $this->getPack("differences");
    }

}

?>
