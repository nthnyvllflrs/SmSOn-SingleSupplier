<template>
    <v-container>
        <supplier-administrator v-if="userRole == 'Administrator'" />
        <supplier-customer v-if="userRole == 'Customer'" />
    </v-container>
</template>

<script>
    import SupplierAdministrator from '../administrator/SupplierComponent'
    import SupplierCustomer from '../customer/SupplierComponent'

    export default {
        data() {
            return {
                userRole: sessionStorage.getItem('user-role'),
            }
        },
        components: {
            'supplier-administrator' : SupplierAdministrator, 
            'supplier-customer' : SupplierCustomer
        }
    }
</script>