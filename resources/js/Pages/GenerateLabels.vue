<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
import axios from 'axios';
</script>

<template>

    <Head title="Generate Labels" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Generate Labels</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form class="content-center" method="post" action="/label.php">
                            <div class="text-2xl font-bold antialiased text-center">
                                <h1>CHP Anesthesia Labels</h1>
                            </div>
                            <div class="grid grid-cols-2 gap-4 w-2/4 m-auto my-10">
                                <div class="form-row">
                                    <label>
                                        <span>LASTNAME</span>
                                    </label>
                                </div>
                                <div>
                                    <input type="text" name="lastname" v-model="form.lastName">
                                </div>

                                <div class="form-row">
                                    <label>
                                        <span>DATE MEDICATION PREPARED</span>
                                    </label>
                                </div>
                                <div>
                                    <Datepicker v-model="form.datePrepared" input-format="MM/dd/yyy"></Datepicker>
                                </div>

                                <div class="form-row">
                                    <label>
                                        <span>EXPIRATION DATE</span>
                                    </label>
                                </div>
                                <div>
                                    <Datepicker v-model="form.expDate" input-format="MM/dd/yyy"></Datepicker>
                                </div>

                                <div class="form-row">
                                    <label>
                                        <span>EXPIRATION TIME</span>
                                    </label>
                                </div>
                                <div>
                                    <vue-timepicker v-model="form.expTime"></vue-timepicker>
                                </div>

                                <div class="form-row">
                                    <label>
                                        <span>LABEL SHEET REQUESTED</span>
                                    </label>
                                </div>
                                <div>
                                    <select name="sheet" v-model="form.sheet">
                                        <option>GENERAL</option>
                                        <option>CARDIAC</option>
                                        <option>T&A</option>
                                        <option>SURGICENTER</option>
                                        <option>SPINE</option>
                                        <option>AIPPS</option>
                                    </select>
                                </div>

                                <div class="col-span-2">
                                    <div class="flex flex-col items-center">
                                        <button class="self-center" @click="generateLabel" type="button">
                                            GENERATE LABELS
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="disclaimer">Disclaimer: This tool and the labels it generates are
                                    provided "as is" without expressed or implied warranties as to their currency,
                                    accuracy or comprehensiveness. Some drug names may have been abbreviated. The
                                    healthcare professional is solely responsible for any resulting medical
                                    judgement or medication errors. Please review current product information
                                    sheets, such as the manufacturer's package insert for each drug, to verify
                                    relevant medication information. To the maximum extent permitted under
                                    applicable law, no responsibility is assumed by the creator of this tool for any
                                    injury and/or damage caused by its use. </div>
                            </div>
                        </form>
                        <form>
                            Upload a new config here
                            <input type="file" @change="uploadFile" ref="file" />
                            <button @click="submitFile()" type="button">Upload!</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import Datepicker from 'vue3-datepicker';
import VueTimepicker from 'vue3-timepicker';
import 'vue3-timepicker/dist/VueTimepicker.css';
import { format } from 'date-fns';

export default {
    components: { Datepicker, VueTimepicker },
    data() {
        return {
            form: {
                datePrepared: new Date(),
                expDate: new Date(),
                expTime: format(new Date(), 'hh:mm'),
                sheet: 'GENERAL',
                lastName: '',
            },
            files: null,
            count: 0
        }
    },
    methods: {
        generateLabel() {
            console.log('generateLabel was called');
            axios.get(`/labels/generate?sheet=${this.form.sheet}&prep_date=${format(this.form.datePrepared, 'M/d/yyyy')}&exp_date=${format(this.form.expDate, 'M/d/yyyy')}&exp_time=${this.form.expTime}&name=${this.form.lastName}`, {
                responseType: 'blob'
            }).then((response) => {
                window.open(URL.createObjectURL(response.data));
            });
        },
        submitFile() {
            let formData = new FormData();
            formData.append('file', this.file);
            console.log('>> formData >> ', formData);

            axios.post('http://localhost/labels/import-config',
                formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }
            ).then(function () {
                console.log('SUCCESS!!');
            })
                .catch(function () {
                    console.log('FAILURE!!');
                });
        },
        uploadFile() {
            this.file = this.$refs.file.files[0];
        },
    }
}
</script>
