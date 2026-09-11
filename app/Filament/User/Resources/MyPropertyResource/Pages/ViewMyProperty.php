<?php

namespace App\Filament\User\Resources\MyPropertyResource\Pages;

use App\Filament\User\Resources\MyPropertyResource;
use App\Models\Property;
use Filament\Resources\Pages\ViewRecord;

/**
 * A single, continuous, printable Annex-A record — the same content-plan
 * pattern used for the KYC summary page (masthead, seal, numbered
 * sections, submission strip, signatures), kept in parity with this
 * resource's own PDF export. Unlike KYC (one record per user, so the page
 * toggles between an editable wizard and a locked summary), a property
 * owner has many records, so this is simply the resource's normal "view"
 * page with a fully custom view — editing happens on the separate Edit
 * page, gated by MyPropertyResource::canEdit().
 */
class ViewMyProperty extends ViewRecord
{
    protected static string $resource = MyPropertyResource::class;

    protected string $view = 'filament.user.pages.my-property-record';

    public function getRecord(): Property
    {
        /** @var Property $record */
        $record = $this->record->loadMissing(['address', 'photos', 'documents.docType', 'features', 'listings.assignedOfficer', 'owner', 'user.kycVerification']);

        return $record;
    }
}
