<template>
    <div>
        <div class="pt-2">
            <router-link to="/order">Back</router-link>

        </div>
        <div class="pt-8 bg-white mt-8">

            <div class="title flex justify-between items-center">
                <h1 class="text-[20px] ml-4">Chỉnh sửa đơn hàng {{ order_code }}</h1>
                <p class="mr-2">Quy đổi: <b> 1kg = {{ formatPrice(globalFee) }}</b></p>
            </div>
            <a-row class="justify-between">
                <a-col :span="8">
                    <div style="background: #ffff; padding: 20px">
                        <a-card title="Thông tin chung" class="w-full">
                            <div class="w-full mb-3">
                                <label for="">Mã đặt hàng(taobao,1688)</label>
                                <a-input :disabled="disabled" v-model:value="this.order_code" class="w-full"
                                    placeholder="Mã đặt hàng" />
                            </div>
                            <div class="w-full mb-3">
                                <label for="">Mã vận đơn</label>
                                <a-input v-model:value="this.shippingcode" class="w-full" placeholder="Mã vận đơn" />
                            </div>
                            <!-- <div class="w-full mb-3">
                                <label for="">Link lấy mã vận đơn</label>
                                <a-input v-model:value="value" class="w-full" placeholder="Link lấy mã vận đơn" />
                            </div> -->
                        </a-card>
                    </div>
                </a-col>
                <a-col :span="8">
                    <div style="background: #ffff; padding: 20px">
                        <a-card title="Thông tin khác" class="w-[100%]">
                            <div class="w-[100%]">
                                <div class="mb-5">
                                    <label for="">Khối lượng</label>
                                    <br>
                                    <a-input-number addon-after="Kg" @blur="insertFeeShipGlobal(kg)" v-model:value="kg"
                                        addon-before="+"></a-input-number>
                                </div>
                                <div class="mb-5">
                                    <label for="">Thành tiền</label>
                                    <a-input addon-after="VNĐ" v-model:value="totalGlobalShipping"
                                        :formatter="totalGlobalShipping => `$ ${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                                        :parser="totalGlobalShipping => value.replace(/\$\s?|(,*)/g, '')" />
                                </div>
                                <div class="mb-5">
                                    <label for="">Số lượng khách muốn đặt hàng</label>
                                    <a-input v-model:value="quantityPurchasedCustomer" />
                                </div>
                                <div class="mb-5">
                                    <label for="">Số lượng mua được</label>
                                    <a-input v-model:value="quantityPurchased" />
                                </div>
                            </div>
                        </a-card>
                    </div>
                </a-col>
                <a-col :span="7">
                    <div style="background: #ffff; padding: 20px">
                        <a-card title="Dịch vụ" class="w-full">
                            <div class="mb-3">
                                <a-checkbox v-model:checked="option.order_checking">Đã kiểm hàng</a-checkbox>
                            </div>
                            <div class="mb-3">
                                <a-checkbox v-model:checked="option.inventory">Phí kiểm hàng</a-checkbox>
                            </div>
                            <div class="mb-3">
                                <a-checkbox v-model:checked="option.autoshipping">Tự động giao hàng</a-checkbox>
                            </div>
                            <div class="mb-3">
                                <a-radio-group v-model:value="woodpacking">
                                    <a-radio :style="radioStyle" :value="1">Phí đóng gỗ</a-radio>
                                    <a-radio :style="radioStyle" :value="0">Phí đóng gỗ riêng</a-radio>
                                </a-radio-group>
                            </div>

                            <div class="mb-3">
                                <a-radio-group v-model:value="shipping">
                                    <a-radio :style="radioStyle" :value="0">Giao hàng tiết kiệm</a-radio>
                                    <a-radio :style="radioStyle" :value="1">Giao hàng nhanh</a-radio>
                                </a-radio-group>
                            </div>


                        </a-card>
                    </div>
                </a-col>
            </a-row>
            <div class="title ml-4">
                <h1 class="text-[20px]">Phí ship theo shop</h1>
            </div>
            <a-row>

                <a-col :span="8" v-for="(item, index) in dataShop" :key="index">
                    <div style="background:#ffff;padding:20px">
                        <a-card :title="item.shop_name ? item.shop_name : item.shop_id" class="w-[100%]">
                            <div class="mb-3">
                                <label for="">Phí ship</label>
                                <CurrencyInput v-model="feeShipChina[index]" :options="{
                                    currency: 'VND',
                                    currencyDisplay: 'hidden',
                                    hideCurrencySymbolOnFocus: true,
                                    hideGroupingSeparatorOnFocus: false,
                                    hideNegligibleDecimalDigitsOnFocus: false,
                                }" class="w-full border-gray-300 rounded my-2 px-2 py-1"></CurrencyInput>
                            </div>
                        </a-card>
                    </div>
                </a-col>

            </a-row>

            <div class="flex justify-end mr-4 pb-3">

                <a-button @click="updateOrderPacket()" type="danger">Lưu thay đổi</a-button>
            </div>
        </div>
    </div>
