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

    /**
     * @todo Remove in 3.0 or when <2.6 support is not needed.
     *
     * Returns the bundle's container extension class.
     *
     * @return string
     */
    protected function getContainerExtensionClass()
    {
        $basename = preg_replace('/Bundle$/', '', $this->getName());
        
        return $this->getNamespace() . '\\DependencyInjection\\' . $basename . 'Extension';
    }
}
