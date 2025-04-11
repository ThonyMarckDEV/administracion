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

export interface InputSupplierResponse {
    data: InputSupplier[];
}

export interface InputCategoryResponse {
    data: InputCategory[];
}

export interface InputClientTypeResponse {
    data: InputClientType[];
}
