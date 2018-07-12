<template>
    <v-app id="inspire">
        <v-container grid-list-md text-xs-center style="max-width: 100%; width:100%">
            <v-card :height="350">
                <v-layout row>
                    <v-flex xs12>
                        <h2 style="float: left; margin: 20px">Testing Assignments</h2>
                    </v-flex>
                </v-layout>
                <v-layout row>
                    <AssignTest></AssignTest>
                    <v-flex xs1></v-flex>
                    <WorkloadSummary></WorkloadSummary>
                </v-layout>
            </v-card>
        </v-container>
    </v-app>
</template>

<script>
    import AssignTest from './AssignTest.vue';
    import WorkloadSummary from './WorkloadSummary.vue';

    export default {
        data () {
            return {

            }
        },
        components:{
            AssignTest,
            WorkloadSummary
        }
    }
</script>

<style scoped>

</style>