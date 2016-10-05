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

namespace Rollerworks\Component\Datagrid\Tests\Extension\Core\Type;

use Rollerworks\Component\Datagrid\Datagrid;
use Rollerworks\Component\Datagrid\Extension\Core\Type\DateTimeType;
use Symfony\Component\Intl\Util\IntlTestHelper;

class DateTimeTypeTest extends BaseTypeTest
{
    public function testPassLabelAndOtherToView()
    {
        $column = $this->factory->createColumn(
            'id',
            $this->getTestedType(),
            [
                'label' => 'My label',
                'label_attr' => ['class' => 'info'],
                'header_attr' => ['class' => 'striped'],
                'cell_attr' => ['class' => 'striped'],
                'label_translation_domain' => 'messages',
                'data_provider' => function ($data) {
                    return $data->key;
                },
            ]
        );

        $datagrid = new Datagrid('my_grid', [$column]);

        $object = new \stdClass();
        $object->key = new \DateTime();

        $datagrid->setData([1 => $object]);

        $view = $datagrid->createView();
        $view = $column->createHeaderView($view);

        $this->assertSame('My label', $view->label);
        self::assertViewVarsEquals(
            [
                'label_attr' => [
                    'class' => 'info',
                ],
                'header_attr' => [
                    'class' => 'striped',
                ],
                'cell_attr' => [
                    'class' => 'striped',
                ],
                'label_translation_domain' => 'messages',
                'unique_block_prefix' => '_my_grid_id',
                'block_prefixes' => ['column', 'date_time', '_my_grid_id'],
            ],
            $view
        );
    }

    protected function getTestedType(): string
    {
        return DateTimeType::class;
    }

    protected function setUp()
    {
        // we test against different locales, so we need the full
        // implementation
        IntlTestHelper::requireFullIntl($this);

        parent::setUp();
    }

    // TODO complete tests, transformers work so tests should be kept small
}
