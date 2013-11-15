<?php
/**
 *
 */

namespace SymfonyContrib\Bundle\FileFieldBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class UploadPostMoveEvent extends Event
{
    /**
     * @var array
     */
    protected $files;

    /**
     * @var array
     */
    protected $response;

    public function __construct(array $files, array $response)
    {
        $this->files = $files;
        $this->response = $response;
    }

    /**
     * @param mixed $files
     */
    public function setFiles(array $files)
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

    /**
     * @param array $response
     */
    public function setResponse(array $response)
    {
        $this->response = $response;
    }

    /**
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

}
