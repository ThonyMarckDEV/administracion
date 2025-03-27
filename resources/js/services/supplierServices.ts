import { SupplierResponse } from "@/pages/panel/supplier/interface/Supplier";
import axios from "axios";


export const SupplierServices = {
    async index(page: number, name: string): Promise<SupplierResponse> {
        const response = await axios.get(`/panel/listar-suppliers?page=${page}&name=${encodeURIComponent(name)}`);
        return response.data;
    },
    async store(data: any): Promise<any> {
        return await axios.post('/suppliers', data);
    },
    async show(id: number): Promise<any> {
        return await axios.get(`/suppliers/${id}`);
    },
    async update(id: number, data: any): Promise<any> {
        return await axios.put(`/suppliers/${id}`, data);
    },
    async destroy(id: number): Promise<any> {
        return await axios.delete(`/suppliers/${id}`);
    }
}