<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
</script>

<template>

    <Head title="Dashboard" />

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
                                    <input type="text" name="lastname">
                                </div>

                                <div class="form-row">
                                    <label>
                                        <span>DATE MEDICATION PREPARED</span>
                                    </label>
                                </div>
                                <div>
                                    <input type="text" name="dateprepared" value="01/08/2023">
                                </div>

                                <div class="form-row">
                                    <label>
                                        <span>EXPIRATION DATE</span>
                                    </label>
                                </div>
                                <div>
                                    <input type="text" name="expdate" value="01/08/2023">
                                </div>

                                <div class="form-row">
                                    <label>
                                        <span>EXPIRATION TIME</span>
                                    </label>
                                </div>
                                <div>
                                    <input type="text" name="exptime" value="1900">
                                </div>

                                <div class="form-row">
                                    <label>
                                        <span>LABEL SHEET REQUESTED</span>
                                    </label>
                                </div>
                                <div>
                                    <select name="sheet">
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
                                        <button class="self-center" type="submit">GENERATE LABELS</button>
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
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
