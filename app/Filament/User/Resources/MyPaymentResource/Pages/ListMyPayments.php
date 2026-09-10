<?php

namespace App\Filament\User\Resources\MyPaymentResource\Pages;

use App\Filament\User\Resources\MyPaymentResource;
use Filament\Resources\Pages\ListRecords;

class ListMyPayments extends ListRecords
{
    protected static string $resource = MyPaymentResource::class;
}
