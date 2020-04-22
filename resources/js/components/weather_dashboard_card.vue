<template>
    <div>
        <weather_notifications/>
        <v-card flat>
            <v-toolbar
                color="primary"
                dark
                extended
                flat
            >
                <v-app-bar-nav-icon></v-app-bar-nav-icon>
            </v-toolbar>

            <v-card
                class="mx-auto"
                max-width="80%"
                style="margin-top: -64px;"
            >
                <v-toolbar flat>
                    <v-toolbar-title class="grey--text">Weather Application</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="showAdd">
                        <v-icon>mdi-playlist-plus</v-icon>
                    </v-btn>
                    <v-btn icon @click.stop="showFilter = true">
                        <v-icon>mdi-filter-variant</v-icon>
                    </v-btn>
                    <v-btn icon>
                        <v-icon>mdi-dots-vertical</v-icon>
                    </v-btn>
                </v-toolbar>
                <v-divider></v-divider>
                <v-data-table
                    :loading="loading"
                    :headers="headers"
                    :items="weatherData"
                    :items-per-page="10"
                    class="elevation-1"
                >
                    <template v-slot:item.actions="{ item }">
                        <v-icon small @click="showUpdate(item)">
                            mdi-pencil-outline
                        </v-icon>
                        <v-icon small @click="deleteItem(item)">
                            mdi-delete
                        </v-icon>
                    </template>
                </v-data-table>
            </v-card>
            <v-dialog
                v-model="showFilter"
                max-width="600"
            >
                <v-card>
                    <v-card-title>
                        Filter results
                    </v-card-title>
                    <v-card-text class="mb-0 pb-0">
                        <div>
                            By date
                            <v-row>
                                <v-col md="6">
                                    <date_selector label="Start Date" @date_selected="filterParams.start = $event"/>
                                </v-col>
                                <v-col md="6">
                                    <date_selector label="End Date" @date_selected="filterParams.end = $event"/>
                                </v-col>
                            </v-row>
                        </div>
                        <div>
                            Location
                            <v-row>
                                <v-col md="6">
                                    <v-text-field
                                        label="Latitude"
                                        dense
                                        outlined
                                        v-model="filterParams.lat"
                                    ></v-text-field>
                                </v-col>
                                <v-col md="6">
                                    <v-text-field
                                        label="Longitude"
                                        dense
                                        outlined
                                        v-model="filterParams.lon"
                                    ></v-text-field>
                                </v-col>
                            </v-row>

                        </div>
                    </v-card-text>
                    <v-card-actions class="px-7 pb-7">
                        <v-btn @click="fetchData" color="info" small>Filter</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="addRecord" max-width="600">
                <v-card>
                    <v-card-title>
                        Add new record
                    </v-card-title>
                    <v-card-text>
                        <v-form ref="form">
                            <date_selector :value="recordData.date" label="Date" @date_selected="recordData.date = $event"/>
                            <v-text-field
                                label="Latitude"
                                outlined
                                dense
                                v-model="recordData.location.lat"
                                :rules="rules"
                            ></v-text-field>
                            <v-text-field
                                label="Longitude"
                                outlined
                                dense
                                v-model="recordData.location.lon"
                                :rules="rules"
                            ></v-text-field>
                            <v-text-field
                                label="City"
                                outlined
                                dense
                                v-model="recordData.location.city"
                                :rules="rules"
                            ></v-text-field>
                            <v-text-field
                                label="State"
                                outlined
                                dense
                                v-model="recordData.location.state"
                                :rules="rules"
                            ></v-text-field>
                            <v-textarea
                                outlined
                                name="temperatures"
                                label="Temperatures"
                                v-model="recordData.temperature"
                                :rules="rules"
                            ></v-textarea>
                        </v-form>
                        <v-card-actions>
                            <v-btn color="info" v-if="!isUpdate" @click="saveNewData()">Submit</v-btn>
                            <v-btn color="info" v-else @click="saveNewData(isUpdate)">Submit</v-btn>
                        </v-card-actions>
                    </v-card-text>
                </v-card>
            </v-dialog>
        </v-card>
    </div>
</template>

