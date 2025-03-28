import { onMounted, reactive } from 'vue';
import { ServiceResource } from '@/pages/panel/service/interface/Service';
import { Pagination } from '@/interface/paginacion';
import { ServiceServices } from '@/services/serviceServices';

export const useService = () => {
    const principal = reactive<{
        serviceList: ServiceResource[];
        paginacion: Pagination;
        loading: boolean;
        filter: string;
        idService: number;
        statusModal: {
            register: boolean;
            update: boolean;
            delete: boolean;
        };
        serviceData: ServiceResource;
    }>({
        serviceList: [],
        paginacion: {
            total: 0,
            current_page: 0,
            per_page: 0,
            last_page: 0,
            from: 0,
            to: 0,
        },
        loading: false,
        filter: '',
        idService: 0,
        statusModal: {
            register: false,
            update: false,
            delete: false,
        },
        serviceData: {
            id: 0,
            name: '',
            cost: 0,
            ini_date: '',
            state: '',
        },
    });

    // loading services
    const loadingServices = async (page: number = 1, name: string = '', status: boolean = true) => {
        if (status) {
            principal.loading = true;
            try {
                const response = await ServiceServices.index(page, name);
                principal.serviceList = response.services;
                principal.paginacion = response.pagination;
                console.log(response);
            } catch (error) {
                console.error(error);
            } finally {
                principal.loading = false;
            }
        }
    };

    onMounted(() => {
        loadingServices();
    });

    return {
        principal,
        loadingServices,
    };
};