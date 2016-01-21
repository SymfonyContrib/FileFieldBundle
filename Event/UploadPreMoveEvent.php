<?php
/**
 *
 */

namespace SymfonyContrib\Bundle\FileFieldBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class UploadPreMoveEvent extends Event
{
    /** @var  array */
    protected $files;

    /** @var bool */
    protected $move = true;

    /** @var Request */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
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
        return $this->files ?: $this->request->files->all();
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
