import axios from "axios";
import settings from "@/settings";

const URL = `${settings.BACKEND.Protocol}://${settings.BACKEND.Host}:${settings.BACKEND.Port}`
/**
 * Асинхронное получение товаров с сервера
 *
 * @return {Promise<Object>} Возврат товаров
 */
export async function getProducts(){
    return (await axios.get(`${URL}/products`)).data;
}
/**
 * Асинхронное получение записей корзины с сервера
 *
 * @return {Promise<Object>} Возврат записей корзины
 */
export async function getBasket(){
    return (await axios.get(`${URL}/basket`)).data;
}
/**
 * Асинхронное добавление товара в корзину
 *
 * @param {Object} params - Параметры добавления товара.
 * @param {int} params.id_product Идентификатор, добавляемого товара
 * @return {Promise<Object>} Возврат добавленной записи в корзину
 */
export async function addInBasket({id_product}){
    return (await axios.post(`${URL}/basket/add`,{
        data: {
            id_product: id_product
        }
    })).data;
}
/**
 * Асинхронное удаление товара из корзины
 *
 * @param {Object} params - Параметры удаления записи корзины.
 * @param {int} params.id Идентификатор, удаляемой записи корзины
 * @return {Promise<Object>} Возврат удалённой записи из корзины
 */
export async function deleteInBasket({id}){
    return (await axios.delete(`${URL}/basket/delete/${id}`)).data;
}