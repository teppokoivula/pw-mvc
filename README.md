# Simple and mostly template based implementation of the model-view-controller pattern for ProcessWire

The files in this repository represent the contents of the /site/templates/ directory. As such, this is not really a site profile, and neither does it provide meaningful markup for your site out of the box. Consider this a starting point for building your own site or an application with ProcessWire using a flexible model-view-controller type architecture.

If you're not familiar with the MVC pattern, there's a ton of stuff written about it all around the web. As long as you understand that MVC is about separation of concerns (typically model is about data, controller contains business logic, and view is what the user sees) and that there are many variations and derivates of it floating around, you're pretty far already.

## Effectiveness over semantics

While the components and terminology you see here are similar to those you would expect to see in a full-fledged MVC application, this project is actually a relatively loose interpretation of the original pattern, largely based on template files. Each Controller is just a regular PHP file with access to component called the View, which in turn is an instance of the TemplateFile class.

Perhaps the most prominent difference between this project and a "pure" MVC implementation is that there's  no model component here – or, rather, the model layer is ProcessWire itself. Model is resposible for managing data, and so is ProcessWire. Taking that into account, it just didn't seem to make sense to introduce a new layer on top of the existing API.

## Getting started

To get started, you can simply copy the contents of this repository into your /site/templates/ directory and set the value of the Alternate Template Filename setting of any given template to 'index'. This will redirect requests for pages using that template to our front controller component (/site/templates/index.php).

It should probably be noted that since this solution is based on the Alternate Template Filename setting, you don't have to use it for all of your templates. If you want to use [other output strategies](https://processwire.com/docs/tutorials/how-to-structure-your-template-files/) for some of your templates, go for it. ProcessWire is all about flexibility, and I don't want to tie your hands here either.

## Directory structure

The directory structure introduced here is heavily influenced by the [recommended project directory structure for earlier versions of Zend Framework](http://framework.zend.com/manual/1.12/en/project-structure.project.html), and it is an important part of this solution. Each component has it's place in the tree, and each directory is there for a reason:

- /controllers/ contains controller files. Each controller is tied to a template, yet optional; you can have controllers for all of your templates, but also for none of them, depending on what makes most sense.
- /errors/ is a directory native to ProcessWire, and contains a 500.html file used in case of a fatal error.
- /lib/ is intended for the files required by the MVC output strategy. This directory shouldn't be touched, as the contents may be altered by upcoming versions of this output strategy.
- /static/ contains static assets, such as images and JavaScript files. Typically you would create separate subdirectories for different types of files, but in the end that is entirely up to you.
- /views/ contains everything related to the display side of your site: /views/scripts/ contains view scripts, /views/layouts/ contains layouts, and /views/partials/ contains partials.

Here's the entire directory structure:

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
        │   └── default.php
        └── home
            ├── default.php
            └── json.php
```

## Components

**Front Controller** is a loose interpretation of the [Front Controller pattern](http://martinfowler.com/eaaCatalog/frontController.html), and it's main tasks include setting up the environment and required variables, instantiating View and Layout, and including a) template-specific Controller (if one exists) and b) custom front controller file index.custom.php (if it exists).

If a template specific **Controller** is found, it is included within the Front Controller. A Controller should contain any business logic related to the template – or, at least, any business logic that doesn't belong to a separate module or class. One of the key concepts of MVC is separation of concerns, and one aspect of this is not mixing business logic with output generation.

**View** represents the part of the page that is visible to visitors on the site; markup, if you will. Actual output is generated based on layouts, partials, and/or view scripts:

* Layouts are "page frames"; template files with placeholder slots for injecting page-specific markup into said frame. You don't actually have to use a layout for any of your templates, but if most of your pages share a general structure (header, footer, perhaps other parts too) a Layout is one way to make sure that you don't  repeat yourself (DRY).

* Partials are what you might also refer to as "include files", i.e. files that don't make sense alone, but can be pulled into other files in order to provide features shared by multiple templates, etc. Partials can be referred to as regular files (`include "partials/menu/top.php"`) or via the `$partials` variable (`include $partials->menu->top`).

* View scripts are specific to a given template, and essentially represent the per-page markup. Each template may have more than one view script, each of which may represent either different sections of the page (masthead.php, aside.php, content.php) or completely different methods of rendering page content (json.php, rss.php).

In the directory structure displayed above 'lib' contains files required by this output solution itself, 'static' contains all static assets of your site (images, stylesheets, JavaScript files, etc.) and 'errors' is the directory where ProcessWire expects templates used for error messages to be placed.

## Other MVC-ish output strategies

In case you're interested in working with the MVC pattern – or simply looking for a solution that offers separation of concerns for your template files – and this particular project doesn't seem to fulfill your needs, you might want to check out the following options:

* [A Rails-inspired [something]VC boilerplate for new ProcessWire projects](https://github.com/fixate/pw-mvc-boilerplate)
* [Spex, an asset and template management module for ProcessWire](https://github.com/jdart/Spex)
* [Template Data Providers module](https://github.com/marcostoll/processwire-template-data-providers)

## License

This project is licensed under the Mozilla Public License Version 2.0.
