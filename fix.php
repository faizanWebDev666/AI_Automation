<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$privatePublicPath = storage_path('app/private/public');
$publicPath = storage_path('app/public');

echo "Checking $privatePublicPath...\n";

if (is_dir($privatePublicPath)) {
    $items = scandir($privatePublicPath);
    foreach ($items as $item) {
        if ($item == '.' || $item == '..') continue;
        
        $src = $privatePublicPath . '/' . $item;
        $dst = $publicPath . '/' . $item;
        
        if (!file_exists($dst)) {
            rename($src, $dst);
            echo "Moved $item to public storage.\n";
        } else {
            if (is_dir($src)) {
                $subitems = scandir($src);
                foreach($subitems as $sub) {
                    if ($sub == '.' || $sub == '..') continue;
                    if (!file_exists($dst . '/' . $sub)) {
                        rename($src . '/' . $sub, $dst . '/' . $sub);
                        echo "Merged $sub into $dst.\n";
                    }
                }
            }
        }
    }
}

$removePrefix = function($path) {
    if ($path && str_starts_with($path, 'public/')) {
        return substr($path, 7);
    }
    return $path;
};

// Users
$users = \App\Models\User::all();
foreach($users as $user) {
    $user->cnic_front_image = $removePrefix($user->cnic_front_image);
    $user->cnic_back_image = $removePrefix($user->cnic_back_image);
    $user->live_photo = $removePrefix($user->live_photo);
    $user->selfie_with_cnic = $removePrefix($user->selfie_with_cnic);
    $user->save();
}

// Properties
$props = \App\Models\Property::all();
foreach($props as $prop) {
    $prop->electricity_bill = $removePrefix($prop->electricity_bill);
    $prop->ownership_proof = $removePrefix($prop->ownership_proof);
    $prop->save();
}

// Property Images
$imgs = \App\Models\PropertyImage::all();
foreach($imgs as $img) {
    $img->image_path = $removePrefix($img->image_path);
    $img->save();
}

echo "Database paths updated. Storage issue fixed!\n";
