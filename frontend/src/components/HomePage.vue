<!--Компонент, предоставляющий главную страницу-->
<template>
  <v-container>
    <v-row>
      <v-col v-for="product in products" :key="product.id" :cols="12" :lg="3" :md="4" :sm="6">
        <product-card
            :id="product.id"
            :name="product.name"
            :price="product.price"
            :description="product.description"
            :path-image="product.path_image"
            :parent-open-detailed-description="productsDetailedDescriptionCall[productsDetailedDescriptionCall.findIndex(productDetailedDescriptionCall => productDetailedDescriptionCall.id === product.id)].callDetailedDescription"
            :parent-is-added-basket="basket.findIndex(itemBasket => itemBasket.id_product === product.id) !== -1"
            @close-detailed-description="onCloseDetailedDescriptionProduct"
            @edit-basket="onEditBasket"
        />
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
  //Импорт компонента, предоставляющего карточку товара
  import ProductCard from "@/components/ProductCard.vue";
  export default {
    name: "HomePage",
    components: {ProductCard},
    emits: [
      "close-detailed-description-product",//Событие закрытия подробного описания товара
      "edit-basket"//Событие изменения принадлежности товара к корзине
    ],
    props:{
      // @type {Array} Товары, получаемые из вне
      products: Array,
      // @type {Array} Корзина товаров, получаемая из вне
      basket: Array,
      // @type {Array} Переменная, описывающая товары у которых было вызвано детальное описание из вне
      productsDetailedDescriptionCall: Array
    },
    methods:{
      /**
       * Слушатель события "close-detailed-description-product" ProductCard, является прослойкой между компонентом App и ProductCard
       *
       * @param {Object} params - Параметры полученные из события
       * @param {int} params.idProduct Идентификатор, товара, у которого было закрыто подробное описание
       */
      onCloseDetailedDescriptionProduct: function ({idProduct}){
        this.$emit("close-detailed-description-product",{idProduct:idProduct})
      },
      /**
       * Слушатель события "edit-basket" ProductCard, является прослойкой между компонентом App и ProductCard
       *
       * @param {Object} params - Параметры полученные из события
       * @param {int} params.idProduct Идентификатор, товара, у которого было изменено наличие его в корзине
       * @param {boolean} params.inBasket Значение, говорящее нам о том, находится ли товар в коризне
       */
      onEditBasket: function ({idProduct,inBasket}){
        this.$emit("edit-basket",{idProduct:idProduct,inBasket:inBasket})
      }
    }
  }

</script>
<style scoped></style>