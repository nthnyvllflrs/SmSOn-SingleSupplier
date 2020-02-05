<template>
    <v-container>
        <supplier-manifest v-if="userRole == 'Administrator'" />
        <logistic-manifest v-if="userRole == 'Logistic'" />
    </v-container>
</template>

<script>
    import SupplierManifest from '../supplier/ManifestComponent'
    import LogisticManifest from '../logistic/ManifestComponent'

    export default {
        data() {
            return {
                userRole: sessionStorage.getItem('user-role'),
            }
        },
        components: {
            'supplier-manifest' : SupplierManifest, 
            'logistic-manifest' : LogisticManifest
        }
    }
</script>