import { Pagination } from '@/interface/paginacion';
import { UserRequest, UserResource } from '@/pages/panel/user/interface/User';
import { UserServices } from '@/services/userServices';
import { onMounted, reactive } from 'vue';

export const useUser = () => {
    const principal = reactive<{
        userList: UserResource[];
        paginacion: Pagination;
        loading: boolean;
        filter: string;
        idUser: number;
        statusModal: {
            register: boolean;
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
            register: false,
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
    onMounted(() => {
        loadingUsers();
    });
    return {
        principal,
        loadingUsers,
        createUser,
    };
};
