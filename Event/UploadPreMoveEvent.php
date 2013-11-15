<?php
/**
 *
 */

namespace SymfonyContrib\Bundle\FileFieldBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class UploadPreMoveEvent extends Event
{
    protected $files;
    protected $move = true;

    public function __construct($files)
    {
        $this->files = $files;
    }

    /**
     * @param mixed $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    public function preventDefaultMove($value = true)
    {
        $this->move = !(bool)$value;
    }

    public function isDefaultMoveAllowed()
    {
        return $this->move;
    }

}
