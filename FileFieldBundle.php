<?php

namespace SymfonyContrib\Bundle\FileFieldBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FileFieldBundle extends Bundle
{

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $class = $this->getContainerExtensionClass();
            if (class_exists($class)) {
                $extension       = new $class();
                $this->extension = $extension;
            } else {
                $this->extension = false;
            }
        }

        if ($this->extension) {
            return $this->extension;
        }
    }
}
