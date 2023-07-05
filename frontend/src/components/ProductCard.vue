<!--Компонент, предоставляющий карточку товара-->
<template>
  <div>
    <v-dialog
        transition="dialog-bottom-transition"
        v-model="openDetailedDescription"
        width="auto"
    >
      <template v-slot:default>
        <v-card max-width="900">
          <v-container class="fill-height">
            <v-row no-gutters>
              <v-col :cols="12" :md="6" class="d-flex justify-center">
                <v-img :src="pathImage"  class="col-2 col-md-3  d-md-flex d-xs-none d-flex"/>
                <img :src="pathImage"  class="col-2 col-md-3 d-md-none d-xs-block d-none"/>
              </v-col>
              <v-col :cols="12" :md="6">
                <v-sheet class="ma-2">
                  <p class="text-h5 text-red-darken-1 text-start pb-3">{{name}}</p>
                  <v-spacer></v-spacer>
                  <p class="text-body-1 text-justify"> {{description}}</p>
                  <v-spacer></v-spacer>
                  <v-container class="px-0">
                    <v-row no-gutters>
                      <v-col :cols="12" :sm="6" class="d-flex justify-sm-start justify-center pb-xs-0 pb-3">
                        <span class="text-h6 font-weight-bold">{{price}} руб</span>
                      </v-col>
                      <v-col :cols="12" :sm="6" class="d-flex justify-sm-end justify-center">
                        <v-btn :variant="!isAddedBasket ? 'tonal' : 'flat'" rounded="lg" color="red-darken-1"
                               @click="editProductInBasket">
                          {{ !isAddedBasket ? 'Добавить в корзину' : 'В корзине' }}
                        </v-btn>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-sheet>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </template>
    </v-dialog>
    <v-card
        class="mx-auto"
        max-width="315"
    >
      <v-img :src="pathImage"></v-img>
      <v-card-title class="text-red-darken-1 card-title">
        {{name}}
      </v-card-title>
      <v-spacer></v-spacer>
      <v-card-actions class="d-flex justify-space-between">
        <h3 class="ps-2">{{price}} руб</h3>
        <v-btn variant="tonal" rounded="lg" color="red-darken-1" @click="openDetailedDescription = !openDetailedDescription">
          Подробнее
        </v-btn>
      </v-card-actions>
    </v-card>
  </div>
</template>

<script>
export default {
  name: "ProductCard",
  data: function(){
    return{
      // @type {Boolean} Локальная переменная, отвечающая за открытие/закрытие подробного описания товара
      openDetailedDescription: false,
      // @type {Boolean} Локальная переменная, которая меняет принадлежность товара к корзине
      isAddedBasket: false
    }
  },
  emits:
      [
        "close-detailed-description",//Событие закрытия подробного описания товара
        "edit-basket"//Событие изменения принадлежности товара к корзине
      ],
  //Хук, вызывается после того, как компонент был добавлен в DOM, в нём мы приравниваем значение локальной принадлежности товара к корзине к глобальной.
  mounted: function() {
    this.isAddedBasket = this.isProductInBasket
  },
  props: {
    // @type {Number} Идентификатор товара, получаем из-вне
    id: Number,
    // @type {String} Название товара, получаем из-вне
    name: String,
    // @type {String} Описание товара, получаем из-вне
    description: String,
    // @type {Number} Цена товара, получаем из-вне
    price: Number,
    // @type {String} Путь до изображения товара, получаем из-вне
    pathImage: String,
    // @type {Boolean} Глобальная переменная, которая отвечает за вызов окна подробного описания, получаем из-вне
    parentOpenDetailedDescription: Boolean,
    // @type {Boolean} Глобальная переменная, которая отвечает за принадлежность товара к корзине, получаем из-вне
    parentIsAddedBasket: Boolean
  },
  methods:{
    /**
     * Метод, изменяющий принадлежность товара к корзине и генерирующий событие, оповещающее другие компоненты
     */
    editProductInBasket: function (){
      this.isAddedBasket = !this.isAddedBasket
      this.$emit("edit-basket", {idProduct:this.id,inBasket:this.isAddedBasket});
    }
  },
  watch: {
    /**
     * Метод, отслеживающий изменения вызова подробного описания из вне, в нём изменяется значения локального открытия подробного описания товара
     *
     * @param {Boolean} newValue - Новое значение, вызова подробного описания товара из-вне
     */
    parentOpenDetailedDescription(newValue) {
      if(newValue){
        this.openDetailedDescription = true
      }
    },
    /**
     * Метод, отслеживающий изменения в переменной локального открытия/закрытия подробного описания, он нужен для того, чтобы сообщить родительскому компоненту, что подробное описание товара было закрыто, при условии вызова подробного описания из вне
     */
    openDetailedDescription(){
      if(this.parentOpenDetailedDescription){
        this.$emit("close-detailed-description", {idProduct : this.id});
      }
    },
    /**
     * Метод, отслеживающий изменения в глобальной переменной принадлежности товара к корзине, в случае её изменения мы меняем значение локальной принадлежности товара к корзине
     *
     * @param {Boolean} newValue - Новое значение, изменения принадлежности товара к корзине
     */
    parentIsAddedBasket(newValue){
      this.isAddedBasket = newValue
    }
  }
}
</script>

<style scoped>
.card-title{
  font-size: 18px!important;
}
</style>