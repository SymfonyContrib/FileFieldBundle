<?php

namespace SymfonyContrib\Bundle\FileFieldBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use SymfonyContrib\Bundle\FileFieldBundle\Helper\UploadHelper;

/**
 * Advanced file form field.
 */
class FileFieldSimpleType extends AbstractType
{
    /** @var UploadHelper */
    public $helper;

    /** @var array */
    public $defaultData;

    public function __construct(UploadHelper $helper)
    {
        $this->helper      = $helper;
        $this->defaultData = [
            'name'    => '',
            'iconUri' => '',
            'size'    => '',
            'uri'     => '',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $filefield = $options['filefield_options'];
        $file      = $this->defaultData;
        $data      = $filefield['multiple'] ? $view->vars['value'] : $view->parent->vars['value'];

        if (is_string($data)) {
            $file['uri']  = $data;
            $file['name'] = basename($file['uri']);
        } elseif (is_array($data)) {
            $file['uri']  = isset($data['uri'])  ? $data['uri']  : array_pop($data);
            $file['name'] = isset($data['name']) ? $data['name'] : basename($file['uri']);
        } elseif (is_object($data)) {
            $file = $this->convertObjectToViewArray($data);
        }

        $vars = [
            'multiple'     => $filefield['multiple'],
            'preview_type' => $filefield['preview_type'],
            'file'         => $file,
            'type'         => $filefield['type'] === 'filefield_simple' ? 'hidden' : $filefield['type'],
            'is_prototype' => ($view->vars['name'] === $filefield['prototype_name']),
            'value'        => $file['uri'],
        ];

        $view->vars = array_replace($view->vars, $vars);

        // Set required class.
        if (empty($view->vars['attr'])) {
            $view->vars['attr']['class'] = 'filefield-value';
        } else {
            if (empty($view->vars['attr']['class'])) {
                $view->vars['attr']['class'] = 'filefield-value';
            } else {
                $view->vars['attr']['class'] .= ' filefield-value';
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'filefield_options' => [],
        ]);
    }

    public function getParent()
    {
        return 'hidden';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'filefield_simple';
    }

    public function convertObjectToViewArray($object)
    {
        $file = $this->defaultData;

        if (is_callable([$object, 'getFilename'])) {
            $file['name'] = $object->getFilename();
        }

        if (is_callable([$object, 'getMimeType'])) {
            $file['iconUri'] = $this->helper->getIconUri() . $this->helper->getFileIcon($object->getMimeType());
        }

        if (is_callable([$object, 'getSize'])) {
            $file['size'] = $this->helper->formatSize($object->getSize());
        }

        if (is_callable([$object, 'getUri'])) {
            $file['uri'] = $this->helper->formatSize($object->getUri());
        }

        return $file;
    }
}
