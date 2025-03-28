import { Pagination } from '@/interface/paginacion';
import { UserRequest, UserResource, UserUpdateRequest } from '@/pages/panel/user/interface/User';
import { UserServices } from '@/services/userServices';
import { reactive } from 'vue';

export const useUser = () => {
    const principal = reactive<{
        userList: UserResource[];
        paginacion: Pagination;
        loading: boolean;
        filter: string;
        idUser: number;
        statusModal: {
            update: boolean;
            delete: boolean;
        };
        userData: UserResource;
    }>({
        userList: [],
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
        idUser: 0,
        statusModal: {
            update: false,
            delete: false,
        },
        userData: {
            id: 0,
            name: '',
            email: '',
            username: '',
            status: true,
            created_at: '',
        },
    });
    //reset user data
    const resetUserData = () => {
        principal.userData = {
            id: 0,
            name: '',
            email: '',
            username: '',
            status: true,
            created_at: '',
        };
    };
    // loading users
    const loadingUsers = async (page: number = 1, name: string = '', status: boolean = true) => {
        if (status) {
            principal.loading = true;
            try {
                const response = await UserServices.index(page, name);
                principal.userList = response.users;
                principal.paginacion = response.pagination;
                console.log(response);
            } catch (error) {
                console.error(error);
            } finally {
                principal.loading = false;
            }
        }
    };
    // creating user
    const createUser = async (data: UserRequest) => {
        try {
            await UserServices.store(data);
        } catch (error) {
            console.error(error);
        }
    };
    // get user by id
    const getUserById = async (id: number) => {
        try {
            if (id === 0) {
                principal.userData = {
                    id: 0,
                    name: '',
                    email: '',
                    username: '',
                    status: true,
                    created_at: '',
                };
                return;
            }
            const response = await UserServices.show(id);
            if (response.status) {
                principal.userData = response.user;
                console.log(principal.userData.name);
                principal.idUser = response.user.id;
                principal.statusModal.update = true;
            }
        } catch (error) {
            console.error(error);
        }
    };
    // update user
    const updateUser = async (id: number, data: UserUpdateRequest) => {
        try {
            const response = await UserServices.update(id, data);
            if (response.status) {
                principal.statusModal.update = false;
                loadingUsers(principal.paginacion.current_page, principal.filter);
            }
        } catch (error) {
            console.error(error);
        }
    };
    return {
        principal,
        loadingUsers,
        createUser,
        getUserById,
        resetUserData,
        updateUser,
    };
};
