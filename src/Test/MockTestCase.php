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

namespace Rollerworks\Component\Datagrid\Test;

use PHPUnit\Framework\TestCase;
use Rollerworks\Component\Datagrid\Column\CellView;
use Rollerworks\Component\Datagrid\Column\ColumnInterface;
use Rollerworks\Component\Datagrid\Column\HeaderView;
use Rollerworks\Component\Datagrid\Column\ResolvedColumnTypeInterface;
use Rollerworks\Component\Datagrid\DatagridInterface;
use Rollerworks\Component\Datagrid\DatagridView;
use Rollerworks\Component\Datagrid\Extension\Core\Type\TextType;
use Rollerworks\Component\Datagrid\Util\StringUtil;

abstract class MockTestCase extends TestCase
{
    protected function createColumn(string $name = 'foo', string $type = TextType::class): ColumnInterface
    {
        $resolvedType = $this->createMock(ResolvedColumnTypeInterface::class);
        $resolvedType->expects(self::any())
            ->method('getInnerType')
            ->willReturn(new $type());

        $resolvedType->expects(self::any())
            ->method('getBlockPrefix')
            ->willReturn(StringUtil::fqcnToBlockPrefix($type));

        $column = $this->createMock(ColumnInterface::class);
        $column->expects(self::any())
            ->method('getName')
            ->willReturn($name);

        $column->expects(self::any())
            ->method('getType')
            ->willReturn($resolvedType);

        $column->expects(self::any())
            ->method('createHeaderView')
            ->withAnyParameters()
            ->willReturnCallback(
                function (DatagridView $datagrid) use ($column, $name) {
                    return new HeaderView($column, $datagrid, $name);
                }
            );

        $column->expects(self::any())
            ->method('createCellView')
            ->withAnyParameters()
            ->willReturnCallback(
                function (HeaderView $header, $source, $index) {
                    $view = new CellView($header, $header->datagrid);
                    $view->vars['row'] = $index;
                    $view->value = $source;
                    $view->source = $source;

                    return $view;
                }
            );

        return $column;
    }

    /**
     * @param array  $columns
     * @param array  $source
     * @param string $name
     *
     * @return DatagridInterface
     */
    protected function createDatagrid(array $columns, $source, string $name = 'users'): DatagridInterface
    {
        $datagrid = $this->createMock(DatagridInterface::class);
        $datagrid->expects(self::any())
            ->method('getName')
            ->willReturn($name);

        $datagrid->expects(self::any())
            ->method('getColumns')
            ->willReturn($columns);

        $datagrid->expects(self::any())
            ->method('getData')
            ->willReturn($source);

        return $datagrid;
    }

    protected function createDatagridViewNoInit(DatagridInterface $datagrid, array $vars = []): DatagridView
    {
        $view = new DatagridView($datagrid);
        $view->vars = $vars;

        return $view;
    }

    protected function createDatagridView(DatagridInterface $datagrid, array $vars = []): DatagridView
    {
        $view = new DatagridView($datagrid);
        $view->vars = $vars;

        $view->init($datagrid);

        return $view;
    }
}
