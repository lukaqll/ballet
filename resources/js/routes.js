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
import SignerConfig from './pages/admin/Config/SignerConfig'
import Register from './pages/auth/Register'
import NewRegistration from './pages/admin/NewRegistration'
import Contracts from './pages/admin/Contracts'
import Sales from './pages/admin/Sales'
import Posts from './pages/admin/Posts'
import Reports from './pages/admin/Reports'
import ReportsKnowBy from './pages/admin/Reports/KnowBy.vue'
import ReportsRevenue from './pages/admin/Reports/Revenue.vue'

import ClientHome from './pages/dashboard/Home'
import ClientSettings from './pages/dashboard/Settings'
import ClientStudents from './pages/dashboard/Students'
import ClientContracts from './pages/dashboard/Contracts'
import ClientPosts from './pages/dashboard/Posts'
import ClientInvoices from './pages/dashboard/Invoices'

import SiteHome from './pages/site'

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
            component: NotFound,
            meta: {base: 'commom-header'}
        },

        {
            name: 'login',
            path: '/login',
            component: Login,
            meta: {base: 'commom-header'}
        },

        {
            name: 'admin.home',
            path: '/admin',
            component: Home,
            meta: {base: 'admin-base'}
        },
        {
            path: '/admin/users',
            component: Users,
            meta: {base: 'admin-base'}
        },
        {
            path: '/admin/units',
            component: Units,
            meta: {base: 'admin-base'}
        },
        {
            path: '/admin/students',
            component: Students,
            meta: {base: 'admin-base'}
        },
        {
            path: '/admin/settings',
            component: Settings,
            meta: {base: 'admin-base'}
        },

        // password recovery
        {
            name: 'password_reset',
            path: '/password-reset/:token',
            component: PasswordReset,
            meta: {base: 'commom-header'}

        },
        {
            name: 'password_recovery',
            path: '/password-recovery',
            component: SendMail,
            meta: {base: 'commom-header'}
        },
        {
            name: 'config',
            path: '/admin/config',
            component: Config,
            meta: {base: 'admin-base'}
        },
        {
            name: 'contract_config',
            path: '/admin/config/contract',
            component: ContractConfig,
            meta: {base: 'admin-base'}
        },
        {
            name: 'signer_config',
            path: '/admin/config/signer',
            meta: {base: 'admin-base'},
            component: SignerConfig,
        },
        {
            name: 'new_users',
            path: '/admin/new-users',
            meta: {base: 'admin-base'},
            component: NewRegistration,
        },
        {
            name: 'contracts',
            path: '/admin/contracts',
            meta: {base: 'admin-base'},
            component: Contracts,
        },
        {
            name: 'contracts',
            path: '/admin/posts',
            meta: {base: 'admin-base'},
            component: Posts,
        },
        {
            name: 'sales',
            path: '/admin/sales',
            meta: {base: 'admin-base'},
            component: Sales,
            exact: true
        },
        {
            name: 'reports',
            path: '/admin/reports',
            meta: {base: 'admin-base'},
            component: Reports,
            exact: true
        },
        {
            name: 'reports',
            path: '/admin/reports/know-by',
            meta: {base: 'admin-base'},
            component: ReportsKnowBy,
            exact: true
        },
        {
            name: 'reports',
            path: '/admin/reports/revenue',
            meta: {base: 'admin-base'},
            component: ReportsRevenue,
            exact: true
        },

        // register
        {
            name: 'cadastro',
            path: '/cadastro',
            component: Register,
            meta: {base: 'commom-header'}
        },
        


        /**
         * client dashboard
         */
        {
            name: 'client.home',
            path: '/home',
            component: ClientHome,
            exact: true,
            meta: {base: 'dashboard-base'}
        },
        {
            name: 'client.settings',
            path: '/minha-conta',
            component: ClientSettings,
            meta: {base: 'dashboard-base'}
        },
        {
            name: 'client.students',
            path: '/alunos',
            component: ClientStudents,
            meta: {base: 'dashboard-base'}
        },
        {
            name: 'client.contracts',
            path: '/contratos',
            component: ClientContracts,
            meta: {base: 'dashboard-base'}
        },
        {
            name: 'client.posts',
            path: '/posts',
            component: ClientPosts,
            meta: {base: 'dashboard-base'}
        },
        {
            name: 'client.invoices',
            path: '/faturas',
            component: ClientInvoices,
            meta: {base: 'dashboard-base'}
        },

        /**
         * site
         */
         {
            name: 'site.home',
            path: '/',
            component: SiteHome,
            exact: true,
            meta: {base: 'site-base'}
        },
    ]
}