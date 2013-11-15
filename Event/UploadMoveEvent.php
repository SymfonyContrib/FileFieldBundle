<?php
/**
 *
 */

namespace SymfonyContrib\Bundle\FileFieldBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadMoveEvent extends Event
{
    /**
     * @var UploadedFile
     */
    protected $file;

    /**
     * @var array
     */
    protected $response;

    /**
     * @var string
     */
    protected $uploadDir;

    /**
     * @var string
     */
    protected $uri;

    public function __construct(UploadedFile $file, $uploadDir, $uri, $response)
    {
        $this->file = $file;
        $this->uploadDir = $uploadDir;
        $this->uri = $uri;
        $this->response = $response;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
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

    /**
     * @param string $uploadDir
     */
    public function setUploadDir($uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    /**
     * @return string
     */
    public function getUploadDir()
    {
        return $this->uploadDir;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }


}
