<template>
    <div>
        <v-dialog v-model="dialog" max-width="700px">
            <v-card>
                <v-card-title>
                    <span class="headline">Comments/Bugs</span>
                </v-card-title>
                <v-card-text>
                    <v-data-table
                            :items="comments"
                            class="elevation-1"
                            hide-actions
                            :headers="commentsHeader"
                    >
                        <template slot="items" slot-scope="props">
                            <td class="text-xs-left">{{ props.item.comment }}</td>
                            <td class="text-xs-left"><a href="props.item.uploaded_file" target="_blank">Check File</a></td>
                        </template>
                    </v-data-table>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn color="blue darken-1" flat @click.native="dialog = false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-layout row>
            <v-flex xs12>
                <h3 style="float: left; margin: 20px">Approved Log</h3>
            </v-flex>
        </v-layout>
        <v-layout row>
            <v-flex xs10 offset-xs1>
                <v-layout row>
                    <v-flex xs2>
                        <v-text-field style="width:100px"
                                      v-model="orderId"
                                      label="ID"
                        ></v-text-field>
                    </v-flex>
                    <v-flex xs2>
                        <v-text-field style="width:150px"
                                      v-model="orderNum"
                                      label="Order Number"
                        ></v-text-field>
                    </v-flex>
                    <v-flex xs2>
                        <v-text-field style="width:180px"
                                      v-model="customerDetails"
                                      label="Customer Details"
                        ></v-text-field>
                    </v-flex>
                    <v-flex xs2>
                        <div style="display: inline-block; margin-top: 15px"><p>Bugs?</p></div>
                        <v-select style="width: 150px; float: right"
                                  v-model="filterBug"
                                  :items="bugs"
                                  item-text="text"
                                  item-value="value"
                                  solo
                        ></v-select>
                    </v-flex>
                    <v-flex xs2>
                        <v-btn color="primary" style="float:right" @click="search">Search</v-btn>
                    </v-flex>
                </v-layout>
            </v-flex>
        </v-layout>
        <v-data-table
                :items="approvedLogs"
                class="elevation-1"
                hide-actions
                :headers="approvedLogsHeader"
                :pagination.sync="pagination"
        >
            <template slot="items" slot-scope="props">
                <td class="text-xs-left">{{ props.item.id }}</td>
                <td class="text-xs-left">{{ props.item.name }}</td>
                <td class="text-xs-left">{{ props.item.environment }}</td>
                <td class="text-xs-left">
                    <p style="margin-bottom: 0;">
                        {{ props.item.order_id }}
                    </p>
                    <b style="margin-bottom: 0">Items: </b>
                    <p v-for="item in props.item.order_items" style="margin-bottom: 0;">
                        {{ item }}
                    </p>
                </td>
                <td class="text-xs-left">{{ props.item.product_look_up }}</td>
                <td class="text-xs-left">
                    <v-btn @click="checkCustomerDetails(props.item)">View Customer Details</v-btn>
                    <v-dialog v-model="dialogCustomerDetails" max-width="700px">
                        <v-card>
                            <v-card-title>
                                <span class="headline">Customer Details</span>
                            </v-card-title>
                            <v-card-text>
                                <div v-for="(value, key, index) in props.item.customer_details">
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
                </td>
                <td class="text-xs-center">
                    <b v-if="props.item.is_all_good=='1'" style="color: mediumseagreen">All Good</b>
                    <p v-if="props.item.has_comments=='0'">No</p>
                    <v-layout row justify-center v-if="props.item.has_comments=='1'">
                        <v-btn small slot="activator" color="error" @click="getComments(props.item)">Check Comments</v-btn>
                    </v-layout>
                </td>
                <td class="text-xs-left" v-if="props.item.is_approved==1">
                    <v-checkbox v-model="checkbox" @click.stop="unSetAssignment(props.item)"></v-checkbox>
                </td>
                <td class="text-xs-left" v-else="props.item.is_approved===0">
                    <v-checkbox @click.stop="approveAssignment(props.item)"></v-checkbox>
                </td>
            </template>
        </v-data-table>
        <div class="text-xs-center pt-2">
            <v-pagination
                    v-model="pagination.page"
                    :length="pages"
            ></v-pagination>
        </div>
    </div>
