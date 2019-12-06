<template>
    <v-container>
        <supplier-order-request v-if="userRole == 'Supplier'" />
        <customer-order-request v-if="userRole == 'Customer'" />
    </v-container>
</template>

<script>
    import SupplierOrderRequest from '../supplier/OrderRequestComponent'
    import CustomerOrderRequest from '../customer/OrderRequestComponent'

    export default {
        data() {
            return {
                userRole: sessionStorage.getItem('user-role'),
            }
        },
        components: {
            'supplier-order-request' : SupplierOrderRequest, 
            'customer-order-request' : CustomerOrderRequest
        }
    }
</script>