# I/O

## Function

### ithOut

    [{ ithOut file="path/to/file" moduleId="optional_module_id" }]

Accessing files in the `out` folder of the shop or given module.
This method will not check if the file exists or not, just provide the url.

#### Parameters

| **Name**     | Type   | Required | Default | Description                                                                                                     |
|--------------|--------|----------|---------|-----------------------------------------------------------------------------------------------------------------|
| **file**     | string | y        | -       | Path to file from out folder. Out folder can be of shop or module, depending if the `moduleId` parameter is set |
| **moduleId** | string | n        | null    | Set the module id to use the path to the module's out folder                                                    |

#### Examples

    [{ ithOut file="img/beautiful.png }]
    > YOUR_DOMAIN/out/YOUR_THEME_ID/img/beautiful.png

    [{ ithOut file="img/beautiful.png" moduleId="your_module" }]
    > YOUR_DOMAIN/modules/YOUR_MODULE_PATH/out/img/beautiful.png 


### ithMedia

    [{ ithMedia file="path/to/file" }]

Accessing media file from oxid's media libary.
This method will not check if the file exists or not, just provide the url.

#### Parameters

| **Name**     | Type   | Required | Default | Description                                                                    |
|--------------|--------|----------|---------|--------------------------------------------------------------------------------|
| **file**     | string | y        | -       | This may be the complete path including the /out section or just the file name |

#### Examples

    [{ ithMedia file="example.jpg" }]
    > YOUR_DOMAIN/out/pictures/ddmedia/example.jpg

    [{ ithMedia file="/out/pictures/ddmedia/example.jpg" }]
    > YOUR_DOMAIN/out/pictures/ddmedia/example.jpg

## Modifer

### ithOut

    [{ $source|ithOut : $moduleId=null }]

Accessing files in the `out` folder of the shop or given module.
This method will not check if the file exists or not, just provide the url.

#### Examples

    'img/beautiful.png'|ithOut
    > YOUR_DOMAIN/out/YOUR_THEME_ID/img/beautiful.png 

    "img/beautiful.png"|ithOut:"your_module" }]
    > YOUR_DOMAIN/modules/YOUR_MODULE_PATH/out/img/beautiful.png


### ithMedia

    [{ $source|ithMedia }]

Accessing media file from oxid's media libary.
This method will not check if the file exists or not, just provide the url.

#### Examples

    "example"|ithMedia
    > YOUR_DOMAIN/out/pictures/ddmedia/example.jpg

    "/out/pictures/ddmedia/example.jpg"|ithMedia
    > YOUR_DOMAIN/out/pictures/ddmedia/example.jpg