<script>
    import weather_notifications from "./utils/weather_notifications";
    import date_selector from "./utils/date_selector";

    export default {
        components: {
            weather_notifications,
            date_selector
        },
        data () {
            return {
                url: '/api/weather',
                loading: false,
                filterModal: true,
                headers: [
                    {
                        text: 'City',
                        align: 'start',
                        sortable: true,
                        value: 'city',
                    },
                    {
                        text: 'Date',
                        align: 'start',
                        sortable: true,
                        value: 'date',
                    },
                    {
                        text: 'State',
                        align: 'start',
                        sortable: true,
                        value: 'state',
                    },
                    {
                        text: 'Longitude',
                        align: 'start',
                        sortable: true,
                        value: 'lon',
                    },
                    {
                        text: 'Latitude',
                        align: 'start',
                        sortable: true,
                        value: 'lat',
                    },
                    {
                        text: 'Temperature Range',
                        align: 'start',
                        sortable: false,
                        value: 'temperatures',
                    },
                    { text: 'Actions', value: 'actions', sortable: false },
                ],
                weatherData: [],
                datemenu: false,
                addRecord: false,
                isUpdate: null,
                recordData: {
                    date: null,
                    location: {
                        lat: null,
                        lon: null,
                        city: null,
                        state: null,
                    },
                    temperature: []
                },
                showFilter: false,
                filterParams: {
                    start: null,
                    end: null,
                    lat: null,
                    lon: null
                },
                rules: [
                    (v) => !!v || 'Field is required!',
                ],
                filter_datemenu: {
                    start: false,
                    end: false
                }
            }
        },
        methods: {
            fetchData() {
                this.showFilter = false;
                this.loading = true;
                this.url = '/api/weather';
                this.modifyUrl();
                axios.get(this.url)
                .then(response => {
                    this.mutateData(response.data);
                    this.loading = false;
                }).catch(error => {
                    bus.$emit('show_notifications', error);
                });
            },
            mutateData(data) {
                this.weatherData = [];
                for (let i = 0; i < data.length; i++) {
                    let d = data[i];
                    d.temperature.sort();

                    this.weatherData.push({
                        id: d.id,
                        date: d.date,
                        city: d.location.city,
                        state: d.location.state,
                        lon: d.location.lon,
                        lat: d.location.lat,
                        temperatures: `${d.temperature[0]} to ${d.temperature[d.temperature.length - 1]}`,
                        temperature: d.temperature
                    });
                }
            },
            modifyUrl() {
                //fall back
                if (!this.filterParams.end && !this.filterParams.start && !this.filterParams.lon, !this.filterParams.lat)
                    this.url = '/api/weather';
                if (this.filterParams.start)
                    this.appendUrl('start', this.filterParams.start);
                if (this.filterParams.end)
                    this.appendUrl('end', this.filterParams.end);
                if (this.filterParams.lon)
                    this.appendUrl('lon', this.filterParams.lon);
                if (this.filterParams.lat)
                    this.appendUrl('lat', this.filterParams.lat);
            },
            appendUrl(param, value) {
                let sep = this.url.indexOf("?") === -1 ? "?" : "&";
                this.url = this.url + sep + param + "=" + value;
            },
            deleteItem(item) {
                this.loading = true;
                axios.delete(`/api/weather/${item.id}`)
                    .then(response => {
                        this.fetchData();
                    }).catch(error => {
                        bus.$emit('show_notifications', error); // I could do this in an interceptor though so I do not have to repeatedly catch all errors from axios
                    });
            },
            saveNewData(id) {
                this.loading = true;
                this.recordData.temperature = this.recordData.temperature.split(',');
                let url = (id) ? `/api/weather/${id}` : '/api/weather/'
                axios.post(url, this.recordData)
                    .then(response => {
                        this.addRecord = false;
                        bus.$emit('show_notifications', response.data);
                        this.reset();
                        if (id) {
                            this.successful();
                        }
                        this.fetchData();
                    }).catch(error => {
                        bus.$emit('show_notifications', error);
                    });
            },
            reset() {
                this.recordData = {
                    date: null,
                        location: {
                        lat: null,
                            lon: null,
                            city: null,
                            state: null,
                    },
                    temperature: []
                };
            },
            showAdd() {
                this.reset();
                this.addRecord = true;
            },
            showUpdate(item) {
                this.addRecord = true;
                this.isUpdate = item.id;
                this.recordData = {
                    date: item.date,
                    location: {
                        lat: item.lat,
                        lon: item.lon,
                        city: item.city,
                        state: item.state,
                    },
                    temperature: item.temperature.join(',')
                };
            },
            successful() {
                bus.$emit('show_notifications', {
                    status: 'success',
                    message: 'Operation successful'
                });
            }
        },
        mounted() {
            this.fetchData();
        }
    }
</script>
