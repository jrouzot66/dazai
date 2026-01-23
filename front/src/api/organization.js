import axios from 'axios'

export async function fetchOrganizations() {
    const { data } = await axios.get('/api/organizations')
    return data
}