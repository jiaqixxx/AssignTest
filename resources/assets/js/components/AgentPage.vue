<template>
    <v-app id="inspire">
        <notifications group="commentsSave" position="top center"/>
        <v-dialog
            v-model="commentsDialog"
            width="900"
        >
            <v-card>
                <v-card-title
                    class="headline grey lighten-2"
                    primary-title
                >
                    Comments/Bug Report
                </v-card-title>
                <v-card-text>
                    <p style="font-style:italic">Please be descriptive as possible when reporting bugs. Use screenshots/recordings where appropriate</p>
                    <vue-editor
                            id="editor"
                            v-model="content"
                            useCustomImageHandler
                            @imageAdded="handleImageAdded"
                    >
                    </vue-editor>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn
                            v-model="assignmentId"
                            color="primary"
                            @click="saveContent"
                    >
                        Save Comments
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-btn
                            color="primary"
                            flat
                            @click="commentsDialog = false"
                    >
                        Close
                    </v-btn>
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
        <v-container grid-list-md text-xs-center style="max-width: 100%; width:100%">
            <v-card>
                <v-layout row>
                    <v-flex xs12>
                        <h3 style="float: left; margin: 20px">Assigned - Testing-In-Progress </h3>
                    </v-flex>
                </v-layout>
                <v-data-table
                        :items="agentAssignments"
                        class="elevation-1"
                        hide-actions
                        :headers="agentAssignmentsHeader"
                        :pagination.sync="pagination"
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
                            <v-btn @click="showComments(props.item)">Comments/Bugs</v-btn>
                            <div v-if="props.item.is_all_good==0">
                                <v-btn color="success" @click="setAllGood(props.item)">All Good</v-btn>
                            </div>
                            <div v-else="props.item.is_all_good==1">
                                <b style="color:  mediumseagreen">Marked as all good</b>
                            </div>
                        </td>
                    </template>
                </v-data-table>
                <div class="text-xs-center pt-2">
                    <v-pagination
                            v-model="pagination.page"
                            :length="pages"
                    ></v-pagination>
                </div>
            </v-card>
        </v-container>
    </v-app>
</template>

<script>
    import { VueEditor } from "vue2-editor";
    export default {
        data () {
            return {
                agentAssignments: [],
                agentAssignmentsHeader: [
                    {text: 'ID', align: 'left', value: 'id'},
                    {text: 'Assigned Agent', align: 'left', value: 'name'},
                    {text: 'Environment', align: 'left', value: 'environment', width: 15},
                    {text: 'Order Number', align: 'left', value: 'order_items'},
                    {text: 'Product_look_up', align: 'left', value: 'product_look_up'},
                    {text: 'Customer Detail', align: 'left', value: 'customer_details'},
                    {text: 'Any Comments or Bugs to report', align: 'center', value: 'has_comments'},
                ],
                pagination: {
                    rowsPerPage: 6,
                    totalItems: 0
                },
                customerDetailsPopUp: [],
                dialogCustomerDetails: false,
                commentsDialog: false,
                assignmentId: [],
                content: ''
            }
        },
        components: {
           VueEditor
        },
        created () {
            this.initialize();
        },
        methods: {
            initialize() {
                let app = this;
                axios.get('agents/assignments')
                    .then(function (response) {
                        app.agentAssignments = response.data;
                        app.pagination.totalItems = response.data.length;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
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
            setAllGood(item){
                var assignmentId = item.id;
                let app = this;
                const params = new URLSearchParams();
                params.append('assignmentId', assignmentId);
                axios.put('agents/assignments', params)
                    .then(function (response) {
                        if(response.data.result == 'Failed'){
                            alert(response.data.message);
                        }else{
                            app.initialize();
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

            },
            showComments(item){
                this.assignmentId = item.id;
                var assignmentId = item.id;
                this.content = '';
                let app = this;
                axios.get('comments/' +assignmentId)
                    .then(function (response) {
                        if(response.data.result == 'Failed'){
                            alert(response.data.message);
                        }else{
                            if(response.data.length >= 1){
                                app.content = response.data[0]['comment'];
                            }
                        }
                        app.commentsDialog = true;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            saveContent(){
                var assignmentId = this.assignmentId;
                var content = this.content;
                if(!content){
                    this.commentsDialog = false;
                    return false;
                }
                let app = this;
                const params = new URLSearchParams();
                params.append('assignmentId', assignmentId);
                params.append('content', content);
                axios.post('comments', params)
                    .then(function (response) {
                        if(response.data.result == 'Failed'){
                            app.$notify({
                                group: 'commentsSave',
                                text: 'Failed to save comments',
                                duration: 5000,
                                type: 'warn'
                            });
                        }else{
                            app.commentsDialog = false;
                            app.$notify({
                                group: 'commentsSave',
                                text: 'Comments saved successfully',
                                duration: 5000,
                                type: 'success'
                            });
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            handleImageAdded: function(file, Editor, cursorLocation, resetUploader){
                var assignmentId = this.assignmentId;
                var formData = new FormData();
                formData.append('image', file);
                formData.append('assignmentId', assignmentId);

                axios({
                    url: 'comments/images',
                    method: 'POST',
                    data: formData
                })
                    .then((result) => {
                        let url = result.data; // Get url from response
                        Editor.insertEmbed(cursorLocation, 'image', url);
                        resetUploader();

                    })
                    .catch((err) => {
                        console.log(err);
                    })
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