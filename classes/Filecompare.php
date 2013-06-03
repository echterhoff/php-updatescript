<?php

namespace updatesystem;

use updatesystem\File as File;

class Filecompare
{

    public $result = array();

    function __construct(File $source, File $target = null)
    {

        if( !isset($target) )
        {
            $this->result['missing'] = true;
            return;
        }
        $this->setSource($source);
        $this->setTarget($target);
        $this->compare();
        $this->clean();
    }

    private function clean()
    {
        $this->source = null;
        unset($this->source);
        $this->target = null;
        unset($this->target);
    }

    private function setSource(File $source)
    {
        $this->source = $source;
    }

    private function setTarget(File $target)
    {
        $this->target = $target;
    }

    /**
     *
     * @return file
     */
    private function getSource()
    {
        return $this->source;
    }

    /**
     *
     * @return file
     */
    private function getTarget()
    {
        return $this->target;
    }

    public function hasDifferences()
    {
        if( count($this->result) ) return true;
        return false;
    }

    public function getDifferences()
    {
        return $this->result;
    }

    private function compare()
    {
        foreach( $this->getSource()->properties() as $name => $source )
        {
            $target = $this->getTarget()->property($name);

            switch (gettype($source))
            {
                case "float":
                case "integer":
                    if( $source == $target )
                    {
//                        $this->result[$name] = "=";
                    }
                    elseif( $source < $target )
                    {
                        $this->result[$name] = "<";
                    }
                    elseif( $source > $target )
                    {
                        $this->result[$name] = ">";
                    }
                    break;
                case "string":
                default:
                    if( $source === $target )
                    {
//                        $this->result[$name] = "=";
                    }
                    else
                    {
                        $this->result[$name] = "!";
                    }
                    break;
            }
        }
    }

}

?>
