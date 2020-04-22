<template>
    <v-snackbar v-model="show" :timeout="timeout" :top="true" :color="status">
        {{ text }}
        <v-btn text @click="show = false">
            Close
        </v-btn>
    </v-snackbar>
</template>

<script>
    export default {
        data() {
            return {
                show: false,
                text: null,
                status: 'success',
                timeout: 5000
            }
        },
        mounted() {
            bus.$on('show_notifications', (ev) => {
                this.text = ev.message;
                this.status = ev.status;
                this.show = true;
            })
        }
    }
</script>
