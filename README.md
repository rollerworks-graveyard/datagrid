RollerworksDatagrid
===================

RollerworksDatagrid provides you with a powerful datagrid system
for your PHP applications.

The system has a modular design and can work with any PHP framework,
user locale, data format or storage system.

Displaying an objects list is one of the most common tasks in web applications
and probably the easiest one, so you could ask how can this component help you?

The Datagrid Component allows you to create one action that handles
displaying all kinds of lists in your application without duplicating code.

**WARNING. This project is still under development, compatibility breaks may occur.**
**Documentation is currently missing or may outdated from time to time.**

> Compatibility breaks will result in a new minor version like 0.x.0.
> And are documented in the CHANGELOG.md

Requirements
------------

You need at least PHP 5.5, and Intl extension for international support.

For framework integration you may use the following;

* Symfony Bundle (available!)
* Symfony DependencyInjection extension (coming soon)
* ZendFramework2 Plugin (coming soon)
* Silex Plugin (coming soon)

Features
--------

The following types are provided out of the box, building yours is also
possible and very straightforward. You can use any type of data
(including nested sets).

**Tip:** All types listed below support localization.

## ColumnTypes

* Action
* Batch (selection only)
* Boolean
* CompoundColumn (nested columns)
* DateTime
* Model (Property access)
* Money
* Number
* Text

Installation
------------

For installing and integrating RollerworksDatagrid, you can find all the
details in the manual.

[Installing](http://rollerworksdatagrid.readthedocs.org/en/latest/installing.html)

Documentation
-------------

[Read the Documentation for master][6]

The documentation for RollerworksDatagrid is written in [reStructuredText][3] and can be built
into standard HTML using [Sphinx][4].

To build the documentation do the following:

1. Install [Spinx][4]
2. Change to the `doc` directory on the command line
3. Run `make html`

This will build the documentation into the `doc/_build/html` directory.

Further information can be found in The Symfony [documentation format][5] article.

> The Sphinx extensions and theme are installed under Git submodules
> and don't need to be downloaded separately.

Versioning
----------

For transparency and insight into the release cycle, and for striving
to maintain backwards compatibility, RollerworksSearch is maintained under
the Semantic Versioning guidelines as much as possible.

Releases will be numbered with the following format:

`<major>.<minor>.<patch>`

And constructed with the following guidelines:

* Breaking backward compatibility bumps the major (and resets the minor and patch) number
* New additions without breaking backwards compatibility bumps the minor (and resets the patch) number
* Bug fixes and misc changes bump the patch number

For more information on SemVer, please visit <http://semver.org/>.

Credits
-------

The column-type extensions are largely inspired on the Symfony Form
component, and contains a fair amount of code originally developed
by the amazing Symfony community.

This package contains code originally provided by FSi sp. z o.o.

License
-------

This package is provided under the none-restrictive MIT license,
you are free to use it for any free or proprietary product/application,
without restrictions.

[LICENSE](LICENSE)

Contributing
------------

This is an open source project. If you'd like to contribute,
please read the [Contributing Code][1] part of Symfony for the basics. If you're submitting
a pull request, please follow the guidelines in the [Submitting a Patch][2] section.

[1]: http://symfony.com/doc/current/contributing/code/index.html
[2]: http://symfony.com/doc/current/contributing/code/patches.html#check-list
[3]: http://docutils.sourceforge.net/rst.html
[4]: http://sphinx-doc.org/
[5]: http://symfony.com/doc/current/contributing/documentation/format.html