</template>
<script>
import { defineComponent, reactive, ref } from 'vue';
import { getOrderUpdate, updateOrderPacket } from "../../../services/order/order.js";
import { message } from 'ant-design-vue';
import CurrencyInput from '../../format_curency/CurrencyInput.vue';

export default {
    watch: {
        $route: {
            immediate: true,
            handler(to, from) {
                document.title = 'Chỉnh sửa đơn hàng';
            }
        },
    },
    setup() {
        const value = ref(1);

        const radioStyle = reactive({
            display: 'flex',
            height: '30px',
            lineHeight: '30px',
        });
        return {
            radioStyle,
            value
        }

    },
    components: {
        CurrencyInput
    },

    data() {
        return {
            order_id: 0,
            data: [],
            order_code: 0,
            disabled: true,
            option: {
                order_checking: null,
                autoshipping: null,
                savingshipping: null,
                inventory: null,
                wood_packing: null,
                seperatewoodpacking: null,
                autoshipping: null,
                express_shipping: null
            },
            shipping: null,
            woodpacking: null,
            shippingcode: null,
            kg: null,
            cm3: null,
            totalGlobalShipping: null,
            quantityPurchased: null,
            quantityPurchasedCustomer: null,
            feeShippingChina: null,
            feeOrderInsurance: null,
            dataShop: [],
            feeShipChina: [],
            globalFee: 0
        }
    },
    created() {
        this.order_id = this.$route.params.id;
        this.getDetailCart();
    },
    methods: {
        formatPrice(value) {
            return new Intl.NumberFormat("en-US", {
                style: "currency",
                currency: "VND",
            }).format(value);
        },
        insertFeeShipGlobal(kg) {
            this.totalGlobalShipping = kg * +this.globalFee;
        },
        getDetailCart() {
            // key:''

            getOrderUpdate({ id: this.order_id }).then((response) => {
                this.data = response.data[0];
                this.dataShop = response.data[1];
                this.globalFee = response.data[2].value;
                // console.log(this.dataShop);
                this.dataShop.map((item, index) => {
                    this.feeShipChina[index] = item.fee_ship;
                })

                this.data.map((res) => {
                    this.order_code = res.order_code;
                    this.shippingcode = res.code;
                    this.option.order_checking = res.opt_order_checking == 1 ? true : false;
                    this.option.inventory = res.opt_inventory == 1 ? true : false;
                    this.option.autoshipping = res.opt_auto_shipping == 1 ? true : false;
                    this.option.wood_packing = res.opt_wood_packing;
                    this.option.seperatewoodpacking = res.opt_separete_wood_packing;
                    this.option.express_shipping = res.opt_express_shipping;
                    this.option.savingshipping = res.opt_saving_shipping;
                    this.shipping = this.option.express_shipping == 1 ? this.option.express_shipping : this.option.savingshipping;
                    this.woodpacking = this.option.wood_packing == 1 ? this.option.wood_packing : this.option.seperatewoodpacking;
                    this.kg = res.weight;
                    this.cm3 = res.volume;
                    this.quantityPurchased = res.quantitybuy;
                    this.quantityPurchasedCustomer = res.quantityreceive;
                    this.totalGlobalShipping = res.global_shipping_fee;

                })
            }).finally(() => {

            })
        },
        updateOrderPacket() {

            const key = 'updatable';
            message.loading({ content: 'Đang tải...', key });
            const params = {
                order_id: this.order_id,
                weight: this.kg,
                volume: this.cm3,
                quantity_buy: this.quantityPurchasedCustomer,
                quantity_receive: this.quantityPurchased,
                opt_order_checking: this.option.order_checking,
                opt_order_checking: this.option.order_checking,
                opt_auto_shipping: this.option.autoshipping,
                opt_saving_shipping: this.shipping == 0 ? 1 : 0,
                opt_express_shipping: this.shipping == 1 ? 1 : 0,
                opt_inventory: this.option.inventory,
                opt_wood_packing: this.woodpacking == 1 ? 1 : 0,
                opt_separate_wood_packing: this.woodpacking == 0 ? 1 : 0,
                code: this.shippingcode,
                fee_ship: this.feeShipChina,
                global_shipping_fee: this.totalGlobalShipping
            }
            updateOrderPacket(params).then((response) => {
                console.log(response);
                message.success({ content: 'Cập nhật đơn hàng thành công!', key, duration: 2 });

            }).finally(() => {

            });
        },
    }
}
</script>
<style>

</style>