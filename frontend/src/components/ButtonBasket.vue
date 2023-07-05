<!--Компонент, предоставляющий кнопку корзины-->
<template>
  <v-menu location="bottom">
    <!-- eslint-disable-next-line vue/no-unused-vars -->
    <template v-slot:activator="{ props }">
      <v-btn icon :="props">
        <v-badge :content="basket.length" color="black">
          <v-icon icon="mdi-cart"></v-icon>
        </v-badge>
      </v-btn>
    </template>

    <v-list v-if="basket.length !== 0">
      <v-list-item
          v-for="itemBasket in basket"
          :key="itemBasket.id"
          :class="{'animate-leave': idItemBasketToRemove === itemBasket.id}"
          @click="clickItemBasket({idProduct:itemBasket.id_product})"
      >
        <v-list-item-title>{{ products.find(product => product.id === itemBasket.id_product).name }}</v-list-item-title>
        <template v-slot:append>
          <v-btn
              color="red-darken-1"
              icon="mdi-delete"
              variant="text"
              @click.stop="removeItemBasket({id: itemBasket.id})"
          ></v-btn>
        </template>
      </v-list-item>
    </v-list>
  </v-menu>
</template>
<script>
export default {
  name: "ButtonBasket",
  data: function(){
    return{
      // @type {Number} Идентификатор, удаляемой записи из корзины
      idItemBasketToRemove: -1
    }
  },
  props: {
    // @type {Array} Корзина товаров, получаемая из вне
    basket: Array,
    // @type {Array} Товары, получаемые из вне
    products: Array
  },
  emits: [
    "click-item-basket",//Событие нажатия на товар, находящийся в корзине
    "remove-item-basket"//Событие удаления товара из корзины
  ],
  methods:{
    /**
     * Метод удаления товара из корзины, в этом методе мы генерируем событие о удалении товара из корзины, где передаём запись корзины, и ставим задержку для отработки анимации
     *
     * @param {Object} params - Параметры полученные, от пользователя
     * @param {int} params.id Идентификатор, удаляемого товара из корзины
     */
    removeItemBasket: function({id}) {
      this.idItemBasketToRemove = id;
      setTimeout(() => {
        this.$emit("remove-item-basket", {id:id});
        this.idItemBasketToRemove = -1;
      }, 500); // Время задержки анимации (в миллисекундах)
    },
    /**
     * Метод нажатия на товар из корзины
     *
     * @param {Object} params - Параметры полученные, от пользователя
     * @param {int} params.idProduct Идентификатор, выбранного товара из корзины
     */
    clickItemBasket: function ({idProduct}){
      this.$emit("click-item-basket", {idProduct: idProduct});
    }
  }
}
</script>
<style scoped>
.animate-leave {
  animation: slide-out 0.5s ease-out;
}
@keyframes slide-out {
  0% {
    opacity: 1;
    transform: translateX(0);
  }
  100% {
    opacity: 0;
    transform: translateX(-100%);
  }
}
</style>