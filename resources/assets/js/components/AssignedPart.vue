<template>
    <div>
        <notifications group="approveAssignment" position="top center"/>
        <v-dialog v-model="dialog" max-width="900px">
            <v-card>
                <v-card-title>
                    <span class="headline">Comments/Bugs</span>
                </v-card-title>
                <v-card-text>
                    <vue-editor
                            id="editor"
                            v-model="comments"
                    >
                    </vue-editor>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn color="blue darken-1" flat @click.native="dialog = false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-dialog v-model="dialogCustomerDetails" max-width="700px">
            <v-card>
                <v-card-title>
                    <span class="headline">Customer Details</span>
                </v-card-title>
                <v-card-text>
                    <div v-for="(value, key, index) in customerDetailsPopUp">
                        <v-text-field
                                readonly
                                ref="input"
                                class="key"
                                :label="key"
                                v-model="customerDetailsPopUp[key]"
                                append-icon='content_copy'
                                @click:append='copy(index)'
                        >
                        </v-text-field>
                    </div>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn color="blue darken-1" flat @click.native="dialogCustomerDetails = false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-card style="margin-top:20px;">
            <v-layout row>
                <v-flex xs12>
                    <h3 style="float: left; margin: 20px">Assigned - Testing-In-Progress</h3>
                </v-flex>
            </v-layout>
            <v-data-table
                    :items="assignmentsAssigned"
                    class="elevation-1"
                    hide-actions
                    :headers="assignmentAssignedHeader"
            >
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left">{{ props.item.id }}</td>
                    <td class="text-xs-left">{{ props.item.name }}</td>
                    <td class="text-xs-left">
                        <p v-for="environment in props.item.environment" style="margin-bottom: 0;">
                            {{ environment }}
                        </p>
                    </td>
                    <td class="text-xs-left">
                        <p style="margin-bottom: 0;">
                            {{ props.item.order_id }}
                        </p>
                        <b style="margin-bottom: 0">Items: </b>
                        <div v-for="item in props.item.order_items">
                            <p v-for="(value, key) in item" style="margin-bottom: 0;">
                                {{ key }}: {{ value }}
                            </p>
                        </div>
                    </td>
                    <td class="text-xs-left">
                        <p v-for="product_look_up in props.item.product_look_up" style="margin-bottom: 0;">
                            {{ product_look_up }}
                        </p>
                    </td>
                    <td class="text-xs-left">
                        <v-btn @click="checkCustomerDetails(props.item)">View Customer Details</v-btn>
                    </td>
                    <td class="text-xs-center">
                        <p v-if="props.item.has_comments=='0'">No</p>
                        <b v-if="props.item.is_all_good=='1'" style="color: mediumseagreen">All Good</b>
                        <v-layout row justify-center v-if="props.item.has_comments=='1'">
                            <v-btn small slot="activator" color="error" @click="getComments(props.item)">Check Comments</v-btn>
                        </v-layout>
                    </td>
                    <td class="text-xs-center">
                        <v-checkbox v-model="checkbox"  @click.stop="approveAssignment(props.item, props.index)"></v-checkbox>
                    </td>
                </template>
            </v-data-table>
            <div class="text-xs-center pt-2">
                <v-pagination
                        v-model="pagination.page"
                        :length="pages"
                        :total-visible="7"
                        @input="next"
                ></v-pagination>
            </div>
        </v-card>
    </div>
</template>

<script>
    import {EventBus} from "../app";
    import { VueEditor } from "vue2-editor";
    export default {
        data: () => ({
            pagination: {
                page: 1,
                rowsPerPage: 3,
                totalItems: 0
            },
            assignmentAssignedHeader: [
                {text: 'ID', align: 'left', value: 'id'},
                {text: 'Assigned Agent', align: 'left', value: 'assignee'},
                {text: 'Environment', align: 'left', value: 'environment', width: 15},
                {text: 'Order Number', align: 'left', value: 'name'},
                {text: 'Product_look_up', align: 'left', value: 'product_look_up'},
                {text: 'Customer Detail', align: 'left', value: 'customer_details'},
                {text: 'Any Comments or Bugs to report', align: 'center', value: 'has_comments'},
                {text: 'Approve?', align: 'left', value: 'is_approved'},
            ],
            assignmentsAssigned: [],
            dialog: false,
            dialogCustomerDetails: false,
            customerDetailsPopUp: [],
            comments: '',
            checkbox: false
        }),
        components: {
            VueEditor
        },
        created () {
            EventBus.$on(['updateWorkload', 'unSetAssignment', 'updateInProgress'], this.initialize);
            this.initialize();
        },
        methods: {
            initialize(){
                let app = this;
                axios.get('assignments/status', {
                    params: {
                        status: 0,
                        page: 1
                    }
                })
                    .then(function (response) {
                        app.assignmentsAssigned = response.data.assignments;
                        app.pagination.totalItems = response.data.count;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            getComments(item){
                var assignmentId = item.id;
                let app = this;
                axios.get('comments/' + assignmentId)
                    .then(function (response) {
                        app.comments = response.data[0]['comment'];
                        app.dialog = true;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            approveAssignment(item, index){
                if(confirm('Are you sure to approve this assignment?')){
                    var assignmentId = item.id;
                    let app = this;
                    const params = new URLSearchParams();
                    params.append('status', '1');
                    params.append('assignment_id', assignmentId);
                    axios.put('assignments', params)
                        .then(function (response) {
                            if (response.data.result == 'Failed') {
                                app.$notify({
                                    group: 'approveAssignment',
                                    text: response.data.message,
                                    duration: 5000,
                                    type: 'warn'
                                });
                            } else {
                                app.$notify({
                                    group: 'approveAssignment',
                                    text: 'Assignment approved',
                                    duration: 5000,
                                    type: 'success'
                                });
                                app.initialize();
                                EventBus.$emit('approveAssignment');
                            }
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                }
            },
            checkCustomerDetails(item){
                this.dialogCustomerDetails = false;
                if(item.customer_details == null){
                    this.$notify({
                        group: 'commentsSave',
                        text: 'Customer Details is not available',
                        duration: 5000,
                        type: 'warn'
                    });
                    return false;
                }else{
                    this.customerDetailsPopUp = item.customer_details;
                    this.dialogCustomerDetails = true;
                }
            },
            copy(index) {
                var input = this.$refs.input[index];
                input.focus();
                document.execCommand('selectAll');
                this.copied = document.execCommand('copy');
            },
            next(page){
                let app = this;
                axios.get('assignments/status', {
                    params: {
                        status: 0,
                        page: page
                    }
                })
                    .then(function (response) {
                        app.assignmentsAssigned = response.data.assignments;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        },
        computed: {
            pages () {
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