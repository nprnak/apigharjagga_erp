<?php

namespace App\Filament\User\Resources\MyRentalResource\Pages;

use App\Filament\User\Resources\MyRentalResource;
use Filament\Resources\Pages\ListRecords;

class ListMyRentals extends ListRecords
{
    protected static string $resource = MyRentalResource::class;
}
