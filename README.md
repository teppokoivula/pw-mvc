**pw-mvc is no longer actively maintained – if you're looking for a MVC type output framework for ProcessWire, check out [wireframe](https://wireframe-framework.com/) instead.**

---

# Simple implementation of the model-view-controller pattern for ProcessWire

The goal of this project is to provide a light-weight model-view-controller implementation for ProcessWire. While it can be adapted to just about any kind of site or application, it is important to understand that this project is not a site profile in itself.

The little HTML that is included is intended to be replaced with your own markup, not used as-is. Consider this a starting point for building your own site or an application, no more and no less.

If you're not familiar with the MVC pattern, there's a ton of stuff written about it all around the web. As long as you understand that MVC is about separation of concerns (typically model is about data, controller contains business logic, and view is the user interface) and that there are many variations and derivates of it floating around, you're pretty far already.

## Effectiveness over semantics

While the components and terminology you see here are similar to those you would expect from a full-fledged MVC application, this project is a relatively loose interpretation of the pattern.

Perhaps the most prominent difference between this project and "pure" MVC implementations is that there's actually no model component here – or, rather, the model layer is ProcessWire itself. Model is resposible for managing data, and so is ProcessWire. Taking that into account, it just didn't seem to make sense to introduce a new layer on top of the existing API.

Controllers, on the other hand, are just regular PHP files that have – through the $view variable – access to a View component. Behind the scenes the View component is an instance of ProcessWire's built-in TemplateFile class.

## Getting started

Getting started with this project is a two-step process:

1. Copy the contents of this repository into your /site/templates/ directory. If you already have identically named files or folders, you can rename the included files to something else, as long as you also adjust the paths in $config->mvc accordingly. See index.php for more details.

2. Set the value of the Alternate Template Filename setting of templates you want to route through this project to 'index'. This will redirect requests for pages using those templates through the front controller component (/site/templates/index.php).

Since this solution is based on the Alternate Template Filename setting, you don't have to use it for all of your templates. If you want to use [other output strategies](https://processwire.com/docs/tutorials/how-to-structure-your-template-files/) for some of your templates, that's perfectly fine. ProcessWire is all about flexibility, after all.

## Directory structure

The default directory structure introduced here is heavily influenced by the [recommended project directory structure for earlier versions of Zend Framework](http://framework.zend.com/manual/1.12/en/project-structure.project.html), and it is an important part of this solution. Each component has it's place in the tree, and each directory is there for a reason:

- /controllers/ contains controller files. Each controller is tied to a template, and completely optional; you can have controllers for all of your templates, some of them, or none of them.
- /views/ contains everything related to the display side of your site: /views/scripts/ contains view scripts, /views/layouts/ contains layouts, and /views/partials/ contains partials.
- /static/ contains static assets, such as CSS, JavaScript, and images. Typically you would create separate subdirectories for different types of files, but that is entirely up to you.
- /lib/ contains the files required by this project itself. If you want to make updating this project easy, leave this directory alone: don't modify, remove, or add anything here.

Here's the entire (default) directory structure:

```
.
├── controllers
│   └── home.php
├── index.custom.php
├── index.php
├── lib
│   ├── Functions.php
│   ├── Hooks.php
│   └── ViewPlaceholders.php
├── static
│   └── css
│       └── style.css
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

**Front Controller** is a loose interpretation of the [Front Controller pattern](http://martinfowler.com/eaaCatalog/frontController.html), and it's main tasks include setting up the environment and required variables, instantiating View and Layout, and including a) template-specific Controller (if one exists) and b) custom files that modify the front controller behaviour (index.before.php, index.custom.php, and index.after.php).

If a template-specific **Controller** file is found, it is included by the Front Controller. A Controller should contain any business logic related to the template – or, at least, any business logic that doesn't belong to a separate module or class. One of the key concepts of MVC is separation of concerns, and one aspect of this is not mixing business logic with output generation.

**View** represents the part of the page that is visible to visitors on the site; markup, if you will. Actual output is constructed from layout, partials, and a view script:

* Layouts are "page frames"; template files with placeholder slots for injecting page-specific markup into said frame. You don't have to use a layout for any of your templates, but if a bunch of pages share an identical structure (header, footer, and perhaps other parts too) a Layout is one way to make sure that you don't repeat yourself (DRY principle).

* Partials are what some refer to as "include files", i.e. files that don't necessarily make much sense alone, but can be included in order to provide a feature that is shared by multiple templates. Partials can be referred to as regular files (`include "partials/menu/top.php"`) or via the `$partials` variable (`include $partials->menu->top`).

* View Scripts are specific to a given template, and represent the per-page markup. There can be more than one view script for each template, each representing either a different section of the page (masthead.php, aside.php, content.php) or different method of rendering the content of the page (default.php, json.php, rss.php).

In the directory structure displayed above, 'lib' contains files required by this output solution itself and 'static' contains all the static assets of your site (images, stylesheets, JavaScript files, etc.)

## Other MVC-ish output strategies

In case you're interested in working with the MVC pattern – or simply looking for a solution that offers separation of concerns for your template files – and this particular project doesn't quite fit your needs, check out the following alternatives:

* [A Rails-inspired [something]VC boilerplate for new ProcessWire projects](https://github.com/fixate/pw-mvc-boilerplate)
* [Spex, an asset and template management module for ProcessWire](https://github.com/jdart/Spex)
* [Template Data Providers module](https://github.com/marcostoll/processwire-template-data-providers)

## License

This project is licensed under the Mozilla Public License Version 2.0.
