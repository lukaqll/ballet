import axios from "axios";

const common = {

    request:  ( opt = {} ) => {
    
        // send request
        axios({
            url: opt.url,
            method: opt.type,
            data: opt.type,
            
            headers: opt.auth ? {
                Authorization: 'Bearer ' + localStorage.getItem('auth_token')
            } : null,
        })
        .then ( resp => {
    
            opt.log && console.log( resp )
    
            if( !resp.data )
                return
            
            // success
            if( resp.data.status == 'success' ){
                opt.success && opt.success( resp.data.data )
            } 

            // error
            else {
                opt.error && opt.log( resp.data.message )
            }
    
        })
        .catch ( e => {
            opt.log && console.error( e )
            opt.error && opt.error( e )
        })
    
    }
}

const getClient = ( opt = {} ) => {

    let headers = {
        "Authorization": opt.auth
    }

    if( opt.file ){
        headers['Content-Type'] = 'multipart/form-data'
    }

    return axios.create({
        baseURL: '',
        headers: headers
    })
}

export default common;