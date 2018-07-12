<template>
    <v-flex xs3>
        <v-data-table
                :headers="headers"
                :items="workloadSummary"
                hide-actions
                class="elevation-1"
                :pagination.sync="pagination"
        >
            <template slot="items" slot-scope="props">
                <td style="text-align: left">{{ props.item.name }}</td>
                <td style="text-align: center">{{ props.item.numAssignments }}</td>
            </template>
        </v-data-table>
        <div class="text-xs-center pt-2">
            <v-pagination
                    v-model="pagination.page"
                    :length="pages"
            ></v-pagination>
        </div>
    </v-flex>
</template>

<script>
    import {EventBus} from "../app";
    export default {
        data() {
            return {
                headers: [
                    {text: 'Agent', align: 'left', value: 'name'},
                    {text: '# Assigned Before', align: 'center', value: 'numAssignments'},
                ],
                workloadSummary: [],
                pagination: {
                    rowsPerPage: 5,
                    totalItems: 0
                }
            }
        },
        created () {
            EventBus.$on('updateWorkload', this.initialize);
            this.initialize();
        },
        methods: {
            initialize(){
                let app = this;
                axios.get('workload')
                    .then(function (response) {
                        app.workloadSummary = response.data;
                        app.pagination.totalItems = response.data.length;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        },
        computed: {
            pages() {
                if (this.pagination.rowsPerPage == null ||
                    this.pagination.totalItems == null
                ) return 0;
                return Math.ceil(this.pagination.totalItems / this.pagination.rowsPerPage)
            }
        }
    }
</script>

<style scoped>

</style>