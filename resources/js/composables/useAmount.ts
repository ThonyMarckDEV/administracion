import { InputCategory, InputSupplier } from '@/interface/Inputs';
import { Pagination } from '@/interface/paginacion';
import { AmountRequestCreate, AmountResource, AmountResponseShow } from '@/pages/panel/amount/interface/Amount';
import { AmountServices } from '@/services/amountServices';
import { showSuccessMessage } from '@/utils/message';
import { reactive, ref } from 'vue';

export const useAmount = () => {
    const principal = reactive<{
        amountList: AmountResource[];
        paginacion: Pagination;
        loading: boolean;
        filter: {
            ruc: string;
            date_start: string;
            date_end: string;
        };
        idAmount: number;
        statusModal: {
            update: boolean;
            delete: boolean;
        };
        amountData: AmountResponseShow;
    }>({
        amountList: [],
        paginacion: {
            total: 0,
            current_page: 0,
            per_page: 0,
            last_page: 0,
            from: 0,
            to: 0,
        },
        loading: false,
        filter: {
            ruc: '',
            date_start: '',
            date_end: '',
        },
        idAmount: 0,
        statusModal: {
            update: false,
            delete: false,
        },
        amountData: {
            category_id: 0,
            supplier_id: 0,
            description: '',
            amount: 0,
            date_init: '',
        },
    });

    const suppliers = ref<InputSupplier[]>([]);
    const categories = ref<InputCategory[]>([]);

    const loadingAmounts = async (page: number = 1, ruc: string = '', date_start: string = '', date_end: string = '') => {
        principal.loading = true;
        principal.filter.ruc = ruc;
        principal.filter.date_start = date_start;
        principal.filter.date_end = date_end;
        try {
            const response = await AmountServices.index(page, ruc, date_start, date_end);
            principal.amountList = response.amounts;
            principal.paginacion = response.pagination;
        } catch (error) {
            console.error('Error loading amounts:', error);
        } finally {
            principal.loading = false;
        }
    };

    // get suppliers
    const loadingSuppliers = async (texto: string = '') => {
        try {
            const response = await AmountServices.getSuppliers(texto);
            suppliers.value = response.data;
        } catch (error) {
            console.error('Error loading suppliers:', error);
        }
    };

    // get categories
    const loadingCategories = async (texto: string = '') => {
        try {
            const response = await AmountServices.getCategories(texto);
            categories.value = response.data;
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    };
    //  get show amount
    const loadingShowAmount = async (id: number) => {
        try {
            const response = await AmountServices.show(id);
            principal.amountData = response.amount;
            if (response.status) {
                principal.statusModal.update = true;
                principal.idAmount = id;
            }
        } catch (error) {
            console.error('Error loading amount:', error);
        }
    };
    // create amount
    const createAmount = async (data: AmountRequestCreate) => {
        try {
            await AmountServices.store(data);
        } catch (error) {
            console.error('Error creating amount:', error);
        }
    };
    // delete amount
    const deleteAmount = async (id: number) => {
        try {
            const response = await AmountServices.destroy(id);
            if (response.status) {
                showSuccessMessage(response.message, 'Eliminado correctamente');
                loadingAmounts(principal.paginacion.current_page, principal.filter.ruc, principal.filter.date_start, principal.filter.date_end);
            }
        } catch (error) {
            console.error('Error deleting amount:', error);
        } finally {
            principal.statusModal.delete = false;
        }
    };
    // update amount
    const updateAmount = async (id: number, data: AmountResponseShow) => {
        try {
            const response = await AmountServices.update(id, data);
            if (response.status) {
                showSuccessMessage(response.message, 'Actualizado correctamente');
                loadingAmounts(principal.paginacion.current_page, principal.filter.ruc, principal.filter.date_start, principal.filter.date_end);
            }
        } catch (error) {
            console.error('Error updating amount:', error);
        } finally {
            principal.statusModal.update = false;
        }
    };
    return {
        principal,
        suppliers,
        categories,
        loadingAmounts,
        loadingSuppliers,
        loadingCategories,
        loadingShowAmount,
        createAmount,
        deleteAmount,
        updateAmount,
    };
};
