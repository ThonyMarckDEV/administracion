import { Pagination } from '@/interface/paginacion';
import { ClientTypeResource, ClientTypeRequest, ClientTypeUpdateRequest } from '@/pages/panel/clientType/interface/ClientType';
import { ClientTypeServices } from '@/services/clientTypeService';
import { showSuccessMessage } from '@/utils/message';
import { reactive } from 'vue';

export const useClientType = () => {
    const principal = reactive<{
        clientTypeList: ClientTypeResource[];
        paginacion: Pagination;
        loading: boolean;
        filter: string;
        idClientType: number;
        stateModal: {
            update: boolean;
            delete: boolean;
        };
        clientTypeData: ClientTypeResource;
    }>({
        clientTypeList: [],
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
        idClientType: 0,
        stateModal: {
            update: false,
            delete: false,
        },
        clientTypeData: {
            id: 0,
            name: '',
            created_at: '',
            state: true,
        },
    });

    // reset clientTypes data
    const resetClientTypeData = () => {
        principal.clientTypeData = {
            id: 0,
            name: '',
            created_at: '',
            state: true,
        }
    }
    // loading clientTypes
    const loadingClientTypes = async (page: number = 1, name: string = '', status: boolean = true) => {
        if (status) {
            principal.loading = true;
            try {
                const response = await ClientTypeServices.index(page, name);
                principal.clientTypeList = response.clientTypes;
                principal.paginacion = response.pagination;
                console.log(response);
            } catch (error) {
                console.error(error);
            } finally {
                principal.loading = false;
            }
        }
    };

    // creating clientType
    const createClientType = async (data: ClientTypeRequest) => {
        try {
            await ClientTypeServices.store(data);
        } catch (error) {
            console.error(error);
        }
    };

    // get ClientType by id
    const getClientTypeById = async (id: number) => {
        try {
            if (id === 0) {
                principal.clientTypeData = {
                    id: 0,
                    name: '',
                    created_at: '',
                    state: true,
                };
                return;
            }
        const response = await ClientTypeServices.show(id);
            if (response.state) {
                principal.clientTypeData = response.clientType;
                console.log(principal.clientTypeData.name);
                principal.idClientType = response.clientType.id;
                principal.stateModal.update = true;
            }
        } catch (error) {
            console.error(error);
        }
    };

    //update clientType
    const updateClientType = async (id: number, data: ClientTypeUpdateRequest) => {
        try{
            const response = await ClientTypeServices.update(id, data);
            if (response.state) {
                showSuccessMessage('Tipo de cliente actualizado', 'El tipo de cliente se actualizo correctamente');
                principal.stateModal.update = false;
                loadingClientTypes(principal.paginacion.current_page, principal.filter);
            }
        } catch (error) {
            console.error(error);
        }
    };

    //delete clientType
    const deleteClientType = async (id: number) => {
        try {
            const response = await ClientTypeServices.destroy(id);
            console.log(response.state);
            if (response.state) {
                showSuccessMessage('Tipo de cliente eliminado', 'El tipo de cliente se elimino correctamente');
                principal.stateModal.delete = false;
                loadingClientTypes(principal.paginacion.current_page, principal.filter);
            }
        } catch (error) {
            console.error(error);
        } finally {
            principal.stateModal.delete = false;
        }
    };

    return {
        principal,
        loadingClientTypes,
        createClientType,
        getClientTypeById,
        resetClientTypeData,
        updateClientType,
        deleteClientType,
    };
};