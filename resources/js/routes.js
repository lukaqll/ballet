import Login from './pages/auth/Login'
import Home from './pages/admin/Home'
import Users from './pages/admin/Users'
import NotFound from './pages/errors/404'

import common from './common/common'

const beforeEnter = (to, form, next) => {
    common.request({
        type: 'get',
        url: '/api/user',
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
    linkActiveClass: 'font-semibold',

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
            beforeEnter
        },
        {
            path: '/admin/users',
            component: Users
        }
    ]
}