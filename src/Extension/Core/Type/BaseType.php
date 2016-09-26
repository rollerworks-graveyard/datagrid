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
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Column\HeaderView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyPath;

/**
 * Encapsulates common logic of {@link ColumnType} and {@link ComponendColumnType}.
 *
 * This type does not appear in the column's type inheritance chain and as such
 * cannot be extended (via {@link \Rollerworks\Component\Datagrid\Column\ColumnTypeExtensionInterface}) nor themed.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
abstract class BaseType extends AbstractType
{
    public function buildHeaderView(HeaderView $view, ColumnInterface $column, array $options)
    {
        $view->attributes = array_replace($view->attributes, [
            'label_attr' => $options['label_attr'],
            'header_attr' => $options['header_attr'],
            'cell_attr' => $options['header_attr'],
            'label_translation_domain' => $options['label_translation_domain'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => null,
            'label_attr' => [],
            'header_attr' => [],
            'cell_attr' => [],
            'label_translation_domain' => null,
        ]);

        $resolver->setDefault('parent_column', null);
        $resolver->setDefault('data_provider', null);

        $resolver->setAllowedTypes('label', ['string', 'null']);
        $resolver->setAllowedTypes('label_attr', 'array');
        $resolver->setAllowedTypes('header_attr', 'array');
        $resolver->setAllowedTypes('cell_attr', 'array');

        $resolver->setAllowedTypes('data_provider', ['Closure', 'null', 'string', PropertyPath::class]);
    }

    public function getParent()
    {
        // no-op.
    }
}
