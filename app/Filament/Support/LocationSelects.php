<?php

namespace App\Filament\Support;

use App\Models\District;
use App\Models\Municipality;
use App\Models\Province;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

/**
 * Builds a cascading set of location dropdowns:
 *
 *     Province → District → Municipality → Ward
 *
 * Every dropdown is populated live from the database (provinces, districts,
 * municipalities, wards tables) and each level is filtered by the parent
 * selection above it. Options are keyed by their display name (ward by its
 * number) so the selected value persists directly into the existing
 * string columns (province / district / municipality / ward_no), keeping
 * backward compatibility with previously stored free-text addresses.
 *
 * The "tole / locality / landmark" field is intentionally NOT included here:
 * it is free text with no reference table to draw from, so each form keeps
 * its own TextInput for it.
 */
class LocationSelects
{
    /**
     * @param  string  $province      form key for the province dropdown
     * @param  string  $district      form key for the district dropdown
     * @param  string  $municipality  form key for the municipality dropdown
     * @param  string  $ward          form key for the ward dropdown
     * @param  bool    $required      whether the four dropdowns are required
     * @param  array<string, string>  $labels  optional label overrides keyed by
     *                                          'province' | 'district' | 'municipality' | 'ward'
     * @return array<int, Select>
     */
    public static function make(
        string $province = 'province',
        string $district = 'district',
        string $municipality = 'municipality',
        string $ward = 'ward_no',
        bool $required = true,
        array $labels = [],
    ): array {
        return [
            Select::make($province)
                ->label($labels['province'] ?? 'Province (प्रदेश)')
                ->options(fn () => Province::orderBy('name')->pluck('name', 'name'))
                ->searchable()
                ->native(false)
                ->preload()
                ->required($required)
                ->live()
                ->afterStateUpdated(function (Set $set) use ($district, $municipality, $ward): void {
                    $set($district, null);
                    $set($municipality, null);
                    $set($ward, null);
                }),

            Select::make($district)
                ->label($labels['district'] ?? 'District (जिल्ला)')
                ->options(function (Get $get) use ($province) {
                    $selected = Province::where('name', $get($province))->first();

                    return $selected
                        ? $selected->districts()->orderBy('name')->pluck('name', 'name')
                        : [];
                })
                ->searchable()
                ->native(false)
                ->required($required)
                ->live()
                ->afterStateUpdated(function (Set $set) use ($municipality, $ward): void {
                    $set($municipality, null);
                    $set($ward, null);
                }),

            Select::make($municipality)
                ->label($labels['municipality'] ?? 'Municipality / Rural Municipality (गा.पा. / न.पा.)')
                ->options(function (Get $get) use ($province, $district) {
                    $selected = District::query()
                        ->whereHas('province', fn ($q) => $q->where('name', $get($province)))
                        ->where('name', $get($district))
                        ->first();

                    return $selected
                        ? $selected->municipalities()->orderBy('name')->pluck('name', 'name')
                        : [];
                })
                ->searchable()
                ->native(false)
                ->required($required)
                ->live()
                ->afterStateUpdated(fn (Set $set) => $set($ward, null)),

            Select::make($ward)
                ->label($labels['ward'] ?? 'Ward No. (वडा नं.)')
                ->options(function (Get $get) use ($province, $district, $municipality) {
                    $selected = Municipality::query()
                        ->where('name', $get($municipality))
                        ->whereHas('district', function ($q) use ($get, $province, $district): void {
                            $q->where('name', $get($district))
                                ->whereHas('province', fn ($p) => $p->where('name', $get($province)));
                        })
                        ->first();

                    return $selected
                        ? $selected->wards()->orderBy('ward_number')->pluck('ward_number', 'ward_number')
                        : [];
                })
                ->searchable()
                ->native(false)
                ->required($required),
        ];
    }
}
