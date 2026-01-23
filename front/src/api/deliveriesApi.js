import axios from 'axios'

export async function fetchMoDeliveries() {
    const { data } = await axios.get('/api/mo/deliveries')
    return data
}

export async function fetchFoDeliveries() {
    const { data } = await axios.get('/api/fo/deliveries')
    return data
}

export async function createMoDelivery(deliveryData) {
    const { data } = await axios.post('/api/mo/deliveries', deliveryData)
    return data
}