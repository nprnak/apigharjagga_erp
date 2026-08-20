export type Listing = {
    listing_id: number;
    application_no: string;
    purpose: string;
    price: number | string | null;
    negotiable: boolean;
    property_code: string | null;
    property_type: string | null;
    area: string | null;
    covered_area?: string | null;
    no_of_floors?: number | null;
    status?: string | null;
    municipality: string | null;
    district?: string | null;
    province?: string | null;
    photo_url?: string | null;
    photos?: string[];
};

export type ListingDetail = Listing & {
    property_id: number | null;
    legal_verification_status?: string | null;
    remarks?: string | null;
    kitta_no?: string | null;
    map_sheet_no?: string | null;
    ownership_type?: string | null;
    road_access?: string | null;
    road_width?: string | null;
    facing_direction?: string | null;
    year_of_construction?: number | null;
    structure_type?: string | null;
    roof_type?: string | null;
    parking?: string | null;
    water_supply?: string | null;
    electricity?: string | null;
    internet?: string | null;
    drainage?: string | null;
    current_building_condition?: string | null;
    ward_no?: string | null;
    tole_locality?: string | null;
    full_address_text?: string | null;
};

export type CityOption = {
    value: string;
    label: string;
    type: string;
};

export type LocationLink = {
    name: string;
    count: number;
};

export type ProvinceLocations = {
    province: string;
    cities: LocationLink[];
};
