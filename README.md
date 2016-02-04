# Simple template-based model-view-controller output solution for ProcessWire CMS/CMF

The files in this repository represent the contents of the /site/templates/ directory. As such, this is not really a site profile, and neither does it provide meaningful markup for your site out of the box. Consider this a starting point for building your own sites and applications with ProcessWire using a simplified model-view-controller type architecture.

While the components and terminology you see here are similar to those you would expect to see in a proper MVC application, this project is actually largely based on regular template files: instead of a specialized object, each Controller is just a regular PHP file with access to View and Layout components, which in turn are instances of the TemplateFile class, etc.

## Directory structure

```
.
├── admin.php
├── controllers
│   └── home.php
├── errors
│   └── 500.html
├── index.custom.php
├── index.php
├── lib
│   ├── Functions.php
│   ├── Hooks.php
│   └── ViewPlaceholders.php
├── static
│   ├── css
│   │   └── style.css
│   ├── img
│   ├── js
│   └── misc
└── views
    ├── layouts
    │   └── default.php
    ├── partials
    │   └── menu
    │       └── top.php
    └── scripts
        ├── basic-page
        │   └── index.php
        └── home
            ├── index.php
            └── json.php
```

## Getting started

To get started, you can simply copy the contents of this repository into your /site/templates/ directory and set the value of the Alternate Template Filename setting of any given template to 'index'. This will redirect any requests for pages using that template to our Front Controller component (/site/templates/index.php).

It should be noted that since this solution is based on the Alternate Template Filename setting, you don't have to use it for all of your templates – unless you want to.

## Components

**Front Controller** is a loose interpretation of the [Front Controller pattern](http://martinfowler.com/eaaCatalog/frontController.html), and it's main tasks include setting up the environment and required variables, instantiating View and Layout, and including a) template-specific Controller (if one exists) and b) custom front controller file index.custom.php (if it exists).

If a template specific **Controller** is found, it is included within the Front Controller. A Controller should contain any business logic related to the template – or, at least, any business logic that doesn't belong to a separate module or class. One of the key concepts of MVC is separation of concerns, and one aspect of this is not mixing business logic with output generation.

**View** represents the part of the page that is visible to visitors on the site; markup, if you will. Actual output is generated based on Layouts and/or View Scripts:

* Layouts are "page frames"; template files with placeholder slots for injecting page-specific markup into said frame. You don't actually have to use a layout for any of your templates, but if most of your pages share a general structure (header, footer, perhaps other parts too) a Layout is one way to make sure that you don't  repeat yourself (DRY).

* View Scripts are specific to a given template, and essentially represent the per-page markup. Each template may have more than one View Script, each of which may represent either different sections of the page (masthead.php, aside.php, content.php) or completely different methods of rendering page content (json.php, rss.php).

In the directory structure displayed above 'lib' contains files required by this output solution itself, 'static' contains all static assets of your site (images, stylesheets, JavaScript files, etc.) and 'errors' is the directory where ProcessWire expects templates used for error messages to be placed.

## Other MVC-ish output solutions for ProcessWire

In case you're interested in working with the MVC pattern – or simply looking for a solution that offers separation of concerns for your template files – and this particular project doesn't seem to fulfill your needs, you might want to check out the following options:

* [A Rails-inspired [something]VC boilerplate for new ProcessWire projects](https://github.com/fixate/pw-mvc-boilerplate)
* [Spex, an asset and template management module for ProcessWire](https://github.com/jdart/Spex)
* [Template Data Providers module](https://github.com/marcostoll/processwire-template-data-providers)

## License

This project is licensed under the Mozilla Public License Version 2.0.
