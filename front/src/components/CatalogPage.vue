<template>
    <div class="container">
        <div class="row">
            <label>Курс</label>
            <input class="form-control" v-model="usd_rub">
        </div>
        <div class="row">
            <div class="col col-md-6 col-lg-6">

                <div v-for="(category, index) in getGoods" :key="index">
                    <table class="table">
                        <thead>
                        <tr>
                            <td>{{ category.G }}</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, index) in category.B" :key="index" :class="item.data.class">
                            <td class="col col-10">{{ item.data.T + ' ' + item.N }} ({{ item.data.P }}) {{item.class}}</td>
                            <td class="col col-1">{{ item.data.C_R }}</td>
                            <td class="col col-1">
                                <button class="btn btn-sm btn-outline-primary" @click="addTocart(item.data.T)">ADD
                                </button>
                            </td>

                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col col-md-6 col-lg-6">
                <div>Cart</div>
                <div v-if="cart.length > 0">
                    <table class="table">
                        <tbody>
                            <tr v-for="(item) in cart" :key="item.id">
                                <td>{{item.name}}</td>
                                <td>{{item.C_R}}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-danger" @click="removeFromCart(item.T)">REMOVE
                                    </button>
                                </td>
                            </tr>
                        <tr>
                            <td>Сумма</td>
                            <td>{{ calculateCart }}</td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</template>

<script>

import Axios from 'axios';

export default {
    name: "catalogPage",
    data() {
        return {
            usd_rub: 70,
            goods: [],
            names: '',
            cart: []
        };
    },
    computed: {
        calculateCart(){
            let summ = 0;
            this.cart.map((item) => summ += Number(item.C_R));
            return summ.toFixed(2);
        },
        getGoods() {
            /*            let items = this.names.map((name) => {
                            console.log(name);
                        })*/
            let categorys = this.names;
            for (let index in categorys) {
                for (let item_index in categorys[index].B) {
                    categorys[index].B[item_index].data = this.goods.filter((good) => {
                        return good.T == item_index;
                    })[0]
                    if (!categorys[index].B[item_index].data) {
                        delete categorys[index].B[item_index];
                    } else {
                        categorys[index].B[item_index].data.C_R = (categorys[index].B[item_index].data.C * this.usd_rub).toFixed(2)
                    }
                }
            }
            return categorys;
        },
    },
    methods: {
        removeFromCart(id){
            this.cart = this.cart.filter((item) => {
                return item.T != id;
            })
        },
        addTocart(id) {
            let item = this.goods.filter((goog) => {
                return goog.T == id;
            })[0];
            item.name = this.names[item.G].B[id].N;
            this.cart.push(item);
        },
        async getData() {
            await Axios.get('/database/index.php?get_data')
                .then((response) => {
                    let new_goods = response.data.Value.Goods.map((good) => {
                        good.class = '';
                        if (this.goods.length != 0){
                            let old_item = this.goods.filter((old_good) => {
                                return old_good.T == good.T;
                            })[0];
                            if (old_item.C.toFixed(2) > good.C.toFixed(2)){
                                good.class = 'bg-success';
                            } else if (old_item.C.toFixed(2) < good.C.toFixed(2)){
                                good.class = 'bg-danger';
                            }
                        }
                        return good;
                    });
                    this.goods = new_goods;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        async getName() {
            await Axios.get('/database/index.php?get_names')
                .then((response) => {
                    console.log('api leads', response);
                    this.names = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        }
    },
    mounted() {
        this.getData();
        this.getName();
        setInterval(this.getData, 5000);
    }
}
</script>

<style scoped>

</style>