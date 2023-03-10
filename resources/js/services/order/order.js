import config from '../../config/config.js';
const url = 'orders';
const detail_order_url = 'detail-order'
export const getAll = (params) => {
    return config.get(url, { params });
}
export const get = (params) => {
    return config.get(detail_order_url, { params });
}
export const getOrderUpdate = (params) => {
    return config.get('detail-orderUpdate', { params })
}
export const insert = (data) => {
    return config.post(url, data);
}
export const update = (id, data) => {
    return config.put('edit-status-order?id=' + id + '&status_id=' + data);
}
export const destroy = (id) => {
    return config.delete(`${url}/${id}`);
}

export const getOrder = (id) => {
    return config.get(`get-order?id=` + id);
}
export const getInfoUser = (id) => {
    return config.get(`get-info?address_id=` + id);
}
export const updateOrderPacket = (data) => {
    return config.put('update-orderpacket', data);
}
