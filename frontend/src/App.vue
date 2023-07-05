<!--Главный компонент-->
<template>
  <v-app
      v-resize="onResize"
  >
    <v-dialog v-model="errorProps.open" :persistent="!errorProps.isPossibilityExit">
      <v-sheet
          elevation="12"
          max-width="600"
          rounded="lg"
          width="100%"
          class="pa-4 text-center mx-auto"
      >
        <v-icon
            class="mb-5"
            color="error"
            icon="mdi-exclamation"
            size="112"
        ></v-icon>

        <h2 class="text-h5 mb-6">Ошибка</h2>

        <p class="mb-4 text-medium-emphasis text-body-2">
          {{errorProps.text}}
        </p>
        <v-divider v-if="errorProps.isPossibilityExit" class="mb-4"></v-divider>

        <div class="text-end" v-if="errorProps.isPossibilityExit">
          <v-btn
              class="text-none"
              color="error"
              rounded
              variant="flat"
              width="90"
              @click="errorProps.open = false"
          >
            Закрыть
          </v-btn>
        </div>
      </v-sheet>
    </v-dialog>
    <v-navigation-drawer v-if="showMobileMenu" v-model="openMobileMenu" color="red-darken-1" temporary>
      <v-list
          dense
          nav>
        <v-list-item
            v-for="menuItem in menuItems"
            :key="menuItem.value"
            :title="menuItem.title"
            :value="menuItem.value"
            :prepend-icon="menuItem.icon"
            :active="selectedPage === menuItem.value"
            @click="selectedPage = menuItem.value"/>
      </v-list>
    </v-navigation-drawer>
    <v-main>
      <v-toolbar color="red-darken-1">
        <v-img :src="logoPath" class="logo" max-width="100"></v-img>
        <v-spacer/>
        <span>Телефон для связи с нами: <strong>+79998881111</strong></span>
        <v-spacer/>
        <button-basket
            :basket="basket"
            :products="products"
            @click-item-basket="onClickItemBasket"
            @remove-item-basket="onRemoveItemBasket"
        />
        <v-btn
            v-if="showMobileMenu"
            icon
            @click.stop="openMobileMenu = !openMobileMenu">
          <v-app-bar-nav-icon></v-app-bar-nav-icon>
        </v-btn>
        <template v-slot:extension v-if="showMainMenu">
          <v-tabs
              align-tabs="title"
          >
            <v-tab
                v-for="menuItem in menuItems"
                :key="menuItem.value"
                :value="menuItem.value"
                :prepend-icon="menuItem.icon"
                @click="selectedPage = menuItem.value"
            >
              {{ menuItem.title }}
            </v-tab>
          </v-tabs>
        </template>
      </v-toolbar>
      <home-page
          v-if="selectedPage === 'home'"
          :products="products"
          :basket="basket"
          :products-detailed-description-call="productsDetailedDescriptionCall"
          @close-detailed-description-product="onCloseModalWindow"
          @edit-basket="onEditProductInBasket"
      />
    </v-main>
    <v-footer color="red-darken-1">
      <v-row justify="center" no-gutters>
        <v-col :cols="12" :md="6" class="d-flex align-center justify-center">
          <v-img :src="logoPath" class="logo" max-width="100"></v-img>
        </v-col>
        <v-col :cols="12" :md="6" class="d-flex align-center justify-center">
          Телефон для связи с нами: <strong>+79998881111</strong>
        </v-col>
        <v-col :cols="12" class="text-center mt-4">
          {{ new Date().getFullYear() }} — <strong>Vuetify</strong>
        </v-col>
      </v-row>
    </v-footer>
  </v-app>
</template>

<script>
//Импорт асинхронных функций получения всех товаров, получение записей корзины, добавление записи в корзину, удаление записи из корзины
import {
  getProducts,
  getBasket,
  addInBasket, deleteInBasket
} from '@/api';
//Получение пути изображения лого
import logoPath from '@/media/logo.png';
//Импорт компонента кнопки корзины
import ButtonBasket from "@/components/ButtonBasket.vue";
//Импорт домашней страницы
import HomePage from "@/components/HomePage.vue";
//Импорт функции для взаимодействия с дисплеем, полученного из vuetify
import { useDisplay } from 'vuetify'

