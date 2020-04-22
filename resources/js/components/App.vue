<template>
    <v-app>
        <v-content class="pa-0 ma-0">
            <v-container fluid>
                <weather_dashboard_card/>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
    import weather_dashboard_card from "./weather_dashboard_card";

    export default {
        components: {
            weather_dashboard_card
        }
    }
</script>
