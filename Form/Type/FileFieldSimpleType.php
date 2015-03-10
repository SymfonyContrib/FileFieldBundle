<?php

namespace SymfonyContrib\Bundle\FileFieldBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use SymfonyContrib\Bundle\FileFieldBundle\Helper\UploadHelper;

/**
 * Advanced file form field.
 */
class FileFieldSimpleType extends AbstractType
{
    public $helper;

    public function __construct(UploadHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        global $kernel;

        $filefield = $options['filefield_options'];
        $data      = $filefield['multiple'] ? $view->vars['value'] : $view->parent->vars['value'];

        $uri  = '';
        $file = [];
        if (!empty($data) && ((is_array($data) && !empty($data['filename'])) || $data instanceof File)) {
            if (is_array($data) && count($data) === 1 && isset($data['filename'])) {
                $data = new File(realpath($kernel->getRootDir() . '/../web') . $data['filename']);
            }

            if (is_array($data) && count($data) >= 3) {
                $name = $data['filename'];
                $mime = $data['mime_type'];
                $size = $data['size'];
            } elseif ($data instanceof File) {
                $name = $data->getFilename();
                $mime = $data->getMimeType();
                $size = $data->getSize();
            } else {
                throw new \Exception('Array or object expected.');
            }
            $uri  = $filefield['uri'] . $name;
            $icon = $this->helper->getFileIcon($mime);
            $file = [
                'name'    => $name,
                'iconUri' => $this->helper->getIconUri() . $icon,
                'size'    => $this->helper->formatSize($size),
                'uri'     => $uri,
            ];
        }

        $vars = [
            'multiple'     => $filefield['multiple'],
            'preview_type' => $filefield['preview_type'],
            'file'         => $file,
            'type'         => $filefield['type'] === 'filefield_simple' ? 'hidden' : $filefield['type'],
            'is_prototype' => ($view->vars['name'] === $filefield['prototype_name']),
            'value'        => $uri,
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

}
