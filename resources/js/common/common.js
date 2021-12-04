import axios from "axios";
import SweetAlert from 'sweetalert2'

const common = {

    request:  ( opt = {url, type, data, auth, log, success, savedAlert, error, setError, file} ) => {
    
        // send request
        axios({
            url: opt.url,
            method: opt.type,
            data: opt.data,
            
            headers:  {
                'Content-Type': opt.file ? 'multipart/form-data' : undefined,
                'Authorization': opt.auth ? 'Bearer ' + localStorage.getItem('auth_token') : null
            },
        })
        .then ( resp => {
    
            opt.log && console.log( resp )
    
            if( !resp.data )
                return
            
            // success
            if( resp.data.status == 'success' ){
                opt.success && opt.success( resp.data.data )

                //savedAlert is true
                opt.savedAlert && common.success({title: 'Salvo!'})
            } 

            // error
            else {
                opt.error && opt.log( resp.data.message )

                //if set error is true
                opt.setError && common.setError({message: common.formatRequiredError(resp.data.message) || 'Ops! Houve algum erro.'})
            }
    
        })
        .catch ( e => {
            opt.log && console.error( e )
            opt.error && opt.error( e )
        })
    
    },

    formartDate: function(str, time=false){

        if(str == null || str == '' || !str) return ''

        const dateTimeSplit = str.match('T') ? str.split('T') : str.split(' ')

        const parts = dateTimeSplit[0].split('-')
        const day = parts[2]
        const month = parts[1]
        const year = parts[0]
        
        return day + '/' + month + '/' + year + (dateTimeSplit[1] && time ? (' ' + dateTimeSplit[1].split('.')[0]) : '')
    },

    toMoney: function(str, currency=false){

        if(str == null || str == '' || !str)
            return currency ? ('R$ ' + '0,00') : '0,00'

        if(parseFloat(str) > 0){
    
            let val = parseFloat(str).toFixed(2)
            let array = parseFloat(val).toLocaleString("pt-BR", {currency:"BRL"}).split(',')
            
            let decimal = array[1] ? array[1].padEnd(2, '0') : '00'

            const toReturn = ( array[0] + ',' + decimal )
            return currency ? ('R$ ' + toReturn) : toReturn
        } else {
            return currency ? ('R$ ' + '0,00') : '0,00'
        }
    },

    setError: function(opt = {}){
        SweetAlert.fire({
            icon: opt.type ? opt.type : 'error',
            title: opt.title,
            html: opt.message ? opt.message : ''
        })
    },

    success: function(opt = {}){
        SweetAlert.fire({
            icon: 'success',
            title: opt.title,
            html: '<p>' + (opt.message || '') + '</p>',
            timer: opt.timer || 1000,
            showConfirmButton: false,
        })
    },

    confirmAlert: function(opt = {}){
        SweetAlert.fire({
            icon: opt.type || 'warning',
            title: opt.title || '',
            html: '<p>' + (opt.message || '') + '</p>',
            showCancelButton: true,
            confirmButtonText: opt.confirmButtonText || 'Sim',
            cancelButtonText: opt.cancelButtonText || 'Cancelar',
        }).then((result)=>{
            if(result.value) {
                opt.onConfirm && opt.onConfirm()
            } else {
                opt.onCancel && opt.onCancel()
            }
        })
    },

    formatRequiredError: ( messageArray ) => {
        let message = ''
        if(!messageArray) return ''

        if( typeof messageArray == 'string' )
            return messageArray

        for(const e in messageArray  ){
            message += '<i class="fa fa-angle-right text-secondary"></i> ' + messageArray[e] + '<br>'
        }
        return '<div class="d-flex justify-content-center"><p class="text-left">' + message + '</p></div>'
    },

}

export default common;