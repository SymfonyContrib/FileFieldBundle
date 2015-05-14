<?php

namespace SymfonyContrib\Bundle\FileFieldBundle\File;

use Symfony\Component\HttpFoundation\File\File;

/**
 *
 */
class WebFile extends File
{
    public $uri;

    public function __construct($path, $uri, $checkPath = true)
    {
        $this->uri = $uri;

        parent::__construct($path, $checkPath);
    }

    public function __toString()
    {
        return $this->getUri();
    }

    public function getUri()
    {
        return $this->uri;
    }
}