</template>

<script>
    import {EventBus} from "../app";

    export default {
        data () {
            return {
                pagination: {
                    rowsPerPage: 3,
                    totalItems: 0
                },
                dialog: false,
                commentsHeader: [
                    {text: 'Comment', align: 'left', value: 'comment'},
                    {text: 'Uploaded File', align: 'left', value: 'uploaded_file'},
                ],
                comments: [],
                dialogCustomerDetails: false,
                customerDetailsPopUp: [],
                checkbox: true,
                orderId: [],
                orderNum: [],
                customerDetails: [],
                bugs: [
                    {text: '', value: ''},
                    {text: 'No', value: '0'},
                    {text: 'Yes', value: '1'}
                ],
                filterBug: '',
                approvedLogs: [],
                approvedLogsHeader: [
                    {text: 'ID', align: 'left', value: 'id'},
                    {text: 'Assigned Agent', align: 'left', value: 'name'},
                    {text: 'Environment', align: 'left', value: 'environment'},
                    {text: 'Order Number', align: 'left', value: 'order_items'},
                    {text: 'Product_look_up', align: 'left', value: 'product_look_up'},
                    {text: 'Customer Detail', align: 'left', value: 'customer_details'},
                    {text: 'Any Comments or Bugs to report', align: 'center', value: 'has_comments'},
                    {text: 'Approve?', align: 'left', value: 'is_approved'},
                ]
            }
        },
        created () {
            EventBus.$on('approveAssignment', this.initialize);
            this.initialize();
        },
        methods: {
            initialize(){
                let app = this;
                axios.get('api/assignments/1')
                    .then(function (response) {
                        app.approvedLogs = response.data;
                        app.pagination.totalItems = response.data.length;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            getComments(item){
                var assignmentId = item.id;
                let app = this;
                axios.get('api/comments/' + assignmentId)
                    .then(function (response) {
                        app.comments = response.data;
                        app.dialog = true;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            unSetAssignment(item){
                if(confirm('Are you sure to un-approve this assignment?')){
                    var assignmentId = item.id;
                    let app = this;
                    const params = new URLSearchParams();
                    params.append('status', '0');
                    params.append('assignment_id', assignmentId);
                    axios.post('api/assignments', params)
                        .then(function (response) {
                            if (response.data.result == 'Failed') {
                                alert(response.data.message);
                            } else {
                                app.initialize();
                                EventBus.$emit('unSetAssignment');
                            }
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                }
            },
            approveAssignment(item){
                if(confirm('Are you sure to approve this assignment?')){
                    var assignmentId = item.id;
                    let app = this;
                    const params = new URLSearchParams();
                    params.append('status', '1');
                    params.append('assignment_id', assignmentId);
                    axios.post('api/assignments', params)
                        .then(function (response) {
                            if (response.data.result == 'Failed') {
                                alert(response.data.message);
                            } else {
                                app.initialize();
                                EventBus.$emit('updateInProgress');
                            }
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                }
            },
            checkCustomerDetails(item){
                this.customerDetailsPopUp = item.customer_details;
                this.dialogCustomerDetails = true;
            },
            search(){
                var id = this.orderId;
                var orderNum = this.orderNum;
                var customerDetails = this.customerDetails;
                var bugs = this.filterBug;
                if(id == '' && orderNum == '' && customerDetails == '' && bugs == ''){
                    this.initialize();
                    return false;
                }
                let app = this;
                axios.get('api/search',{
                    params:{
                        id: id,
                        order_id: orderNum,
                        customer_details: customerDetails,
                        bugs: bugs
                    }
                })
                    .then(function (response) {
                        if(response.data.result == 'Failed'){
                            alert(response.data.message);
                            return false;
                        }
                        app.approvedLogs = response.data;
                        app.pagination.totalItems = response.data.length;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            copy(index) {
                var input = this.$refs.input[index];
                input.focus();
                document.execCommand('selectAll');
                this.copied = document.execCommand('copy');
            },
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