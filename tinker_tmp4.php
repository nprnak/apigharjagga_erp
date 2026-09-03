<?php

use App\Models\Property;
use App\Models\SiteInspection;
use App\Models\User;

$engineer = User::where('email', 'engineer@apigharjagga.com')->first();
$property = Property::find(1);

echo 'hasCompletedSiteInspection before: ' . ($property->hasCompletedSiteInspection() ? 'yes' : 'no') . PHP_EOL;

$inspection = SiteInspection::create(array_merge([
    'property_id' => $property->property_id,
    'inspector_user_id' => $engineer->id,
    'status' => SiteInspection::STATUS_REVIEWED,
    'reviewed_at' => now(),
], SiteInspection::defaultChecklists()));

$property->refresh();
echo 'hasCompletedSiteInspection after: ' . ($property->hasCompletedSiteInspection() ? 'yes' : 'no') . PHP_EOL;
echo "inspection_id={$inspection->inspection_id}" . PHP_EOL;
