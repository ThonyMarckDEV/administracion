import { Pagination } from '@/interface/paginacion';
import { ServiceRequest, ServiceResource, ServiceUpdateRequest } from '@/pages/panel/service/interface/Service';
import { ServiceServices } from '@/services/serviceServices';
import { showSuccessMessage } from '@/utils/message';
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

export const useService = () => {
    const principal = reactive<{
        serviceList: ServiceResource[];
        paginacion: Pagination;
        loading: boolean;
        filter: string;
        idService: number;
        statusModal: {
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
            update: false,
            delete: false,
        },
        serviceData: {
            id: 0,
            name: '',
            cost: 0,
            ini_date: '',
            state: true,
            created_at: '',
        },
    });

    // reset service data
    const resetServiceData = () => {
        principal.serviceData = {
            id: 0,
            name: '',
            cost: 0,
            ini_date: '',
            state: true,
            created_at: '',
        };
    };

    // loading services
    const loadingServices = async (page: number = 1, name: string = '') => {
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
    };

   // In useService composable
    const createService = async (data: ServiceRequest) => {
        try {
            const response = await ServiceServices.store(data);
            
            // Check if the response indicates successful creation
            if (response && response.status === 201) {
                // Show success message
                showSuccessMessage('Servicio Creado', 'El servicio se creó correctamente');
                
                // Navigate to users panel or services list
                router.visit('/panel/services');
            }
        } catch (error: any) {
            // Handle different types of errors
            if (error.response) {
                // The request was made and the server responded with a status code
                // that falls out of the range of 2xx
                const errorMessage = error.response.data.message || 
                                    error.response.data.error || 
                                    'Error al crear el servicio';
                
                // You can use a toast or alert system to show the error
                showErrorMessage('Error', errorMessage);
            } else if (error.request) {
                // The request was made but no response was received
                showErrorMessage('Error', 'No se pudo conectar con el servidor');
            } else {
                // Something happened in setting up the request that triggered an Error
                showErrorMessage('Error', 'Ocurrió un error inesperado');
            }
            
            // Log the full error for debugging
            console.error('Service creation error:', error);
        }
    };

    // get service by id
    const getServiceById = async (id: number) => {
        try {
            if (id === 0) {
                principal.serviceData = {
                    id: 0,
                    name: '',
                    cost: 0,
                    ini_date: '',
                    state: true,
                    created_at: '',
                };
                return;
            }
            const response = await ServiceServices.show(id);
            
            // Use response.data instead of checking response.status
            if (response && response.data) {
                principal.serviceData = response.data;
                console.log(principal.serviceData.name);
                principal.idService = response.data.id;
                principal.statusModal.update = true;
            } else {
                console.error('Invalid response structure', response);
                resetServiceData();
            }
        } catch (error) {
            console.error('Error fetching service:', error);
            resetServiceData();
        }
    };

    // update service
    const updateService = async (id: number, data: ServiceUpdateRequest) => {
        try {
            const response = await ServiceServices.update(id, data);
            if (response.status) {
                showSuccessMessage('Servicio actualizado', 'El servicio se actualizó correctamente');
                principal.statusModal.update = false;
                loadingServices(principal.paginacion.current_page, principal.filter);
            }
        } catch (error) {
            console.error(error);
        }
    };

    // delete service
    const deleteService = async (id: number) => {
        try {
            const response = await ServiceServices.destroy(id);
            if (response.status) {
                showSuccessMessage('Servicio eliminado', 'El servicio se eliminó correctamente');
                principal.statusModal.delete = false;
                loadingServices(principal.paginacion.current_page, principal.filter);
            }
        } catch (error) {
            console.error(error);
        } finally {
            principal.statusModal.delete = false;
        }
    };

    return {
        principal,
        loadingServices,
        createService,
        getServiceById,
        resetServiceData,
        updateService,
        deleteService,
    };
};