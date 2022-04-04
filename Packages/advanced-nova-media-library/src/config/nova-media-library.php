<?php
$url = str_finish(config('app.url', config('nova.url', env('APP_URL', '/'))), '/');

return [
    'enable-existing-media' => true,
    'per_page' => 24,
    'thumbnails' => [
        'pdf' => $url . 'images/pdf.png',
        'doc' => $url . 'images/doc.png',
        'docx' => $url . 'images/doc.png',
    ],
];
