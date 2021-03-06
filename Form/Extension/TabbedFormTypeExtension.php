<?php

/*
 * This file is part of the MopaBootstrapBundle.
 *
 * (c) Philipp A. Mohrenweiser <phiamo@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mopa\Bundle\BootstrapBundle\Form\Extension;

use Mopa\Bundle\BootstrapBundle\Form\Type\TabsType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Extension for Adding Tabs to Form type.
 */
class TabbedFormTypeExtension extends AbstractTypeExtension
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var array
     */
    private $options;

    /**
     * Constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param array                $options
     */
    public function __construct(FormFactoryInterface $formFactory, array $options)
    {
        $this->formFactory = $formFactory;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated Remove it when bumping requirements to SF 2.7+
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'tabs_class' => $this->options['class'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['tabbed'] = false;
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if (!$view->vars['tabbed']) {
            return;
        }

        $activeTab = null;
        $tabIndex = 0;
        $foundInvalid = false;
        $tabs = [];

        foreach ($view->children as $child) {
            if (in_array('tab', $child->vars['block_prefixes'])) {
                $child->vars['tab_index'] = $tabIndex;
                $valid = $child->vars['valid'];

                if ((null === $activeTab || !$valid) && !$foundInvalid) {
                    $activeTab = $child;
                    $foundInvalid = !$valid;
                }

                $tabs[$tabIndex] = [
                    'id' => $child->vars['id'],
                    'label' => $child->vars['label'],
                    'icon' => $child->vars['icon'],
                    'active' => false,
                    'disabled' => $child->vars['disabled'],
                    'translation_domain' => $child->vars['translation_domain'],
                ];

                ++$tabIndex;
            }
        }

        $activeTab->vars['tab_active'] = true;
        $tabs[$activeTab->vars['tab_index']]['active'] = true;

        $tabsType = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')
            ? 'Mopa\Bundle\BootstrapBundle\Form\Type\TabsType'
            : new TabsType() // SF <2.8 BC
        ;
        $tabsForm = $this->formFactory->create($tabsType, null, [
            'tabs' => $tabs,
            'attr' => [
                'class' => $options['tabs_class'],
            ],
        ]);

        $view->vars['tabs'] = $tabs;
        $view->vars['tabbed'] = true;
        $view->vars['tabsView'] = $tabsForm->createView();
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')
            ? FormType::class
            : 'form' // SF <2.8 BC
        ;
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes()
    {
        return [
            FormType::class,
        ];
    }
}
