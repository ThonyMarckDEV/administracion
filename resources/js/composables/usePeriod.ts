import { Pagination } from '@/interface/paginacion';
import { PeriodResource, PeriodRequest, PeriodUpdateRequest } from '@/pages/panel/period/interface/Period';
import { PeriodServices } from '@/services/periodServices';
import { showSuccessMessage } from '@/utils/message';
import { reactive } from 'vue';

export const usePeriod = () => {
    const principal = reactive<{
        periodList: PeriodResource[];
        paginacion: Pagination;
        loading: boolean;
        filter: string;
        idPeriod: number;
        stateModal: {
            update: boolean;
            delete: boolean;
        };
        periodData: PeriodResource;
    }>({
        periodList: [],
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
        idPeriod: 0,
        stateModal: {
            update: false,
            delete: false,
        },
        periodData: {
            id: 0,
            name: '',
            description: '',
            state: true,
        },
    });

    //reset period data
    const resetPeriodData = () => {
        principal.periodData = {
            id: 0,
            name: '',
            description: '',
            state: true,
        };
    };

    // loading periods
    const loadingPeriods = async (page: number = 1, name: string = '', status: boolean = true) => {
        if (status) {
            principal.loading = true;
            try {
                const response = await PeriodServices.index(page, name);
                principal.periodList = response.periods;
                principal.paginacion = response.pagination;
            } catch (error) {
                console.log(error);
            } finally {
                principal.loading = false;
            }
        }
    };

    // Creating Periods

    const createPeriod = async (data: PeriodRequest) => {
        try {
            await PeriodServices.store(data);
        } catch (error) {
            console.error(error);
        }
    };

    // Get Period by id
    const getPeriodById = async (id: number) => {
        try {
            if (id === 0){
                principal.periodData = {
                    id: 0,
                    name: '',
                    description: '',
                    state: true,
                };
                return;
            }
            const response = await PeriodServices.show(id);
            if (response.state) {
                principal.periodData = response.period;
                console.log(principal.periodData.name);
                principal.idPeriod = response.period.id;
                principal.stateModal.update = true;
            }
        } catch (error) {
            console.error(error);
        }
    };

    //Update period
    const updatePeriod = async (id: number, data: PeriodUpdateRequest) => {
        try {
            const response = await PeriodServices.update(id, data);
            if (response.state) {
                showSuccessMessage('Periodo actualizado correctamente', 'El periodo se actualizo correctamente');
                principal.stateModal.update = false;
                loadingPeriods(principal.paginacion.current_page, principal.filter, true);
        }
    } catch (error) {
            console.error(error);
        }
    };

    //delete periods
    const deletePeriod = async (id: number) => {
        try {
            const response = await PeriodServices.destroy(id);
            console.log(response.state);
            if (response.state) {
                showSuccessMessage('Periodo eliminado', 'El periodo se elimino correctamente');
                principal.stateModal.delete = false;
                loadingPeriods(principal.paginacion.current_page, principal.filter, true);
            }
        } catch (error) {
            console.error(error);
        } finally {
            principal.stateModal.delete = false;
        }
    };

    return {
        principal,
        loadingPeriods,
        createPeriod,
        getPeriodById,
        resetPeriodData,
        updatePeriod,
        deletePeriod,
    }
}    
