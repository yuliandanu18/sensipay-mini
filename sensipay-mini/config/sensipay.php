<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sensipay Mini Config
    |--------------------------------------------------------------------------
    |
    | Ini paket mini untuk modul pembayaran Bimbel JET.
    | Nanti bisa di-extend untuk integrasi gateway, reminder WA, dsb.
    |
    */

    'currency' => 'IDR',

    // default route prefix & middleware bisa di-override jika perlu
    'route_prefix' => 'sensipay',
    'route_middleware' => ['web', 'auth'],
];
