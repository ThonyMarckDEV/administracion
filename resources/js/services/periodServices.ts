import {
    PeriodDeleteResponse,
    PeriodRequest,
    PeriodResponse,
    showPeriodResponse,
} from '@/pages/panel/period/interface/Period';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

export const PeriodServices = {
    //List periods
    async index(page: number, name: string): Promise<PeriodResponse> {
        const response = await axios.get(`/panel/listar-periods?page=${page}&name=${encodeURIComponent(name)}`);
        return response.data;
    },

    //Inertia
    async store(data: PeriodRequest) {
        router.post(route('panel.periods.store'), data);
    },

    //Show period
    async show(id: number): Promise<showPeriodResponse> {
        const response = await axios.get(`/panel/periods/${id}`);
        return response.data;
    },

    //Update period
    async update(id: number, data: PeriodRequest): Promise<showPeriodResponse> {
        const response = await axios.put(`/panel/periods/${id}`, data);
        return response.data;
    },

    //Delete period
    async destroy(id: number): Promise<PeriodDeleteResponse> {
        const response = await axios.delete(`/panel/periods/${id}`);
        return response.data;
    },

}