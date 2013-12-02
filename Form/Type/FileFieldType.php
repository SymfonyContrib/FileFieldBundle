<?php

namespace SymfonyContrib\Bundle\FileFieldBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;

/**
 * Advanced file form field.
 */
class FileFieldType extends AbstractType
{
    public $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['include_filefield_options']) {
            $options['options']['filefield_options'] = $options;
        }

        if ($options['prototype']) {
            $prototype = $builder->create($options['prototype_name'], $options['type'], array_replace(array(
                'label' => $options['prototype_name'] . 'label__',
            ), $options['options']));
            $builder->setAttribute('prototype', $prototype->getForm());
        }

        if ($options['multiple']) {
            $resizeListener = new ResizeFormListener(
                $options['type'],
                $options['options'],
                $options['allow_add'],
                $options['allow_delete']
            );

            $builder->addEventSubscriber($resizeListener);
        } else {
            $name = $options['type'] === 'filefield_simple' ? 'filename' : $builder->getName();
            $builder->add($name, $options['type'], $options['options']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['multiple']) {
            $view->vars = array_replace($view->vars, array(
                'allow_add' => $options['allow_add'],
                'allow_delete' => $options['allow_delete'],
            ));
        } else {
            // Force limit to 1 for single.
            $options['limit'] = 1;
            // Set fieldname to form fieldname.
            $name = $options['type'] === 'filefield_simple' ? 'filename' : $view->vars['name'];
            $options['js_options']['fieldname'] = $name;
        }

        if ($form->getConfig()->hasAttribute('prototype')) {
            $view->vars['prototype'] = $form->getConfig()->getAttribute('prototype')->createView($view);
        }

        $uri = $options['uri'];
        $uploadDir = $options['upload_dir'];

        // Prepare data-attributes.
        $options['js_options']['maxNumberOfFiles'] = $options['limit'];
        $dataAttr = '';
        foreach ($options['js_options'] as $key => $value) {
            $key = strtolower(preg_replace('/([A-Z])/', '-$1', $key));
            $dataAttr .= ' data-' . $key . '="' . $value . '"';
        }

        $view->vars = array_replace($view->vars, [
            'multiple' => $options['multiple'],
            'limit' => $options['limit'],
            'data_attr' => $dataAttr,
            'uri' => $options['uri'],
            'preview_type' => $options['preview_type'],
        ]);

        $ns = 'filefield/' . $view->vars['id'] . '/';
        $this->session->set($ns . 'uploadDir', $uploadDir);
        $this->session->set($ns . 'uri', $uri);
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($form->getConfig()->hasAttribute('prototype') && $view->vars['prototype']->vars['multipart']) {
            $view->vars['multipart'] = true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        global $kernel;

        $optionsNormalizer = function (Options $options, $value) {
            $value['block_name'] = 'entry';

            return $value;
        };

        $resolver->setDefaults([
            'multiple' => false,
            'limit' => 1,
            'upload_dir' => realpath($kernel->getRootDir() . '/../web/uploads'),
            'uri' => '/uploads/',
            'enable_cors' => false,
            'js_options' => [],
            'preview_type' => null,
            'type' => 'filefield_simple',
            'options' => [],
            'include_filefield_options' => true,
            'allow_add' => false,
            'allow_delete' => false,
            'prototype' => true,
            'prototype_name' => '__name__',
        ]);

        $resolver->setNormalizers(array(
            'options' => $optionsNormalizer,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'filefield';
    }

}
