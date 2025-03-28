// services/serviceServices
import { ServiceResponse } from "@/pages/panel/service/interface/Service"
import axios from "axios"

const URL = '/panel';

export const ServiceServices = {
    async index(page: number, name: string): Promise<ServiceResponse> {
        const response = await axios.get(`${URL}/listar-services?page=${page}&name=${encodeURIComponent(name)}`);
        return response.data;
    },
    async store(data: any): Promise<any> {
        return await axios.post(`${URL}/services`, data);
    },
    async show(id: number): Promise<any> {
        return await axios.get(`${URL}/services/${id}`);
    },
    async update(id: number, data: any): Promise<any> {
        return await axios.put(`${URL}/services/${id}`, data);
    },
    async destroy(id: number): Promise<any> {
        return await axios.delete(`${URL}/services/${id}`);
    }
}