export default {
  name: 'App',
  components: {
    HomePage,
    ButtonBasket,
  },
  data: function(){
    return{
      // @type {Array} Товары
      products: [],
      // @type {Array} Записи корзины
      basket: [],
      // @type {Array} Товары, у которых надо вызвать подробное описание
      productsDetailedDescriptionCall:[],
      // @type {String} Путь до изображения лого
      logoPath: logoPath,
      // @type {Boolean} Открытие/закрытие мобильного меню
      openMobileMenu: false,
      // @type {Boolean} Включение/отключение основного меню
      showMainMenu: true,
      // @type {Boolean} Включение/отключение мобильного меню
      showMobileMenu: false,
      // @type {String} Отображаемая страница
      selectedPage: "home",
      // @type {Array} Элементы меню
      menuItems: [
        {
          title: "Домой",
          value: "home",
          icon: "mdi-view-dashboard"
        },
        {
          title: "О нас",
          value: "aboutUs",
          icon: "mdi-forum"
        },
      ],
      // @type {Object} Объект для взаимодействия с окном ошибок
      errorProps: {
        // @type {Boolean} Открыто/закрыто
        open: false,
        // @type {String} Текст, отображаемый в окне
        text: "",
        // @type {Boolean} Можно или нельзя закрыть окно
        isPossibilityExit: false
      },
      // @type {Boolean} Переменная, которая принимает значение false - в случае, если окно меньше метки md, иначе true (получена из vuetify)
      mdAndUp: useDisplay().mdAndUp
    }
  },
  //Хук, вызывается после того, как компонент был добавлен в DOM, в нём получаем переменную mdAndUp, также асинхронно товары и записи из корзины
  mounted() {
    getProducts().then(val => {
      this.products = val
      this.products.map(product => {
        if("message" in val){
          this.callErrorWindow({text: "Страница не доступна. Попробуйте зайти позже."})
          return
        }
        this.productsDetailedDescriptionCall.push(
            {
              id:product.id,
              callDetailedDescription:false
            })
      })
    }).catch(() => {
      this.callErrorWindow({text: "Страница не доступна. Попробуйте зайти позже."})
    })
    getBasket().then(val => {
      if("message" in val){
        return
      }
      this.basket = val
    }).catch(() => {
      this.callErrorWindow({text: "Страница не доступна. Попробуйте зайти позже."})
    })
  },
  methods:{
    /**
     * Вызов окна ошибок
     *
     * @param {Object} params - Параметры вызова
     * @param {string} params.text Текст ошибки
     * @param {Boolean} params.text Можно или нельзя закрыть окно(по умолчанию нельзя)
     */
    callErrorWindow: function({text,isPossibilityExit = false}){
      this.errorProps.text = text
      this.errorProps.open = true
      this.errorProps.isPossibilityExit = isPossibilityExit
    },
    /**
     * Слушатель события "click-item-basket" ButtonBasket, в нём мы ищем товар, у которого надо вызвать подробное описание и изменяем его значение на true
     *
     * @param {Object} params - Параметры вызова
     * @param {number} params.idProduct Идентификатор, выбранного товара
     */
    onClickItemBasket: function ({idProduct}){
      this.productsDetailedDescriptionCall[
          this.productsDetailedDescriptionCall.findIndex(
              productDetailedDescriptionCall => productDetailedDescriptionCall.id === idProduct
          )
          ].callDetailedDescription = true
    },
    /**
     * Слушатель события изменения окна, в нём мы показываем, либо обычное меню, либо мобильное
     */
    onResize: function (){
      if(this.mdAndUp){
        this.showMainMenu = true
        this.showMobileMenu = false
      }
      else {
        this.showMainMenu = false
        this.showMobileMenu = true
      }
    },
    /**
     * Слушатель события "remove-item-basket" ButtonBasket, в нём мы асинхронно шлём серверу запрос на удаление записи из корзины
     *
     * @param {Object} params - Параметры вызова
     * @param {number} params.id Идентификатор, удаляемой записи из корзины
     */
    onRemoveItemBasket: function ({id}){
      deleteInBasket({id: id}).then(val=>{
        if("message" in val){
          this.callErrorWindow({text: val.message,isPossibilityExit: true})
          return
        }
        const deleteIndexBasket = this.basket.findIndex(itemBasket => itemBasket.id === id)
        this.basket.splice(deleteIndexBasket,1)
      }).catch(ex => {
        this.callErrorWindow({text: ex.message,isPossibilityExit: true})
      })
    },
    /**
     * Слушатель события "close-detailed-description-product" HomePage, в нём мы ищем товар, у которого было закрыто подробное описание и изменяем его значение на false
     *
     * @param {Object} params - Параметры вызова
     * @param {number} params.idProduct Идентификатор, товара, у которого было закрыто подробное описание
     */
    onCloseModalWindow: function ({idProduct}){
      this.productsDetailedDescriptionCall[
          this.productsDetailedDescriptionCall.findIndex(productDetailedDescriptionCall => productDetailedDescriptionCall.id === idProduct)
          ].callDetailedDescription = false
    },
    /**
     * Слушатель события "edit-basket" HomePage, в нём если товар был добавлен шлём асинхронный запрос серверу на добавление новой записи в корзину, иначе асинхронное удаление
     *
     * @param {Object} params - Параметры вызова
     * @param {number} params.idProduct Идентификатор, товара, у которого была изменена принадлежность к корзине
     * @param {boolean} params.inBasket Принадлежность к корзине
     */
    onEditProductInBasket: function ({idProduct,inBasket}){
      if(inBasket){
        addInBasket({id_product:idProduct}).then(val => {
          if("message" in val){
            this.callErrorWindow({text: val.message,isPossibilityExit: true})
            return
          }
          this.basket.push(val.added_record_basket)
        }).catch(ex => {
          this.callErrorWindow({text: ex.message,isPossibilityExit: true})
        })
      }
      else{
        const deleteIndexBasket = this.basket.findIndex(itemBasket => itemBasket.id_product === idProduct)
        deleteInBasket({id: this.basket[deleteIndexBasket].id}).then(val=>
        {
          if("message" in val){
            this.callErrorWindow({text: val.message,isPossibilityExit: true})
            return
          }
          this.basket.splice(deleteIndexBasket,1)
        }).catch(ex => {
          this.callErrorWindow({text: ex.message,isPossibilityExit: true})
        })
      }
    },
  }
}
</script>
<style scoped></style>
