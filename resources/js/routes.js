import common from './common/common'
import NotFound from './pages/errors/404'
import Login from './pages/auth/Login'
import Home from './pages/admin/Home'
import Users from './pages/admin/Users'
import Units from './pages/admin/Units'
import Students from './pages/admin/Students'
import Settings from './pages/admin/Settings'
import PasswordReset from './pages/auth/PasswordRecovery/PasswordReset'
import SendMail from './pages/auth/PasswordRecovery/SendMail'
import Config from './pages/admin/Config'
import ContractConfig from './pages/admin/Config/ContractConfig'
import Register from './pages/Auth/Register'
import NewRegistration from './pages/admin/NewRegistration'
import Contracts from './pages/admin/Contracts'
import Posts from './pages/admin/Posts'

import ClientHome from './pages/dashboard/Home'
import ClientSettings from './pages/dashboard/Settings'
import ClientStudents from './pages/dashboard/Students'
import ClientContracts from './pages/dashboard/Contracts'
import ClientPosts from './pages/dashboard/Posts'
import ClientInvoices from './pages/dashboard/Invoices'

const beforeAdminEnter = (to, form, next) => {
    common.request({
        type: 'get',
        url: '/api/user/admin',
        auth: true,
        success: (resp) => {
            next()
        }, 
        error: e => {
            return next({name: 'login'})
        }
    })
}

const beforeEnter = (to, form, next) => {
    common.request({
        type: 'get',
        url: '/api/user/client',
        auth: true,
        success: (resp) => {
            next()
        }, 
        error: e => {
            return next({name: 'login'})
        }
    })
}

export default {
    mode: 'history',
    linkActiveClass: 'active',

    routes: [
        {
            path: '*',
            component: NotFound
        },

        {
            name: 'login',
            path: '/login',
            component: Login
        },

        {
            name: 'admin.home',
            path: '/admin',
            component: Home,
        },
        {
            path: '/admin/users',
            component: Users,
        },
        {
            path: '/admin/units',
            component: Units,
        },
        {
            path: '/admin/students',
            component: Students,
        },
        {
            path: '/admin/settings',
            component: Settings,
        },

        // password recovery
        {
            name: 'password_reset',
            path: '/password-reset/:token',
            component: PasswordReset
        },
        {
            name: 'password_recovery',
            path: '/password-recovery',
            component: SendMail
        },
        {
            name: 'config',
            path: '/admin/config',
            component: Config,
        },
        {
            name: 'contract_config',
            path: '/admin/config/contract',
            component: ContractConfig,
        },
        {
            name: 'new_users',
            path: '/admin/new-users',
            component: NewRegistration,
        },
        {
            name: 'contracts',
            path: '/admin/contracts',
            component: Contracts,
        },
        {
            name: 'contracts',
            path: '/admin/posts',
            component: Posts,
        },

        // register
        {
            name: 'contract_config',
            path: '/cadastro',
            component: Register
        },


        /**
         * client dashboard
         */
        {
            name: 'client.home',
            path: '/home',
            component: ClientHome,
            exact: true
        },
        {
            name: 'client.settings',
            path: '/minha-conta',
            component: ClientSettings,
        },
        {
            name: 'client.settings',
            path: '/alunos',
            component: ClientStudents,
        },
        {
            name: 'client.settings',
            path: '/contratos',
            component: ClientContracts,
        },
        {
            name: 'client.settings',
            path: '/posts',
            component: ClientPosts,
        },
        {
            name: 'client.settings',
            path: '/faturas',
            component: ClientInvoices,
        },
    ]
}