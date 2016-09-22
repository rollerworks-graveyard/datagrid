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

use Rollerworks\Component\Datagrid\Extension\Core\Type\MoneyType;
use Symfony\Component\Intl\Util\IntlTestHelper;

class MoneyTypeTest extends BaseTypeTest
{
    protected function getTestedType(): string
    {
        return MoneyType::class;
    }

    protected function setUp()
    {
        // we test against different locales, so we need the full
        // implementation
        IntlTestHelper::requireFullIntl($this);

        parent::setUp();
    }

    public function testPassLabelToView()
    {
        $column = $this->factory->createColumn(
            'id',
            $this->getTestedType(),
            [
                'label' => 'My label',
                'data_provider' => function ($data) {
                    return $data->key;
                },
            ]
        );

        $datagrid = $this->factory->createDatagrid('grid', [$column]);

        $object = new \stdClass();
        $object->key = '20.00';

        $datagrid->setData([1 => $object]);

        $view = $datagrid->createView();
        $view = $column->createHeaderView($view);

        $this->assertSame('My label', $view->label);
    }

    public function testEURWithUS()
    {
        \Locale::setDefault('en_US');

        $this->assertCellValueEquals('€1.23', '1.23', ['currency' => 'EUR']);
    }

    public function testEURWithNL()
    {
        \Locale::setDefault('NL');

        $this->assertCellValueEquals('€ 1,23', '1.23', ['currency' => 'EUR']);
    }

    public function testWorksForYen()
    {
        \Locale::setDefault('en_US');

        $this->assertCellValueEquals('¥1', '1.23', ['currency' => 'JPY']);

        \Locale::setDefault('ja');

        $this->assertCellValueEquals('￥1', '1.23', ['currency' => 'JPY']);
    }

    public function testForDifferentCurrencies()
    {
        \Locale::setDefault('de_DE');

        $column = $this->factory->createColumn('price', $this->getTestedType(), ['currency' => 'EUR']);
        $column2 = $this->factory->createColumn('price2', $this->getTestedType(), ['currency' => 'GBP']);

        $datagrid = $this->factory->createDatagrid('grid', [$column, $column2]);

        $object = new \stdClass();
        $object->price = '1.23';
        $object->price2 = '1.23';
        $data = [1 => $object];

        $datagrid->setData($data);
        $datagridView = $datagrid->createView();

        $view = $column->createCellView($datagridView, $data[1], 1);
        $this->assertSame('1,23 €', $view->value);

        $view = $column2->createCellView($datagridView, $data[1], 1);
        $this->assertSame('1,23 £', $view->value);
    }

    public function testDifferentCurrencyByMapping()
    {
        \Locale::setDefault('de_DE');

        $column = $this->factory->createColumn('price', $this->getTestedType(), ['currency' => 'EUR']);
        $column2 = $this->factory->createColumn(
            'price2',
            $this->getTestedType(),
            [
                'data_provider' => function ($value) {
                    return ['currency' => $value->currency, 'amount' => $value->price2];
                },
            ]
        );

        $datagrid = $this->factory->createDatagrid('grid', [$column, $column2]);

        $object = new \stdClass();
        $object->price = '1.23';
        $object->price2 = '1.23';
        $object->currency = null;

        $object2 = new \stdClass();
        $object2->price = '1.23';
        $object2->price2 = '1.23';
        $object2->currency = 'GBP';

        $data = [1 => $object, 2 => $object2];

        $datagrid->setData($data);
        $datagridView = $datagrid->createView();

        $view = $column->createCellView($datagridView, $data[1], 1);
        $this->assertSame('1,23 €', $view->value);

        $view = $column2->createCellView($datagridView, $data[2], 1);
        $this->assertSame('1,23 £', $view->value);
    }

    public function testPrecision()
    {
        // Note changing the precision does not work for nl and en_US
        // I'm unable to find the cause, please provide a fix if you have one - Sebastiaan Stok (@sstok)
        \Locale::setDefault('de_DE');

        $this->assertCellValueEquals('1,2355 €', '1.2355', ['currency' => 'EUR', 'precision' => 4]);
    }
}
