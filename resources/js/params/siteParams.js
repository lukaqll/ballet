
const phone = '(28) 99925-8255'
const phoneFull = '5528999258255'

export default {

    phone: phone,
    phonefull: phoneFull,
    
    networks: [
        {class: 'instagram', text: '@elleganceballet', link: 'https://www.instagram.com/elleganceballet/'},
        {class: 'facebook', text: '/elleganceballet', link: 'https://www.facebook.com/elleganceballet/'},
        {class: 'whatsapp', text: phone, link: `https://wa.me?phone=${phoneFull}`},
    ],

    classes: [
        {name: 'Ballet Clássico', description: '', price: ''},
        {name: 'Jazz Contemporâneo', description: '', price: ''},
    ],
    
    units: [
        {name: 'Ellegance Ballet – Ibatiba ES', address: 'Praça Central', phone: phone, mail: ''},
        {name: 'Ellegance Ballet – Venda Nova do Imigrante ES', address: 'Vila Betânia', phone: phone, mail: ''},
    ],

    banners: [
        {src: '/site/images/banner/banner-1.jpg'},
        {src: '/site/images/banner/banner-2.png'},
        {src: '/site/images/banner/banner-3.jpg'},
    ]
}