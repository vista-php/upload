# VISTA PHP Upload Module
## Introduction
Vista PHP Upload module is a part of Vista PHP Framework, but can also be used as a stand alone file manipulation
package for PHP. It is designed to simplify file uploads and file manipulation in PHP.

## Getting Started
### Installation
```bash
composer require vista-php/upload
```

### Usage
All you need to do is instantiate the `Vista\Upload\FileUploader` class and call the `upload` method.
Constructor of the FileUploader class takes one parameter:
- `string $name` - The name of the file input field in the form.

#### Example:
```php
use Vista\Upload\FileUploader;

$uploader = new FileUploader('file');
$uploader->upload();
```

### Configuration
You can configure the allowed mime types, maximum file size, upload directory, allow overwrite, file name, 
file nameprefix, file name suffix, allowed file types and perform manual validation based on the configured criteria.

#### Example: 
```php
use Vista\Upload\FileUploader;

$uploader = new FileUploader('file');
$uploader->setAllowedMimeTypes(['image/jpeg', 'image/png', 'image/gif'])
    ->setMaxFileSize(1024 * 1024); // 1MB
    ->setUploadDirectory(__DIR__ . '/uploads') // It should be a writable directory
    ->setAllowOverwrite(true) // default is false
    ->setFileName('file')
    ->setFileNamePrefix('prefix_')
    ->setFileNameSuffix('_suffix.jpg');

$uploader->upload();
```
If you need manual validation based on defined criteria without actually uploading the file,
you can call:
```php
$validation = $uploader->validate(); // true or false
```

### Deleting Files
You can delete files using the `delete` method. It takes the file path as a parameter.
```php
use Vista\Upload\FileUploader;

$uploader = new FileUploader('');
$success = $uploader->delete(__DIR__ '/path/to/file.jpg'); // returns true or false
```

### Additional Methods
- `getResults` - Returns the upload results.
```
[
    [
        'filename' => 'file.jpg',
        'destination' => '/path/to/uploads/file.jpg',
    ]
]
```
- `getErrors` - Returns the upload errors.
```
[
    'File type not allowed text/plain',
]

```
