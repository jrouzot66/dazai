import axios from 'axios'

export async function fetchMoWarehouses() {
    const { data } = await axios.get('/api/mo/warehouses')
    return data
}

export async function createMoWarehouse(payload) {
    const { data } = await axios.post('/api/mo/warehouses', payload)
    return data
}