import { Pagination } from '@/interface/paginacion';
import { SupplierResource } from '@/pages/panel/supplier/interface/Supplier';
import { SupplierServices } from '@/services/supplierServices';
import { onMounted, reactive } from 'vue';

export const useSupplier = () => {
    const principal = reactive<{
        supplierList: SupplierResource[];
        paginacion: Pagination;
        loading: boolean;
        filter: string;
        idSupplier: number;
        statusModal: {
            register: boolean;
            update: boolean;
            delete: boolean;
        };
        supplierData: SupplierResource;
    }>({
        supplierList: [],
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
        idSupplier: 0,
        statusModal: {
            register: false,
            update: false,
            delete: false,
        },
        supplierData: {
            id: 0,
            name: '',
            ruc: '',
            address: '',
            state: true,
        },
    });

    // loading suppliers
    const loadingSuppliers = async (page: number = 1, name: string = '', status: boolean = true) => {
        if (status) {
            principal.loading = true;
            try {
                const response = await SupplierServices.index(page, name);
                principal.supplierList = response.suppliers;
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
        loadingSuppliers();
    });

    return {
        principal,
        loadingSuppliers,
    };
};