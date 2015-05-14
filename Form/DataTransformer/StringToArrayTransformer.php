<?php

namespace SymfonyContrib\Bundle\FileFieldBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Converts a string to an array.
 */
class StringToArrayTransformer implements DataTransformerInterface
{
    /**
     * Convert a string to an single element array.
     *
     * @param string $value
     *
     * @return array|null
     */
    public function transform($value)
    {
        if (null !== $value && !is_scalar($value)) {
            throw new TransformationFailedException('Expected a scalar.');
        }

        if ('' === $value || null === $value) {
            return null;
        }

        return [$value];
    }

    /**
     * Convert a single element array to a string.
     *
     * @param array $value
     *
     * @return null|string
     */
    public function reverseTransform($value)
    {
        if (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        return empty($value) ? null : (string)array_pop($value);
    }
}
