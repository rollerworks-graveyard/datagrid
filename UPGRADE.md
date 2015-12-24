UPGRADE
=======

## Upgrade FROM 0.6 to 0.7

This version contains BC breaking changes, older versions are no longer supported.

 * Type names were removed. Instead of referencing types by name, you should reference
   them by their fully-qualified class name (FQCN) instead. With PHP 5.5 or later, you can
   use the "class" constant for that:

   Before:

   ```php
   $datagridBuilder->add('name', 'name', ['label' => 'Name');
   ```

   After:

   ```php
   use \Rollerworks\Component\Datagrid\Extension\Core\ColumnType\TextType;

   $datagridBuilder->add('name', TextType::class, ['label' => 'Name');
   ```

   As a further consequence, the method `ColumnTypeInterface::getName()` was
   removed. You should remove this method from your types.

   If you want to customize the block prefix of a type in Twig, you should now
   implement `ColumnTypeInterface::getBlockPrefix()` instead:

   Before:

   ```php
   class UserProfileType extends AbstractColumnType
   {
       public function getName()
       {
           return 'profile';
       }
   }
   ```

   After:

   ```php
   class UserProfileType extends AbstractColumnType
   {
       public function getBlockPrefix()
       {
           return 'profile';
       }
   }
   ```

   If you don't customize `getBlockPrefix()`, it defaults to the class name
   without "Type" suffix in underscore notation (here: "user_profile").

   Type extension should return the fully-qualified class name of the extended
   type from `ColumnTypeExtensionInterface::getExtendedType()` now.

   Before:

   ```php
   class MyTypeExtension extends AbstractColumnTypeExtension
   {
       public function getExtendedType()
       {
           return 'column';
       }
   }
   ```

   After:

   ```php
   use \Rollerworks\Component\Datagrid\Extension\Core\ColumnType\CoumnType;

   class MyTypeExtension extends AbstractColumnTypeExtension
   {
       public function getExtendedType()
       {
           return CoumnType::class;
       }
   }
   ```

 * Returning type instances from `ColumnTypeInterface::getParent()` is not supported anymore.
   Return the fully-qualified class name of the parent type class instead.

   Before:

   ```php
   class MyType extends AbstractColumnType
   {
       public function getParent()
       {
           return new ParentType();
       }
   }
   ```

   After:

   ```php
   class MyType extends AbstractColumnType
   {
       public function getParent()
       {
           return ParentType::class;
       }
   }
   ```

 * Passing type instances to `DatagridBuilder::add()` and the
   `DatagragridFactory::createCoumn()` methods is not supported anymore.
   Pass the fully-qualified class name of the type instead.

   Before:

   ```php
   $column = $datagridBuilder->createColumn(new MyType());
   ```

   After:

   ```php
   $column = $datagridBuilder->createColumn(MyType::class);
   ```

 * Support for Symfony 2.3 was dropped, the options-resolver requires at
   minimum Symfony 2.7 now. Symfony 3 is now allowed to be installed, and
   will be used unless any of your composer.json packages restricts this version.

## Upgrade FROM 0.5 to 0.6

 * The of methods signature of `buildColumn()`, `buildHeaderView()` and `buildCellView()`
   on the `Rollerworks\Component\Datagrid\Column\ColumnTypeExtensionInterface` was changed
   to be consistent with the `Rollerworks\Component\Datagrid\Column\ColumnTypeInterface`.

   Before:

   ```php
   /**
    * @param ColumnInterface $column
    */
   public function buildColumn(ColumnInterface $column);

   /**
    * @param ColumnInterface $column
    * @param HeaderView      $view
    */
   public function buildHeaderView(ColumnInterface $column, HeaderView $view);

   /**
   * @param ColumnInterface $column
   * @param CellView        $view
   */
   public function buildCellView(ColumnInterface $column, CellView $view);
   ```

   After:

   ```php
   /**
    * @param ColumnInterface $column
    * @param array           $options
    */
   public function buildColumn(ColumnInterface $column, array $options);

   /**
    * @param HeaderView      $view
    * @param ColumnInterface $column
    * @param array           $options
    */
   public function buildHeaderView(HeaderView $view, ColumnInterface $column, array $options);

   /**
    * @param CellView        $view
    * @param ColumnInterface $column
    * @param array           $options
    */
   public function buildCellView(CellView $view, ColumnInterface $column, array $options);
   ```

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

The "action" type has been completely rewritten to be more extensible.

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
