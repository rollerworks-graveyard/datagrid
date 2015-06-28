UPGRADE
=======

## Upgrade FROM 0.4 to 0.5

### Field mapping configuration

 * The "field_mapping" option now only accepts an associative array,
   where the key is used to identify a mapping-field, the value holds
   the mapping-path.
 
   Before: `['field_mapping' => ['user.id']]`
   After: `['field_mapping' => ['user_id' => 'user.id', 'id' => 'id']]`
   
 * Column types with multiple fields will receive the data like:
 
   ```php
   // Keys are as configured (shown above)
   $values = [
       'id' => 50,
       'user_id' => 10,
   ];
   ```

### ActionType

The "action" type has been completely rewritten to be more extendible.

 * Option "content" was added as an alternative to the "label" option,
   you can use eg. the "label" option or "content".
   
 * Option "url" was added and allows to configure a complete uri (instead of a pattern).
 
 * Option "uri_scheme" now uses `strtr()` instead of the `sprintf()` pattern
   for formatting an uri.
   
   The replacement values are provided as `{id}` for the `id` mapping key
   (see above for details).
 
 * Instead of configuring multiple actions, you must now use the "compound_column"
   type to combine multiple actions in a cell.
  
   Before:
   
   ```php
   $datagrid->addColumn(
       $this->factory->createColumn(
           'actions',
           'action',
           $datagrid,
           [
               'label' => 'actions',
               'field_mapping' => ['id'],
               'actions' => [
                   'modify' => [
                       'label' => 'Modify',
                       'uri_scheme' => 'entity/%d/modify',
                   ],
                   'delete' => [
                       'label' => 'Delete',
                       'uri_scheme' => 'entity/%d/delete',
                   ],
               ]
           ]
       )
   );
   ```
   
   After:
   
   ```php
   $datagrid->addColumn(
       $this->factory->createColumn(
           'actions',
           'compound_column',
           $datagrid,
           [
               'label' => 'Actions',
               'columns' => [
                   'modify' => $this->factory->createColumn(
                       'modify',
                       'action',
                       $datagrid,
                       [
                           'label' => 'Modify',
                           'field_mapping' => ['id' => 'id'],
                           'uri_scheme' => 'entity/{id}/modify',
                       ]
                   ),
                   'delete' => $this->factory->createColumn(
                       'delete',
                       'action',
                       $datagrid,
                       [
                           'label' => 'Delete',
                           'field_mapping' => ['id' => 'id'],
                           'uri_scheme' => 'entity/{id}/delete',
                       ]
                   ),
               ]
           ]
       )
   );
   ```

## Upgrade FROM 0.3 to 0.4

 * No changes required.
 
## Upgrade FROM 0.2 to 0.3

 * The methods `setVar()`, `getVar()` and `getVars()` were added
   to `Rollerworks\Component\Datagrid\DatagridViewInterface`. If you implemented
   this interface in your own code, you should add these three methods.

## Upgrade FROM 0.1 to 0.2

 * The methods `createDatagridBuilder()` as added
   to `Rollerworks\Component\Datagrid\DatagridFactoryInterface`. If you implemented
   this interface in your own code, you should add this method.
