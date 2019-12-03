<template>
    <v-container>
        <supplier-product v-if="userRole == 'Supplier'" />
        <customer-product v-if="userRole == 'Customer'" />
    </v-container>
</template>

<script>
    import SupplierProduct from '../supplier/ProductComponent'
    import CustomerProduct from '../customer/ProductComponent'

    export default {
        data() {
            return {
                userRole: sessionStorage.getItem('user-role'),
            }
        },
        components: {
            'supplier-product' : SupplierProduct, 
            'customer-product' : CustomerProduct
        }

    }
</script>