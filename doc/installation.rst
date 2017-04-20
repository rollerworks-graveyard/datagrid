Installation and integration
############################

Installing this library is very easy. Note that there a number of external dependencies,
which are automatically installed when you use `Composer`_ to install this library.

It's possible to download this library and not use Composer, but you will need
to do everything manually and may not get the correct version of other packages.
Composer is the only officially support way of installing this library.

.. tip::

    If you want to integrate RollerworksDatagrid with a framework like Symfony, Zend or
    Silex you'd properly want to checkout the documentation of the integration
    packages instead.

    The examples on this page are for library users, don't use them for framework
    integration! If you want to create an integration layer for a framework have
    a look at :doc:`integration_for_frameworks` for tips and best practices.

Installing with Composer
************************

`Composer`_ is a dependency management library for PHP, which you can use
to download the RollerworksDatagrid system.

Make sure you have `Composer installed`_, and install the package by running:

.. code-block:: terminal

    $ php composer.phar require rollerworks/datagrid

From the directory where your ``composer.json`` file is located.

Now, Composer will automatically download all required files, and install them
for you. After this you can start integrating RollerworksDatagrid with your application.

Integration
***********

Once the library is installed it's time to create your first datagrid.

A datagrid consists of one or more columns and a data-set with rows.

Each column has a name (unique in the datagrid) and a type (used for data transformation
and localized rendering. A column type is configured using options.

  See the :doc:`types/index <types reference section>` for a complete overview
  of all the provided types and there options.

There are some minor notes on the data-set however. Don't worry about your
format/storage, anything that is Iterable (including an array)
can be used to populate the datagrid::

    // --> add these new use statements
    use Rollerworks\Component\Datagrid\Datagrids;
    use Rollerworks\Component\Datagrid\Extension\Core\Type as ColumnType;

    // The $datagridFactory allows to create a new Datagrid with ease
    $datagridFactory = Datagrids::createDatagridFactory();

    // See the API doc of Rollerworks\Component\Datagrid\DatagridBuilderInterface
    // for all methods.
    $datagridBuilder = $datagridFactory->createDatagridBuilder()
        ->add('id', ColumnType\NumberType::class)
        ->add('username', ColumnType\TextType::class)
        ->add('registeredOn', ColumnType\DateTimeType::class)
        ->add('enabled', ColumnType\BooleanType::class, ['true_value' => 'Yes', 'false_value' => 'No'])
    ;

    $datagrid = $datagridBuilder->getDatagrid('users_datagrid');

.. note::

    The datagrid expects all rows to follow the same structure.
    You cannot have one row that has an 'id' property and then another
    row which doesn't have this property.

    Empty values are no problem, but the structure must not change!

Lets break this down.

* The ``$datagridFactory`` hides most of the details you shouldn't have to worry about,
  it's the main entry point of the Datagrid system, use it whenever you need to create
  a new Datagrid builder.

  You only have to create it once.

* ``$datagridBuilder`` holds a :class:`Rollerworks\\Component\\Datagrid\\DatagridBuilderInterface`
  that allows to add/remove and replace columns on a datagrid.

  But once a datagrid is build, it cannot be changed.

* The ``add`` method calls add columns to the datagrid builder.
  You can use ``remove`` to remove a column by its name, and ``get`` to get a 'resolved' column.

* Finally the builder is converted to an actual datagrid, and it's columns are resolved.

  You can still reuse the builder to create _another_ datagrid with the same structure,
  or use ``add``/``remove`` to change the structure (this will not affect the already
  created datagrid).

  Need to recreate the same datagrid elsewhere?
  Use a :doc:`datagrid_configurator <DatagridConfigurator>`.

.. note::

    In the example above, the the datagrid is named 'users_datagrid'.
    Each datagrid per page must have a unique (this is not checked by the library).

    You can use any name you like, but make sure to only use the
    following characters: ``a-z -_ 0-9 . :`` (with spaces).

    Naming a datagrid allows to referencing it in the UI
    and makes unique :doc:`ui_and_themes` possible.

Mapping data to a datagrid
==========================

Now the datagrid is created, it needs a data set for displaying.

A data set can be anything that is Iterable (including an array).
The structure of each row is expected to be the same,
but you can bypass this limitation (it's just not recommended).

To get data to a cell, each column must have a data-provider.
This can be done automatically, but keep the following in mind:

* Automatically configuring the column is done by the
  :class:`Rollerworks\\Component\\Datagrid\\Extension\\Core\Type\\ColumnType`;

* The builder assumes the column equals the name in the structure,
  eg. the row data is an array or an object with a public property or getter.

  Internally this uses the `Symfony PropertyAccess component`_.
  You can also provide a string (with a property-path) or a ``PropertyPath`` object.

* Auto configuration works only for a single field, when the column type
  expects more fields you need to specify the data provider manually.

When auto configuration is not possible (or to costly), you set it by
the ``data_provider`` option (which internally calls ``setDataProvider`` on the column).

The column's data provider can any callable, but usually a closure should be enough::

    use Rollerworks\Component\Datagrid\Extension\Core\Type as ColumnType;

    // ...

    $datagridBuilder = $datagridFactory->createDatagridBuilder()
        ->add('id', ColumnType\NumberType::class, ['data_provider' => 'id']) // uses 'id' property-path as data-provider
        ->add(
            'name',
            ColumnType\TextType::class,
            [
                // Uses a closure to transform the User object to an normalized array.
                'data_provider' => function ($data) {
                    return ['first' => $data->firstName, 'last' => $data->lastName ];
                }
                'format' => '%s, %s', // this sprinft() is applied with the field order as shown above (first, last)
            ]
        )
        ->add('username', ColumnType\TextType::class) // data-provider automatically configured
    ;

.. caution::

    Try to avoid any formatting or type transforming within the data provider.
    The purpose of a data provider is to extract data from a structure (array or object).

To set data on a datagrid simple call ``setData``::

    // ...

    $data = [
        ['id' => 1, 'username' => 'doctor0who', 'registered_on' => 'registeredOn' => new DataTime('1975-03-10 13:12:00'), 'enabled' => true] // row 1
        ['id' => 2, 'username' => 'chunky_lover', 'registered_on' => 'registeredOn' => new DataTime('1995-5-10 13:12:00'), 'enabled' => false] // row 2
    ];

    $datagrid = $datagridBuilder->getDatagrid('users_datagrid');
    $datagrid->setData($data);

That's it, the datagrid is now ready for display.
But, RollerworksData has more features to checkout!

Keep on reading, to find out more.

Compound column
===============

Sometimes displaying a single value is not enough, the ``TextType`` already allows
to combine text values from other fields. But this will not work for numbers or
values of various formats.

For this special type you use a compound column, a compound column combines other
columns into a single column and cell. But each sub-column can still be configured
separately.

.. code-block:: php
    :linenos:

    $datagridBuilder = $datagridFactory->createDatagridBuilder();

    // Create a new CompoundColumn builder
    // first argument is the name of column (action), the second argument provides options for the CompoundColumnType,
    // and the third argument (not shown here) allows to specify another child-type then CompoundColumnType.

    $datagridBuilder->createCompound('actions', ['label' => 'Actions', 'data_provider' => function ($data) { return ['id' => $data->id(); }])
        ->add('edit', ActionType::class, ['url_schema' => '/users/{id}/edit'])
        ->add('delete', ActionType::class, ['url_schema' => '/users/{id}/edit'])
    ->end() // This registers the CompoundColumn at the DatagridBuilder, and return the DatagridBuilder.

.. note::

    Internally the builder creates a :class:`Rollerworks\\Component\\Datagrid\\Column\\CompoundColumn`
    instead of a normal normal column.

    The third argument of ``createCompound`` allows to specify another type then
    ``CompoundColumnType``, but the custom type must be a child of ``CompoundColumnType``
    to ensure proper configuring.

As you may have noticed the ``createCompound`` has an ``data_provider``, but there's
something interesting about this. The ``data_provider`` option for a CompoundColumn
is actually used as the default data-provider for sub-columns.

    In the example above a default data-provider is set for all ActionType columns.
    Saving you some copy-paste work.

Unless a sub-column has a data-provider set, it will use the default (set on the
compound column). Changing the internal data-provider of the CompoundColumnType
is not possible.

By now you should be good to go, and you can render your first datagrid.
Or have a look at the last feature of the Datagrid builder.

Assigning a column to a builder
===============================

Other then creating a new column for a datagrid, you can also assign an existing
column to a datagrid. This useful when you need to create many datagrids with a
common structure::

    $column = $datagridFactory->createColumn('id', ColumnType\NumberType::class, ['data_provider' => 'id']);

    $datagridBuilder = $datagridFactory->createDatagridBuilder();
    $datagridBuilder->set($column);

    // ...

The ``$column`` is added to the datagrid builder, and will be used once the datagrid
is build. And the ``$column`` can be used for other datagrid builders.

.. note::

    When another column with same name already exists on the builder, it will be replaced.

Rendering a datagrid
====================

The RollerworksDatagrid library is not limited to rendering on the web or one one theme,
you can use any output format or theme you want. But you can find more details about that in
another section: :doc:`ui_and_themes`.

Just remember that each datagrid has a :class:`Rollerworks\\Component\\Datagrid\\DatagridView`
which holds all the information for display. You create one with::

    // ...

    $datagrid->setData($data);

    $datagridView = $datagrid->createView();

.. note::

    The data must be set before a datagrid view can be created.

.. _`Composer`: https://getcomposer.org/
.. _`Composer installed`: https://getcomposer.org/download/
.. _`Symfony PropertyAccess component`: http://symfony.com/doc/current/components/property_access.html
