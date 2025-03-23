<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'billers_media' => [
            'driver' => 'local',
            'root' => public_path('images/biller'),
            'url' => env('APP_URL') . '/images/biller',
            'visibility' => 'public',
        ],

        'brands' => [
            'driver' => 'local',
            'root' => public_path('images/brand'),
            'url' => env('APP_URL') . '/images/brand',
            'visibility' => 'public',
        ],

        'adjustment_doc' => [
            'driver' => 'local',
            'root' => public_path('documents/adjustment'),
            'url' => env('APP_URL') . '/documents/adjustment',
            'visibility' => 'public',
        ],//employees

        'employees' => [
            'driver' => 'local',
            'root' => public_path('images/employee'),
            'url' => env('APP_URL') . '/images/employee',
            'visibility' => 'public',
        ],

        'returns' => [
            'driver' => 'local',
            'root' => public_path('documents/sale_return'),
            'url' => env('APP_URL') . '/documents/sale_return',
            'visibility' => 'public',
        ],

        'Quotation' => [
            'driver' => 'local',
            'root' => public_path('documents/quotation'),
            'url' => env('APP_URL') . '/documents/quotation',
            'visibility' => 'public',
        ],
        'stock_count_csv' => [
            'driver' => 'local',
            'root' => public_path('stock_count'),
            'url' => env('APP_URL') . '/stock_count',
            'visibility' => 'public',
        ],//transfers
        'transfers' => [
            'driver' => 'local',
            'root'   => public_path('documents/transfer'),
            'url'    => env('APP_URL').'/documents/transfer',
            'visibility' => 'public',
        ],

        'supplier_media' => [
            'driver' => 'local',
            'root'   => public_path('images/supplier'),
            'url'    => env('APP_URL').'/images/supplier',
            'visibility' => 'public',
        ],
        'category_images' => [
            'driver' => 'local',
            'root'   => public_path('images/category'),
            'url'    => env('APP_URL').'/images/category',
            'visibility' => 'public',
        ],
        //purchase_documents
        'purchase_documents' => [
            'driver' => 'local',
            'root'   => public_path('documents/purchase'),
            'url'    => env('APP_URL').'/documents/purchase',
            'visibility' => 'public',
        ],

        'category_icons' => [
            'driver' => 'local',
            'root'   => public_path('images/category/icons'),
            'url'    => env('APP_URL').'/images/category/icons',
            'visibility' => 'public',
        ],

        'product_images' => [
            'driver' => 'local',
            'root'   => public_path('images/product'),
            'url'    => env('APP_URL').'/images/product',
            'visibility' => 'public',
        ],

        'product_files' => [
            'driver' => 'local',
            'root'   => public_path('public/product/files'),
            'url'    => env('APP_URL').'/public/product/files',
            'visibility' => 'public',
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
