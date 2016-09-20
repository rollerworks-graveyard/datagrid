UPGRADE
=======

## Upgrade FROM 0.8 to 0.9

* The `Rollerworks\Component\Datagrid\Column\HeaderView` and `Rollerworks\Component\Datagrid\DatagridView`
  no longer implement `ArrayAccess` interface.
  
**Note:** This is the last version of the Rollerworks Datagrid that supports PHP 5.5. All feature releases, will
require PHP 7.0 at minimum.

## Upgrade FROM 0.7 to 0.8

This version contains some major BC breaking changes and introduces improvements
and clean-ups.

 * Data binding support is removed. Instead you need process any data before setting data
   on the Datagrid.

   All related methods and constants are removed.

 * Support for Symfony 2.3 was dropped, the options-resolver requires at
   minimum Symfony 2.7 now. Symfony 3 is now allowed to be installed, and
   will be used unless any of your composer.json packages restricts this version.

 * Data passed to `Datagrid::setData()` must be an `array` or `Traversable` object,
   pre/post data filtering is no longer supported.

 * The `DatagridEvents` class is removed as no events are dispatched anymore.

 * Data can only be set once on a datagrid, calling `Datagrid::setData()` will throw
   an exception.

 * The `Datagrid` class no longer allows to change the registered columns
   (all must be provided in the constructor).

   It's advised to use `DatagridBuilder` if you need to allow changing the columns.

### Columns

 * The `Rollerworks\Component\Datagrid\Tests\Extension\Core\ColumnType` namespace is
   renamed to `Rollerworks\Component\Datagrid\Tests\Extension\Core\Type`.

 * The `Rollerworks\Component\Datagrid\Tests\Extension\Core\ColumnTypeExtension` namespace is
   renamed to `Rollerworks\Component\Datagrid\Tests\Extension\Core\TypeExtension`.

 * Class `Rollerworks\Component\Datagrid\Column\AbstractColumnTypeExtension` is renamed to
   `Rollerworks\Component\Datagrid\Column\AbstractTypeExtension`.

 * Class `Rollerworks\Component\Datagrid\Column\AbstractColumnType` is renamed to
   `Rollerworks\Component\Datagrid\Column\AbstractType`.

 * Methods of the `Rollerworks\Component\Datagrid\AbstractDatagridExtension` class and
   `Rollerworks\Component\Datagrid\DatagridExtensionInterface` no longer contain the word `Column`.
   For example `getColumnType` becomes to `getType`, `loadColumnTypes` becomes `loadTypes`.

   *The reason behind this is redundancy, there are no other types in the datagrid then column types.*

 * A Column instance is no longer linked to a Datagrid instance.
   You can still use the Datagrid information when building the Header and Cell view of a column.

   This was done to remove the circular dependency between the datagrid and it's columns,
   making it possible to create a Datagrid instance as an immutable object.

 * A Column will no longer dispatch any events. The EventDispatcher requirement is removed.

 * Type names were removed. Instead of referencing types by name, you should reference
   them by their fully-qualified class name (FQCN) instead. With PHP 5.5 or later, you can
   use the "class" constant for that:

   Before:

   ```php
   $datagridBuilder->add('name', 'name', ['label' => 'Name']);
   ```

   After:

   ```php
   use Rollerworks\Component\Datagrid\Extension\Core\Type\TextType;

   $datagridBuilder->add('name', TextType::class, ['label' => 'Name']);
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
   class UserProfileType extends AbstractType
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
   type from `TypeExtensionInterface::getExtendedType()` now.

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
   use Rollerworks\Component\Datagrid\Extension\Core\Type\CoumnType;

   class MyTypeExtension extends AbstractTypeExtension
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
   class MyType extends AbstractType
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
   $column = $datagridBuilder->createColumn('name', new MyType());
   ```

   After:

   ```php
   $column = $datagridBuilder->createColumn('name', MyType::class);
   ```

 * The DataMapper is removed in favor of an easier and faster solution.
   Instead of setting a DataMapper you set a data-provider callable on the column.

   Before:

   ```php
   $registry = ...;
   $dataProvider = ...;
   $datagrid = ...;

   $datagridFactory = new DatagridFactory($registry, $dataProvider);
   $datagridFactory->createColumn('name', TextType::class, $datagrid, ['label' => 'Name', 'field_mapping' => ['name' => 'name']]);
   ```

   After:

   ```php
   $registry = ...;

   $datagridFactory = new DatagridFactory($registry);
   $datagridFactory->createColumn(
       'name',
       TextType::class,
       ['label' => 'Name', 'data_provider' => function ($data) { $data->getName(); }]
   );
   ```

   **Note:** If your column-type doesn't have the `ColumnType` as it's parent
   you need to call `setDataProvider()` in your custom column-type.

   **Tip:**

   > If you don't provide a value for 'data_provider', the `ColumnType` will try to create a data-provider.
   > This only works when the the property-path name equals to the column-name.
   > So when your column is named "name", eg. a public property "name", method named getName()
   > or magic `__get()` method must exist on the row's data-source for this work.
   >
   > ```php
   > $registry = ...;
   >
   > $datagridFactory = new DatagridFactory($registry);
   > $datagridFactory->createColumn('name', TextType::class, ['label' => 'Name']);
   > ```

 * `Rollerworks\Component\Datagrid\Extension\Core\ColumnType\ModelType` is removed.
    Instead you can use the `Rollerworks\Component\Datagrid\Extension\Core\Type\TextTypeType` with multiple
    fields returned by the "data_provider" and the "value_format" option set to a callback.
    And no "glue" set, the "value_format" as callback will receive all the fields allowing to fully customize
    the returned format.

    Or create a custom type to handle the value returned by your data_provider.

 * The following transformer where removed as they are no longer needed:

    * `Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\TrimTransformer`
    * `Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\SingleMappingTransformer`
    * `Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\NestedListTransformer`
    * `Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\ModelToArrayTransformer`

 * The empty value handling of the `TextTransformer` has moved to its own Transformer
   `Rollerworks\Component\Datagrid\Extension\Core\DataTransformer\EmptyValueTransformer`.

 * The 'label' option is now optional, you should "humanize" the column name when no label
   is provided.

   You can use the following snippet to humanize a column name:

   ```php
   $text = 'columnName';
   $label = ucfirst(trim(strtolower(preg_replace(array('/([A-Z])/', '/[_\s]+/'), array('_$1', ' '), $text))));
   ```

### Views

 * All the view interfaces are removed, and only classes are provided.
   All view classes have public properties and can be extended when needed.

 * Rows are now initialized when the DatagridView is created, and not when the row
   gets iterated.

 * Views are no longer aware of the object that created them, meaning it's no longer possible
   to directly get the Datagrid or Column object from a view.

   Instead any information should be set on the view's `vars` property instead.

 * When a DatagridView is created it's no longer possible to change the column order
   or it's cells. Existing rows can be removed but not added or replaced.

 * Updating the `DatagridView` class is changed to use a callable instead of looping
   through event listeners.

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
