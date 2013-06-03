<?php

namespace updatesystem;

use updatesystem\Filepack;
use \ZipArchive;

class Updatepack
{

    private $packs = array();

    public function __construct()
    {

    }

    public function addFilePack(Filepack $pack)
    {
        $this->packs[] = $pack;
    }

    /**
     *
     * @return filepack
     */
    private function getPacks()
    {
        return $this->packs;
    }

    public function createUpdatePackage()
    {
        /* @var $file File */
        if( $this->getPacks() )
        {
            $zip = new ZipArchive();
            $ziptemp = tempnam("releases", "nuzip_");
            $zip->open($ziptemp, ZipArchive::CREATE);

            foreach( $this->getPacks() as $pack )
            {
                if( $pack->getFiles() )
                {
                    foreach( $pack->getFiles() as $file )
                    {
                        if( $file->type == "directory" && isset($file->getComparison()->result['missing']) )
                        {
                            $zip->addEmptyDir($file->full_relative);
                        }
                        else
                        {

                            if( file_exists($file->fullpath."/".$file->basename) )
                            {
                                $zip->addFile($file->fullpath."/".$file->basename, $file->full_relative);
                            }
                            else
                            {
                                updatereport::state("Missing file: ".$file->fullpath."/".$file->basename);
                            }
                        }
                    }
                }
            }
            $zip->close();
            $zipname = md5_file($ziptemp);
            copy($ziptemp, "releases/".$zipname.".zip");
            unlink($ziptemp);
        }
    }

}


?>
