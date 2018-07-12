<template>
    <v-flex xs7>
        <v-layout row wrap>
            <v-flex xs5>
                <v-subheader style="margin-left: 50px">Who is testing today?</v-subheader>
            </v-flex>
            <v-flex d-flex xs2>
                <v-select style="width: 150px;"
                          v-model="agentDefault"
                          :items="agents"
                          item-text="name"
                          item-value="id"
                          solo
                ></v-select>
            </v-flex>
            <v-flex xs4></v-flex>
        </v-layout>
        <v-layout row wrap>
            <v-flex xs5>
                <v-subheader style="margin-left: 50px">How many orders are they testing?</v-subheader>
            </v-flex>
            <v-flex d-flex xs2>
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
            <v-flex xs1></v-flex>
            <v-flex xs3>
                <div id="assignBtn">
                    <v-btn
                            large
                            color="primary"
                            @click="assignTests"
                    >Assign Test Orders</v-btn>
                </div>
            </v-flex>
        </v-layout>
    </v-flex>
</template>

<script>
    import {EventBus} from "../app";

    export default {
        data() {
            return {
                agentDefault: [],
                agents: [],
                numDefault: [5, 40]

            }
        },
        created() {
            this.initialize();
        },
        methods: {
            initialize() {
                let app = this;
                axios.get('agents')
                    .then(function (response) {
                        if (response.data.result == 'Failed') {
                            alert(response.data.message);
                        } else {
                            app.agents = response.data;
                            app.agentDefault = response.data[0]['id'];
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            assignTests() {
                var agentId = this.agentDefault;
                var numAssignments = this.numDefault[0];
                if (numAssignments < 1) {
                    alert('The number of testing assignments cannot be less than one');
                    return false;
                }
                const params = new URLSearchParams();
                params.append('agentId', agentId);
                params.append('numAssignments', numAssignments);
                axios.post('assignTests', params)
                    .then(function (response) {
                        if (response.data.result == 'Failed') {
                            alert(response.data.message);
                        } else {
                            EventBus.$emit('updateWorkload');
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        }

    }
</script>

<style scoped>
    #assignBtn{
        margin-top: -20px;
    }
</style>