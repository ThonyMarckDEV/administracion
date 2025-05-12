export interface InputClientType {
    id: number;
    name: string;
}

export interface InputCategory {
    id: number;
    name: string;
}

export interface InputSupplier {
    id: number;
    name: string;
    ruc: string;
}

export interface InputService {
    id: number;
    name: string;
}

export interface InputPeriod {
    id: number;
    name: string;
}
// export for autocomplete

export interface InputCustomer {
    id: number;
    name: string;
    ruc: string;
}

export interface InputCustomerResponse {
    data: InputCustomer[];
}

export interface InputSupplierResponse {
    data: InputSupplier[];
}

export interface InputCategoryResponse {
    data: InputCategory[];
}

export interface InputClientTypeResponse {
    data: InputClientType[];
}

export interface InputServiceResponse {
    data: InputService[];
}

export interface InputPeriodResponse {
    data: InputPeriod[];
}
