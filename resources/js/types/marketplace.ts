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
