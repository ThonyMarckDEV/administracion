import { ServiceResponse } from "@/pages/panel/service/interface/Service";
import axios from "axios";

export const ServiceServices = {
    async index(page: number, name: string): Promise<ServiceResponse> {
        const response = await axios.get(`/panel/listar-services?page=${page}&name=${encodeURIComponent(name)}`);
        return response.data;
    },
    async store(data: any): Promise<any> {
        return await axios.post('/services', data);
    },
    async show(id: number): Promise<any> {
        return await axios.get(`/services/${id}`);
    },
    async update(id: number, data: any): Promise<any> {
        return await axios.put(`/services/${id}`, data);
    },
    async destroy(id: number): Promise<any> {
        return await axios.delete(`/services/${id}`);
    }
};