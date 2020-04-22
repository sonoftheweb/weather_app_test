<template>
    <v-menu
        v-model="trigger_cal"
        :close-on-content-click="false"
        :nudge-right="40"
        transition="scale-transition"
        offset-y
        min-width="290px"
    >
        <template v-slot:activator="{ on }">
            <v-text-field
                v-model="date"
                :label="label"
                outlined
                dense
                readonly
                v-on="on"
            ></v-text-field>
        </template>
        <v-date-picker v-model="date" @input="trigger_cal = false"></v-date-picker>
    </v-menu>
</template>

<script>
    export default {
        props: ["label", "value"],
        data() {
            return {
                trigger_cal: false,
                date: null
            }
        },
        watch: {
            date() {
                this.$emit('date_selected', this.date);
            }
        },
        mounted() {
            this.date = this.value;
        }
    }
</script>
