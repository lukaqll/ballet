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
import Posts from './pages/admin/Posts'
import Reports from './pages/admin/Reports'
import ReportsKnowBy from './pages/admin/Reports/KnowBy.vue'

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
            name: 'signer_config',
            path: '/admin/config/signer',
            component: SignerConfig,
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
        {
            name: 'reports',
            path: '/admin/reports',
            component: Reports,
            exact: true
        },
        {
            name: 'reports',
            path: '/admin/reports/know-by',
            component: ReportsKnowBy,
            exact: true
        },

        // register
        {
            name: 'cadastro',
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
            name: 'client.students',
            path: '/alunos',
            component: ClientStudents,
        },
        {
            name: 'client.contracts',
            path: '/contratos',
            component: ClientContracts,
        },
        {
            name: 'client.posts',
            path: '/posts',
            component: ClientPosts,
        },
        {
            name: 'client.invoices',
            path: '/faturas',
            component: ClientInvoices,
        },

        /**
         * site
         */
         {
            name: 'site.home',
            path: '/',
            component: SiteHome,
            exact: true
        },
    ]
}