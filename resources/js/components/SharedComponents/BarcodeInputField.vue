<template>
    <div>
        <input ref="barcode" class="form-control" :placeholder="placeholder"
               v-model="barcode"
               @focus="simulateSelectAll"
               @keyup.enter="barcodeScanned(barcode)"/>
    </div>
</template>

<script>
    import VueObserveVisibilityPlugin from 'vue-observe-visibility';
    import helpers from "../../mixins/helpers";

    export default {
        name: "BarcodeInputField",

        mixins: [helpers],

        props: {
            placeholder: '',
        },

        data: function() {
            return {
                currentLocation: '',
                barcode: '',
            }
        },

        mounted() {
            setTimeout(() => { this.setFocusOnBarcodeInput() }, 500);
        },

        created() {
        },

        methods: {
            barcodeScanned(barcode) {
                this.$emit('barcodeScanned', barcode);
                this.setFocusOnBarcodeInput();
                this.simulateSelectAll();
            },

            setFocusOnBarcodeInput() {
                this.setFocus(this.$refs.barcode, true,true)
            },

            simulateSelectAll() {
                setTimeout(() => { document.execCommand('selectall', null, false); });
            },

        }
    }
</script>

<style scoped>

</style>
