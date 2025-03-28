import { UserRequest, UserResponse } from '@/pages/panel/user/interface/User';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

export const UserServices = {
    async index(page: number, name: string): Promise<UserResponse> {
        const response = await axios.get(`/panel/listar-users?page=${page}&name=${encodeURIComponent(name)}`);
        return response.data;
    },
    //inertia
    async store(data: UserRequest) {
        router.post(route('panel.users.store'), data);
    },
    async show(id: number): Promise<any> {
        return await axios.get(`/users/${id}`);
    },
    async update(id: number, data: any): Promise<any> {
        return await axios.put(`/users/${id}`, data);
    },
    async destroy(id: number): Promise<any> {
        return await axios.delete(`/users/${id}`);
    },
};
