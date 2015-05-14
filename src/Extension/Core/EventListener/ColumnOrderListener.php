<?php

/*
 * This file is part of the RollerworksDatagrid package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Datagrid\Extension\Core\EventListener;

use Rollerworks\Component\Datagrid\DatagridEvent;
use Rollerworks\Component\Datagrid\DatagridEvents;
use Rollerworks\Component\Datagrid\DatagridView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ColumnOrderListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [DatagridEvents::POST_BUILD_VIEW => ['postBuildView', 128]];
    }

    /**
     * {@inheritdoc}
     */
    public function postBuildView(DatagridEvent $event)
    {
        $view = $event->getData();
        /* @var DatagridView $view */
        $columns = $view->getColumns();

        if (count($columns)) {
            $positive = [];
            $negative = [];
            $neutral = [];

            $indexedColumns = [];
            foreach ($columns as $name => $column) {
                if (isset($column->attributes['display_order'])) {
                    $order = $column->attributes['display_order'];
                    if ($order >= 0) {
                        $positive[$name] = $order;
                    } else {
                        $negative[$name] = $order;
                    }
                    $indexedColumns[$name] = $column;
                } else {
                    $neutral[] = $column;
                }
            }

            asort($positive);
            asort($negative);

            $columns = [];
            foreach ($negative as $name => $order) {
                $columns[] = $indexedColumns[$name];
            }

            $columns = array_merge($columns, $neutral);
            foreach ($positive as $name => $order) {
                $columns[] = $indexedColumns[$name];
            }

            $view->setColumns($columns);
        }
    }
}
