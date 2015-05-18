<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Extension\Core\ColumnType;

use Rollerworks\Component\Datagrid\Column\AbstractColumnType;
use Rollerworks\Component\Datagrid\Column\CellView;
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author FSi sp. z o.o. <info@fsi.pl>
 */
class ActionType extends AbstractColumnType
{
    /**
     * @var OptionsResolver
     */
    private $actionOptionsResolver;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->actionOptionsResolver = new OptionsResolver();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'action';
    }

    /**
     * {@inheritdoc}
     */
    public function buildCellView(CellView $view, ColumnInterface $column, array $options)
    {
        $return = [];
        $actions = $options['actions'];

        foreach ($actions as $name => $actionOpts) {
            $actionOpts = $this->actionOptionsResolver->resolve((array) $actionOpts);
            $return[$name] = [];

            if (null !== $actionOpts['label']) {
                if (is_object($actionOpts['label'])) {
                    $actionOpts['label'] = $actionOpts['label']($name, $view->value);
                }
            } else {
                $actionOpts['label'] = $name;
            }

            if (is_object($actionOpts['uri_scheme'])) {
                $url = $actionOpts['uri_scheme']($name, $actionOpts['label'], $view->value);
            } else {
                $url = vsprintf($actionOpts['uri_scheme'], $view->value);
            }

            if ($actionOpts['redirect_uri']) {
                if (is_object($actionOpts['redirect_uri'])) {
                    $actionOpts['redirect_uri'] = $actionOpts['redirect_uri'](
                        $name,
                        $actionOpts['label'],
                        $view->value
                    );
                }

                if (false !== strpos($url, '?')) {
                    $url .= '&redirect_uri='.urlencode($actionOpts['redirect_uri']);
                } else {
                    $url .= '?redirect_uri='.urlencode($actionOpts['redirect_uri']);
                }
            }

            $return[$name]['url'] = $url;
            $return[$name]['label'] = $actionOpts['label'];
            $return[$name]['value'] = $view->value;
        }

        $view->value = $return;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['actions']);

        $this->actionOptionsResolver->setDefaults(
            [
                'redirect_uri' => null,
                'label' => null,
            ]
        );

        $this->actionOptionsResolver->setRequired(['uri_scheme']);

        if ($this->actionOptionsResolver instanceof OptionsResolverInterface) {
            $this->actionOptionsResolver->setAllowedTypes(
                [
                    'redirect_uri' => ['string', 'null', 'callable'],
                    'uri_scheme' => ['string', 'callable'],
                    'label' => ['null', 'string', 'callable'],
                ]
            );
        } else {
            $this->actionOptionsResolver->setAllowedTypes('redirect_uri', ['string', 'null', 'callable']);
            $this->actionOptionsResolver->setAllowedTypes('uri_scheme', ['string', 'callable']);
            $this->actionOptionsResolver->setAllowedTypes('label', ['null', 'string', 'callable']);
        }
    }
}
