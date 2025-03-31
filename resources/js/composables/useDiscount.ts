import { Pagination } from '@/interface/paginacion';
import { DiscountResource, DiscountRequest, DiscountUpdateRequest } from '@/pages/panel/discount/interface/Discount';
import { DiscountServices } from '@/services/discountServices';
import { showSuccessMessage } from '@/utils/message';
import { reactive } from 'vue';

export const useDiscount = () => {
    const principal = reactive<{
        discountList: DiscountResource[];
        paginacion: Pagination;
        loading: boolean;
        filter: string;
        idDiscount: number;
        stateModal: {
            update: boolean;
            delete: boolean;
        };
        discountData: DiscountResource;
    }>({
        discountList: [],
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
        idDiscount: 0,
        stateModal: {
            update: false,
            delete: false,
        },
        discountData: {
            id: 0,
            description: '',
            percentage: 0,
            state: true,
        },
    });
        //reset discount data
        const resetDiscountData = () => {
            principal.discountData = {
                id: 0,
                description: '',
                percentage: 0,
                state: true,
            };
        };

    // loading discounts
    const loadingDiscounts = async (page: number = 1, description: string = '', status: boolean = true) => {
        if (status) {
            principal.loading = true;
            try {
                const response = await DiscountServices.index(page, description);
                principal.discountList = response.discounts;
                principal.paginacion = response.pagination;
                console.log(response);
            } catch (error) {
                console.error(error);
            } finally {
                principal.loading = false;
            }
        }
    };
            // creating discounta
            const createDiscount = async (data: DiscountRequest) => {
                try {
                    await DiscountServices.store(data);
                } catch (error) {
                    console.error(error);
                }
            };
        // get Discount by id
        const getDiscountById = async (id: number) => {
            try {
                if (id === 0) {
                    principal.discountData = {
                        id: 0,
                        description: '',
                        percentage: 0,
                        state: true,
                    };
                    return;
                }
            const response = await DiscountServices.show(id);
            if (response.state) {
                principal.discountData = response.discount;
                console.log(principal.discountData.description);
                principal.idDiscount = response.discount.id;
                principal.stateModal.update = true;
            }
        } catch (error) {
            console.error(error);
        }
    };
    // update discount
    const updateDiscount = async (id: number, data: DiscountUpdateRequest) => {
        try {
            const response = await DiscountServices.update(id, data);
            if (response.state) {
                showSuccessMessage('Descuento actualizado', 'El descuento se actualizo correctamente');
                principal.stateModal.update = false;
                loadingDiscounts(principal.paginacion.current_page, principal.filter);
            }
        } catch (error) {
            console.error(error);
        }
    };
    // delete discount
    const deleteDiscount = async (id: number) => {
        try {
            const response = await DiscountServices.destroy(id);
            console.log(response.state);
            if (response.state) {
                showSuccessMessage('Descuento eliminado', 'El Descuento se elimino correctamente');
                principal.stateModal.delete = false;
                loadingDiscounts(principal.paginacion.current_page, principal.filter);
            }
        } catch (error) {
            console.error(error);
        } finally {
            principal.stateModal.delete = false;
        }
    };
    return {
        principal,
        loadingDiscounts,
        createDiscount,
        getDiscountById,
        resetDiscountData,
        updateDiscount,
        deleteDiscount,
    };
};
