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
                <td style="text-align: left">{{ props.item.agentName }}</td>
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
    export default {
        data() {
            return {
                headers: [
                    {text: 'Agent', align: 'left', value: 'agentName'},
                    {text: '# Assigned Before', align: 'center', value: 'numAssignments'},
                ],
                workloadSummary: [],
                pagination: {
                    rowsPerPage: 5,
                    totalItems: 0
                }
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