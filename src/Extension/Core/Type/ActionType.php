<?php

declare(strict_types=1);

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Extension\Core\Type;

use Rollerworks\Component\Datagrid\Column\AbstractType;
use Rollerworks\Component\Datagrid\Column\CellView;
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Exception\InvalidConfigurationException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ActionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildCellView(CellView $view, ColumnInterface $column, array $options)
    {
        $mappingValues = $view->value;

        if (is_object($options['content'])) {
            $options['content'] = $options['content']($mappingValues);
        }

        $view->attributes['url'] = $this->createUrl($options, $mappingValues);
        $view->attributes['content'] = $options['content'];
        $view->attributes['attr'] = $options['attr'];
        $view->attributes['url_attr'] = $options['url_attr'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'redirect_uri' => null,
                'content' => null,
                'attr' => [],
                'url_attr' => [],
                'url' => null,
                'uri_scheme' => null,

                // Remove requirement for label
                'label' => function (Options $options) {
                    return $options['content'];
                },
            ]
        );

        $resolver->setAllowedTypes('redirect_uri', ['string', 'null', 'callable']);
        $resolver->setAllowedTypes('url', ['string', 'null']);
        $resolver->setAllowedTypes('uri_scheme', ['string', 'callable', 'null']);
        $resolver->setAllowedTypes('content', ['null', 'string', 'callable']);
        $resolver->setAllowedTypes('attr', ['array']);
        $resolver->setAllowedTypes('url_attr', ['array']);
    }

    private static function wrapValues(array $values)
    {
        $return = [];

        foreach ($values as $key => $value) {
            $return['{'.$key.'}'] = $value;
        }

        return $return;
    }

    private function createUrl(array $options, $mappingValues): string
    {
        if (null !== $options['url']) {
            return $options['url'];
        }

        if (null === $options['uri_scheme']) {
            throw new InvalidConfigurationException('Action needs an "url" or "uri_scheme" but none is provided.');
        }

        if (is_object($options['uri_scheme'])) {
            $url = $options['uri_scheme']($mappingValues);
        } else {
            $url = strtr($options['uri_scheme'], $this->wrapValues($mappingValues));
        }

        return $url.$this->getRedirectUrl($options, $url, $mappingValues);
    }

    private function getRedirectUrl(array $options, $url, $mappingValues): string
    {
        if (null === $options['redirect_uri']) {
            return '';
        }

        if (is_object($options['redirect_uri'])) {
            $options['redirect_uri'] = $options['redirect_uri']($mappingValues);
        }

        return (false !== strpos($url, '?') ? '&' : '?').'redirect_uri='.urlencode($options['redirect_uri']);
    }
}
