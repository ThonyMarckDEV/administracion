import { showDiscountResponse, DiscountDeleteResponse, DiscountRequest, DiscountResponse } from "@/pages/panel/discount/interface/Discount";
import { router } from '@inertiajs/vue3';
import axios from "axios";


export const DiscountServices = {

    //list discounts
    async index(page: number, description: string): Promise<DiscountResponse> {
        const response = await axios.get(`/panel/listar-discounts?page=${page}&description=${encodeURIComponent(description)}`);
        return response.data;
    },
    //inertia
    async store(data: DiscountRequest)  {
        router.post(route('panel.discounts.store'), data);
    },
    // show discount
    async show(id: number): Promise<showDiscountResponse> {
        const response = await axios.get(`/panel/discounts/${id}`);
        return response.data;
    },
    // update discount
    async update(id: number, data: DiscountRequest): Promise<showDiscountResponse> {
        const response = await axios.put(`/panel/discounts/${id}`, data);
        return response.data;
    },
    // Delete discount
    async destroy(id: number): Promise<DiscountDeleteResponse> {
        const response = await axios.delete(`/panel/discounts/${id}`);
        return response.data;
    },
};
