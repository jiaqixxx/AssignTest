<?php
require('includes/application_top.php');
date_default_timezone_set('Australia/Sydney');
define('HEADING_TITLE', 'ASSIGN TESTING TASK');
?>


<!doctype html>
<html <?php echo HTML_PARAMS; ?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
    <title><?php echo HEADING_TITLE . ' | ' . TITLE; ?></title>
    <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons' rel="stylesheet">
    <link href="https://unpkg.com/vuetify@1.1.1/dist/vuetify.min.css" rel="stylesheet">
    <script src="https://unpkg.com/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/vuetify@1.1.1/dist/vuetify.min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/babel-polyfill/dist/polyfill.min.js"></script>
    <style>
        tr{
            height: 30px !important;
        }
        td{
            height: 35px !important;
        }
    </style>
</head>
<body>
    <div id="app" style="width: 100%; min-width: 1000px">
        <v-app id="inspire">
            <v-container grid-list-md text-xs-center style="max-width: 100%;">
                <v-card :height="350">
                    <v-layout row>
                        <v-flex xs12>
                            <h2 style="float: left; margin: 20px">Testing Assignments</h2>
                        </v-flex>
                    </v-layout>
                    <v-layout row>
                        <v-flex xs5>
                            <v-container style="padding-bottom: 0px; height: 80px;">
                                <v-layout row wrap>
                                    <v-flex xs7>
                                        <v-subheader style="margin-left: 50px">Who is testing today?</v-subheader>
                                    </v-flex>
                                    <v-flex d-flex xs5 md3>
                                        <v-select style="width: 150px;"
                                            v-model="agentDefault"
                                            :items="agents"
                                            item-text="admin_firstname"
                                            item-value="admin_id"
                                            solo
                                        ></v-select>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                            <v-container style="padding-top:0; padding-bottom: 0">
                                <v-layout row wrap>
                                    <v-flex xs7>
                                        <v-subheader style="margin-left: 50px">How many orders are they testing?</v-subheader>
                                    </v-flex>
                                    <v-flex d-flex xs5 md3>
                                        <v-text-field style="width: 150px;"
                                            v-model="numDefault[0]"
                                            class="mt-0"
                                            hide-details
                                            single-line
                                            type="number"
                                            :max="40"
                                            :min="1"
                                        ></v-text-field>
                                    </v-flex>
                                </v-layout>
                            </v-container>
                        </v-flex>
                        <v-flex xs2>
                            <div style="margin-top:40px">
                                <v-btn
                                    large
                                    color="primary"
                                    @click="assignTestOrders"
                                >Assign Test Orders</v-btn>
                            </div>
                        </v-flex>
                        <v-flex xs2></v-flex>
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
                    </v-layout>
                </v-card>
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
                            :pagination.sync="paginationSecond"
                    >
                        <template slot="items" slot-scope="props">
                            <td class="text-xs-left">{{ props.item.id }}</td>
                            <td class="text-xs-left">{{ props.item.admin_firstname }}</td>
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
                                <v-btn @click="checkCustomerDetailsInProgress(props.item)">View Customer Details</v-btn>
                                <v-dialog v-model="dialogCustomerDetailsInProgress" max-width="700px">
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
                                            <v-btn color="blue darken-1" flat @click.native="dialogCustomerDetailsInProgress = false">Close</v-btn>
                                        </v-card-actions>
                                    </v-card>
                                </v-dialog>
                            </td>
                            <td class="text-xs-center">
                                <p v-if="props.item.has_comments=='0'">No</p>
                                <b v-if="props.item.is_all_good=='1'" style="color: mediumseagreen">All Good</b>
                                <v-layout row justify-center v-if="props.item.has_comments=='1'">
                                    <v-btn small slot="activator" color="error" @click="getComments(props.item)">Check Comments</v-btn>
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
                                </v-layout>
                            </td>
                            <td class="text-xs-left">
                                <v-checkbox v-model="approveBox[props.item.id]"  @click.stop="approveAssignment(props.item)"></v-checkbox>
                            </td>
                        </template>
                    </v-data-table>
                    <div class="text-xs-center pt-2">
                        <v-pagination
                                v-model="paginationSecond.page"
                                :length="pagesSecond"
                        ></v-pagination>
                    </div>
                </v-card>
                <v-card style="margin-top: 20px">
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
                            :pagination.sync="paginationThird"
                    >
                        <template slot="items" slot-scope="props">
                            <td class="text-xs-left">{{ props.item.id }}</td>
                            <td class="text-xs-left">{{ props.item.admin_firstname }}</td>
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
                                <v-btn @click="checkCustomerDetailsApproved(props.item)">View Customer Details</v-btn>
                                <v-dialog v-model="dialogCustomerDetailsApproved" max-width="700px">
                                    <v-card>
                                        <v-card-title>
                                            <span class="headline">Customer Details</span>
                                        </v-card-title>
                                        <v-card-text>
                                            <div v-for="(value, key, index) in props.item.customer_details">
                                                <v-text-field
                                                       readonly
                                                       ref="input1"
                                                       class="key"
                                                       :label="key"
                                                       v-model="customerDetailsPopUp[key]"
                                                       append-icon='content_copy'
                                                       @click:append='copy1(index)'
                                                >
                                                </v-text-field>
                                            </div>
                                        </v-card-text>
                                        <v-divider></v-divider>
                                        <v-card-actions>
                                            <v-btn color="blue darken-1" flat @click.native="dialogCustomerDetailsApproved = false">Close</v-btn>
                                        </v-card-actions>
                                    </v-card>
                                </v-dialog>
                            </td>
                            <td class="text-xs-center">
                                <p v-if="props.item.has_comments=='0'">No</p>
                                <b v-if="props.item.is_all_good=='1'" style="color: mediumseagreen">All Good</b>
                                <v-layout row justify-center v-if="props.item.has_comments=='1'">
                                    <v-btn small slot="activator" color="error" @click="getCommentsApprove(props.item)">Check Comments</v-btn>
                                    <v-dialog v-model="dialogApprove" max-width="700px">
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
                                                <v-btn color="blue darken-1" flat @click.native="dialogApprove = false">Close</v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-dialog>
                                </v-layout>
                            </td>
                            <td class="text-xs-left">
                                <v-checkbox v-model="approveLogBox[props.item.id]" @change="unApproveAssignment(props.item)"></v-checkbox>
                            </td>
                        </template>
                    </v-data-table>
                    <div class="text-xs-center pt-2">
                        <v-pagination
                                v-model="paginationThird.page"
                                :length="pagesThird"
                        ></v-pagination>
                    </div>
                </v-card>
            </v-container>
        </v-app>
    </div>
    <script>
        new Vue({
            el: '#app',
            data: () => ({
                pagination: {
                    rowsPerPage: 5,
                    totalItems: 0
                },
                paginationSecond: {
                    rowsPerPage: 3,
                    totalItems: 0
                },
                paginationThird: {
                    rowsPerPage: 3,
                    totalItems: 0
                },
                numDefault: [5, 40],
                agentDefault: {},
                agents: [],
                headers: [
                    {text: 'Agent', align: 'left', value: 'agentName'},
                    {text: '# Assigned Before', align: 'center', value: 'numAssignments'},
                ],
                workloadSummary: [],
                assignmentAssignedHeader: [
                    {text: 'ID', align: 'left', value: 'id'},
                    {text: 'Assigned Agent', align: 'left', value: 'admin_firstname'},
                    {text: 'Environment', align: 'left', value: 'environment'},
                    {text: 'Order Number', align: 'left', value: 'order_items'},
                    {text: 'Product_look_up', align: 'left', value: 'product_look_up'},
                    {text: 'Customer Detail', align: 'left', value: 'customer_details'},
                    {text: 'Any Comments or Bugs to report', align: 'center', value: 'has_comments'},
                    {text: 'Approve?', align: 'left', value: 'is_approved'},
                ],
                assignmentsAssigned: [],
                dialog: false,
                dialogApprove: false,
                dialogCustomerDetailsApproved: false,
                dialogCustomerDetailsInProgress: false,
                customerDetailsPopUp: [],
                commentsHeader: [
                    {text: 'Comment', align: 'left', value: 'comment'},
                    {text: 'Uploaded File', align: 'left', value: 'uploaded_file'},
                ],
                comments: [],
                approveBox: [],
                approveLogBox: [],
                orderId: [],
                orderNum: [],
                customerDetails: [],
                bugs: [
                    '',
                    'Yes',
                    'No'
                ],
                filterBug: '',
                approvedLogs: [],
                approvedLogsHeader: [
                    {text: 'ID', align: 'left', value: 'id'},
                    {text: 'Assigned Agent', align: 'left', value: 'admin_firstname'},
                    {text: 'Environment', align: 'left', value: 'environment'},
                    {text: 'Order Number', align: 'left', value: 'order_items'},
                    {text: 'Product_look_up', align: 'left', value: 'product_look_up'},
                    {text: 'Customer Detail', align: 'left', value: 'customer_details'},
                    {text: 'Any Comments or Bugs to report', align: 'center', value: 'has_comments'},
                    {text: 'Approve?', align: 'left', value: 'is_approved'},
                ]
            }),
            created () {
                this.initialize();
                this.initializeSummaryTable();
                this.initializeInProgressAssignments();
                this.initializeApprovedLogs();
            },
            methods: {
                initialize(){
                    let app = this;
                    axios.get('assign_test_api.php?action=getAgents')
                        .then(function (response) {
                            if(response.data.result == 'Failed'){
                                alert(response.data.message);
                            }else{
                                app.agents = response.data;
                                app.agentDefault = response.data[0]['admin_id'];
                            }
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                },
                initializeSummaryTable(){
                    let app = this;
                    axios.get('assign_test_api.php?action=getAgentsWorkload')
                        .then(function (response) {
                            app.workloadSummary = response.data;
                            app.pagination.totalItems = response.data.length;
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                },
                initializeInProgressAssignments(){
                    let app = this;
                    axios.get('assign_test_api.php?action=getInProgressAssignments')
                        .then(function (response) {
                            app.assignmentsAssigned = response.data;
                            app.paginationSecond.totalItems = response.data.length;
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                },
                assignTestOrders(){
                    var agentId = this.agentDefault;
                    var numAssignments = this.numDefault[0];
                    if(numAssignments < 1){
                        alert('The number of testing assignments cannot be less than one');
                        return false;
                    }
                    const params = new URLSearchParams();
                    let app = this;
                    params.append('action', 'assignTestingOrders');
                    params.append('agentId', agentId);
                    params.append('numAssignments', numAssignments);
                    axios.post('assign_test_api.php', params)
                        .then(function (response) {
                            if(response.data.result == 'Failed'){
                                alert(response.data.message);
                            }else{
                                app.initializeSummaryTable();
                            }
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                },
                getComments(item){
                    var orderId = item.order_id;
                    let app = this;
                    axios.get('assign_test_api.php', {
                        params: {
                            'action': 'getComments',
                            'orderId': orderId
                        }
                    })
                        .then(function (response) {
                            app.comments = response.data;
                            app.dialog = true;
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                },
                getCommentsApprove(item){
                    var orderId = item.order_id;
                    let app = this;
                    axios.get('assign_test_api.php', {
                        params: {
                            'action': 'getComments',
                            'orderId': orderId
                        }
                    })
                        .then(function (response) {
                            app.comments = response.data;
                            app.dialogApprove = true;
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                },
                approveAssignment(item){
                    if(confirm('Are you sure to approve this assignment?')){
                        var assignmentId = item.id;
                        const params = new URLSearchParams();
                        let app = this;
                        params.append('action', 'approveAssignment');
                        params.append('assignmentId', assignmentId);
                        axios.post('assign_test_api.php', params)
                            .then(function (response) {
                                if (response.data.result == 'Failed') {
                                    alert(response.data.message);
                                } else {
                                    app.initializeInProgressAssignments();
                                    app.initializeApprovedLogs();
                                }
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    }
                },
                unApproveAssignment(item){
                    if(confirm('Are you sure to un-approve this assignment?')){
                        var assignmentId = item.id;
                        const params = new URLSearchParams();
                        let app = this;
                        params.append('action', 'unApproveAssignment');
                        params.append('assignmentId', assignmentId);
                        axios.post('assign_test_api.php', params)
                            .then(function (response) {
                                if (response.data.result == 'Failed') {
                                    alert(response.data.message);
                                } else {
                                    app.initializeInProgressAssignments();
                                    app.initializeApprovedLogs();
                                }
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    }
                },
                search(){
                    var id = this.orderId;
                    var orderNum = this.orderNum;
                    var customerDetails = this.customerDetails;
                    var bugs = this.filterBug;
                    if(id == '' && orderNum == '' && customerDetails == '' && bugs == ''){
                        this.initializeApprovedLogs();
                    }
                    let app = this;
                    axios.get('assign_test_api.php',{
                        params: {
                            'action': 'search',
                            'id': id,
                            'orderNum': orderNum,
                            'customerDetails': customerDetails,
                            'bugs': bugs
                        }
                    })
                        .then(function (response) {
                            app.approvedLogs = response.data;
                            app.paginationThird.totalItems = response.data.length;
                            response.data.forEach(function (value) {
                                app.approveLogBox[value['id']] = true;
                            });
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                },
                initializeApprovedLogs(){
                    let app = this;
                    axios.get('assign_test_api.php?action=getApprovedLogs')
                        .then(function (response) {
                            app.approvedLogs = response.data;
                            app.paginationThird.totalItems = response.data.length;
                            response.data.forEach(function (value) {
                               app.approveLogBox[value['id']] = true;
                            });
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
                copy1(index){
                    var input = this.$refs.input1[index];
                    input.focus();
                    document.execCommand('selectAll');
                    this.copied = document.execCommand('copy');
                },
                checkCustomerDetailsApproved(item){
                    this.customerDetailsPopUp = item.customer_details;
                    this.dialogCustomerDetailsApproved = true;
                },
                checkCustomerDetailsInProgress(item){
                    this.customerDetailsPopUp = item.customer_details;
                    this.dialogCustomerDetailsInProgress = true;
                }
            },
            computed: {
                pages () {
                    if (this.pagination.rowsPerPage == null ||
                        this.pagination.totalItems == null
                    ) return 0;
                    return Math.ceil(this.pagination.totalItems / this.pagination.rowsPerPage)
                },
                pagesSecond () {
                    if (this.paginationSecond.rowsPerPage == null ||
                        this.paginationSecond.totalItems == null
                    ) return 0;
                    return Math.ceil(this.paginationSecond.totalItems / this.paginationSecond.rowsPerPage)
                },
                pagesThird () {
                    if (this.paginationThird.rowsPerPage == null ||
                        this.paginationThird.totalItems == null
                    ) return 0;
                    return Math.ceil(this.paginationThird.totalItems / this.paginationThird.rowsPerPage)
                }
            }
        });
    </script>
</body>
</html>