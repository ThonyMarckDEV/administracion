import { InputCategoryResponse, InputSupplierResponse } from '@/interface/Inputs';
import { AmountRequestCreate, AmountResponse, AmountResponseDelete, AmountResponseShow, AmountShow } from '@/pages/panel/amount/interface/Amount';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

export const AmountServices = {
    async index(page: number, ruc: string, date_start: string, date_end: string): Promise<AmountResponse> {
        const response = await axios.get(
            `/panel/listar-amounts?page=${page}&ruc=${encodeURIComponent(ruc)}&date_start=${date_start}&date_end=${date_end}`,
        );
        return response.data;
    },
    async store(data: AmountRequestCreate) {
        router.post(route('panel.amounts.store'), data);
    },
    async show(id: number): Promise<AmountShow> {
        const response = await axios.get(`/panel/amounts/${id}`);
        return response.data;
    },
    async update(id: number, data: AmountResponseShow): Promise<any> {
        const response = await axios.put(`/panel/amounts/${id}`, data);
        return response.data;
    },
    async destroy(id: number): Promise<AmountResponseDelete> {
        const response = await axios.delete(`/panel/amounts/${id}`);
        return response.data;
    },
    async getSuppliers(texto: string): Promise<InputSupplierResponse> {
        return await axios.get(`/panel/inputs/suppliers_list?texto=${encodeURIComponent(texto)}`);
    },
    async getCategories(texto: string): Promise<InputCategoryResponse> {
        return await axios.get(`/panel/inputs/categories_list?texto=${encodeURIComponent(texto)}`);
    },
};
