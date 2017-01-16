RollerworksDatagrid
===================

RollerworksDatagrid provides a powerful datagrid system for your PHP applications.

Displaying an objects list is one of the most common tasks in web applications
and probably the easiest one. So how can this library help you?

The Datagrid system makes the styling and transforming of your data more uniform
and easier to use. Secondly the system can take care of specific (styling) issues
like sorting and paginating. All without duplicating code or degrading performance.

## Features

RollerworksDatagrid provides you with all features needed, including:

* An Advanced column type system for uniform data transformation and styling
  in your datagrids.
* Auto mapping of data to the datagrid.
* Support for any data source (PHP array or any object implementing `\Traversable`).
* (Optional, and coming soon) search/filter using [RollerworksSearch].
* (Optional, and coming soon) Integrated Paginating using [Pagerfanta](https://github.com/whiteoctober/Pagerfanta)

> **Note:** Passing a `Pagerfanta` object as data source _does already work_
> due to the `IteratorAggregate` implementation. The integration bridge however 
> will make the rendering more uniform for datagrids.

## Framework integration

RollerworksDatagrid can be used with any Framework of your choice, but for the best
possible experience use the provided framework integration plug-ins.

* [Symfony Bundle](https://github.com/rollerworks/datagrid-bundle)
* ZendFramework2 Plugin (coming soon)
* Silex Plugin (coming soon)

Your favorite framework not listed? No problem, see the [Contributing Guidelines]
on how you can help!

## Installation and usage

*Please ignore the instructions below if your use a framework integration.*
[Read the Documentation for master] for complete instructions and information. 

Install the RollerworksDatagrid "core" library using [Composer]:

```bash
$ composer install rollerworks/datagrid
```

And create the `DatagridFactory` to get started:

```php
use Rollerworks\Component\Datagrid\Datagrids;
use Rollerworks\Component\Datagrid\Extension\Core\Type as ColumnType;

$datagridFactory = Datagrids::createDatagridFactory();

$datagrid = $datagridFactory->createDatagridBuilder()
   ->add('id', ColumnType\NumberType::class)
   ->add('username', ColumnType\TextType::class)
   ->add('registered_on', ColumnType\DateTimeType::class)
   ->add('enabled', ColumnType\BooleanType::class, ['true_value' => 'Yes', 'false_value' => 'No'])
   ->getDatagrid('users_datagrid')
;

// Now set the data for the grid, this cannot be changed afterwards.
$datagrid->setData([
    ['id' => 1, 'username' => 'sstok', 'registered_on' => new \DateTime('2017-01-12 14:26:00 CET'), 'enabled' => true], 
    ['id' => 1, 'username' => 'doctorw', 'registered_on' => new \DateTime('1980-04-12 09:26:00 CET'), 'enabled' => false], 
    // etc...
]);

// Almost done, the datagrid needs to be rendered, see bellow.
```

### Rendering

The core package however doesn't provide an implementation for this, 
you are free to use any compatible template engine you wish.
 
This example uses the [TwigRendererEngine](https://github.com/rollerworks/datagrid-twig) 
(which needs to be installed separately).

```php
use Rollerworks\Component\Datagrid\Twig\Extension\DatagridExtension;
use Rollerworks\Component\Datagrid\Twig\Renderer\TwigRenderer;
use Rollerworks\Component\Datagrid\Twig\Renderer\TwigRendererEngine;

// Provide the path to the base theme.
$loader = new \Twig_Loader_Filesystem([...]);

$environment = new \Twig_Environment($loader);
$environment->addExtension(new DatagridExtension());
$environment->addRuntimeLoader(new \Twig_FactoryRuntimeLoader([TwigRenderer::class => function () uses ($environment) {
    // The second argument are filenames of datagrid themes.
    $rendererEngine = new TwigRendererEngine($environment, ['datagrid.html.twig']);
    
    return new TwigRenderer($rendererEngine);
}]));

$environment->render('my_page.html.twig', ['datagrid' => $datagrid->createView()]);
```

And in the `my_page.html.twig` twig Template simple use:

```jinja
{{ rollerworks_datagrid(datagrid) }}
```

That's it! Your datagrid is now rendered, but not only that! Whenever you use an
advanced technique like search you only need this much code in your template.

## Resources

* [Read the Documentation for master]
* RollerworksDatagrid is maintained under the [Semantic Versioning guidelines](http://semver.org/)

## Who is behind RollerworksDatagrid?

RollerworksDatagrid is brought to you by [Sebastiaan Stok](https://github.com/sstok).

## License

RollerworksDatagrid is released under the [MIT license](LICENSE).

The types and extensions are largely inspired on the Symfony Form Component, 
and contain a big amount of code from the Symfony project.

## Support

[Join the chat] or use the issue tracker if your question is to complex for quick support.

> **Note:** RollerworksDatagrid doesn't have a support forum at the moment, if you know
> a good free service let us know by opening an issue :+1:

## Contributing

This is an open source project. If you'd like to contribute,
please read the [Contributing Guidelines]. If you're submitting
a pull request, please follow the guidelines in the [Submitting a Patch] section.

[RollerworksSearch]: https://github.com/rollerworks/search
[Join the chat at https://gitter.im/rollerworks/datagrid](https://gitter.im/rollerworks/datagrid?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[Composer]: https://getcomposer.org/doc/00-intro.md
[Contributing Guidelines]: https://github.com/rollerworks/contributing
[Submitting a Patch]: https://contributing.readthedocs.org/en/latest/code/patches.html
[Read the Documentation for master]: http://rollerworksdatagrid.readthedocs.org/en/latest/
[Join the chat]: https://gitter.im/rollerworks/datagrid
