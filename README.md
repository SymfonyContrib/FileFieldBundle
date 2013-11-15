**This code is part of the [SymfonyContrib](http://symfonycontrib.com/) community.**

# Symfony2 FileFieldBundle

Provides an advanced file upload form field.

###Extended by:

* [ImageFieldBundle](https://github.com/SymfonyContrib/ImageFieldBundle)

###Features:

* Highly extensible & customizable.
* Single and Multiple uploads.
* Use custom forms to collect data about the file.
* Events fired before, during, and after upload(s).
* Allows integration of external asset management systems, S3, CDNs, etc.
* Provides file icons.
* Ajax uploads.
* Progress bars.
* Uses [jQuery File Upload](http://blueimp.github.io/jQuery-File-Upload/index.html)
* ...and more.

## Installation

Installation is similar to a standard bundle.
http://symfony.com/doc/current/cookbook/bundles/installation.html

* Add bundle to composer.json: https://packagist.org/packages/symfonycontrib/filefield-bundle
* Add bundle to AppKernel.php:

```php
new SymfonyContrib\Bundle\FileFieldBundle\FileFieldBundle(),
```

* jQuery is required but not provided by this bundle.
* Include JS files (order is important):
    * 'bundles/filefield/js/vendor/jquery.ui.widget.js' (only required if jQuery UI is not installed)
    * 'bundles/filefield/js/jquery.iframe-transport.js'
    * 'bundles/filefield/js/jquery.fileupload.js'
    * 'bundles/filefield/js/jquery.fileupload-process.js'
    * 'bundles/filefield/js/jquery.fileupload-validate.js'
    * 'bundles/filefield/js/jquery.filefield-ui.js'
    * 'bundles/filefield/js/filefield.js'
* Include CSS file
    * 'bundles/filefield/css/jquery.fileupload-ui.css'

## Usage Examples

[More examples]()

**Simple single file upload backed by an array:

```php
$data = [
    'filename' => 'examplefile.txt',
    'mime_type' => 'text/plain',
    'size' => 50,
];

...

$builder->add('file', 'filefield', [
    'upload_dir' => realpath($kernel->getRootDir() . '/../web/files'),
    'uri' => '/files/',
]);
```

**Simple multi-file upload backed by an array of arrays.
```php
$data = [
    [
        'filename' => 'examplefile.txt',
        'mime_type' => 'text/plain',
        'size' => 50,
    ],
    [
        'filename' => 'examplefile2.txt',
        'mime_type' => 'text/plain',
        'size' => 100,
    ],
];

...

$builder->add('files', 'filefield', [
    'upload_dir' => realpath($kernel->getRootDir() . '/../web/files'),
    'uri' => '/files/',
    'multiple' => true,
    'limit' => 2,
    'allow_add' => true,
    'allow_delete' => true,
]);
```

## Architecture

At its core, FileFieldBundle only takes care of uploading the file from the
browser to a server. This means that most of the display portion of the file in
the form is offloaded to some other form that is used as a sub-form very much
like the core collection form field.

However, FileFieldBundle does provide a simple display form type that is used
by default if no other form type is provided. This form type simply
displays the filename(linked to the file), a file icon representing the mime type,
and the file size.

## Field Options

* **multiple:** (boolean) (Default: false) Whether this field allows more than 1 upload.
* **limit:** (int) (Default: 1) Number of files allowed to upload if multiple is set to true.
* **upload_dir:** (string) (Default: '') System path to upload the file to.
* **uri:** (string) (Default: '') The URI the browser will use to access the file.
* **js_options:** (array) (Default: []) Array of options to pass to jQuery File Upload.
* **preview_type:**  (string) (Default: null) Name of preview template to use.
* **type:** (string) (Default: 'filefield_simple') Name of form type to use for display.
* **options:** (array) (Default: []) Array of options to pass to sub-form type.
* **include_filefield_options:** (boolean) (Default: true) Whether to include all of the parent filefield options in the sub-form options.
* **allow_add:** (boolean) (Default: false) Whether to allow adding of files when multiple is set to true.
* **allow_delete:** (boolean) (Default: false) Whether to allow removing of files when multiple is set to true.

## How to Extend, Integrate, and Customize

* [How to create a custom filefield display form type.](https://github.com/SymfonyContrib/FileFieldBundle/wiki/How-to-create-a-custom-file-display-form-type)
* [How to use custom jQuery File Upload options.](https://github.com/SymfonyContrib/FileFieldBundle/wiki/How-to-use-custom-jQuery-File-Upload-options)
* [How to use filefield events](https://github.com/SymfonyContrib/FileFieldBundle/wiki/How-to-use-filefield-events)
* [How to upload directly to an external server.](https://github.com/SymfonyContrib/FileFieldBundle/wiki/How-to-forward-file-from-local-server-to-external-server)
* [How to forward file from local server to external server.](https://github.com/SymfonyContrib/FileFieldBundle/wiki/How-to-upload-directly-to-an-external-server)
* [How to customize filename, path, workflow, etc. during upload.](https://github.com/SymfonyContrib/FileFieldBundle/wiki/How-to-customize-filename,-path,-workflow,-etc.-during-upload)

* [Example: Doctrine entity just storing the filename.](https://github.com/SymfonyContrib/FileFieldBundle/wiki/Example:-Doctrine-entity-just-storing-the-filename)
