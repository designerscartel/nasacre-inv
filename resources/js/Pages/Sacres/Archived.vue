<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Archived SACREs</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:px-8 border-b border-gray-200">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-semibold text-gray-800">Archived SACREs</h1>
                                <p class="mt-2 text-gray-500">Restore archived SACREs with their contacts and file records.</p>
                            </div>
                            <inertia-link href="/dashboard" class="text-sm text-gray-600 hover:text-gray-900">
                                Back to SACRE directory
                            </inertia-link>
                        </div>

                        <div class="mt-6">
                            <label for="archived-sacre-search" class="block text-sm font-medium text-gray-700">Find an archived SACRE</label>
                            <input id="archived-sacre-search" v-model="search" type="search" placeholder="Search by name"
                                   class="form-input mt-1 block w-full sm:max-w-md rounded-md shadow-sm">
                            <p class="text-sm text-gray-500 mt-2">
                                {{ sacres.meta.total }} archived SACRE<span v-if="sacres.meta.total !== 1">s</span>
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-200 bg-opacity-25 overflow-x-auto">
                        <table class="table-fixed w-full" style="min-width: 700px">
                            <thead class="text-left">
                            <tr>
                                <th class="w-2/5 px-8 py-2">SACRE</th>
                                <th class="w-1/4 px-4 py-2">Region</th>
                                <th class="w-1/5 px-4 py-2">Archived</th>
                                <th class="w-1/6 px-4 py-2">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="sacre in sacres.data" :key="sacre.id">
                                <td class="border px-8 py-2 break-words">{{ sacre.title }}</td>
                                <td class="border px-4 py-2 break-words">{{ sacre.region ? sacre.region.title : '—' }}</td>
                                <td class="border px-4 py-2">{{ formatDate(sacre.archived_at) }}</td>
                                <td class="border px-4 py-2">
                                    <button class="text-sm text-green-700 hover:text-green-900 focus:outline-none"
                                            @click="confirmRestore(sacre)">Restore</button>
                                </td>
                            </tr>
                            <tr v-if="!sacres.data.length">
                                <td colspan="4" class="border px-8 py-4 text-center text-gray-500">No archived SACREs found</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="sacres.meta.last_page > 1" class="flex items-center justify-between px-6 sm:px-8 py-4 border-t border-gray-200">
                        <inertia-link v-if="sacres.links.prev" :href="sacres.links.prev" preserve-scroll preserve-state class="text-sm text-gray-700">&larr; Previous</inertia-link>
                        <span v-else></span>
                        <span class="text-sm text-gray-500">Page {{ sacres.meta.current_page }} of {{ sacres.meta.last_page }}</span>
                        <inertia-link v-if="sacres.links.next" :href="sacres.links.next" preserve-scroll preserve-state class="text-sm text-gray-700">Next &rarr;</inertia-link>
                        <span v-else></span>
                    </div>
                </div>
            </div>
        </div>

        <jet-confirmation-modal :show="sacreBeingRestored" @close="sacreBeingRestored = null">
            <template #title>Restore SACRE</template>
            <template #content>
                Restore {{ sacreBeingRestored ? sacreBeingRestored.title : 'this SACRE' }} and its archived contacts and file records?
            </template>
            <template #footer>
                <jet-secondary-button @click.native="sacreBeingRestored = null">Cancel</jet-secondary-button>
                <jet-button class="ml-2" :class="{ 'opacity-25': restoreForm.processing }"
                            :disabled="restoreForm.processing" @click.native="restoreSacre">Restore</jet-button>
            </template>
        </jet-confirmation-modal>
    </app-layout>
</template>

<script>
import AppLayout from './../../Layouts/AppLayout'
import JetButton from './../../Jetstream/Button'
import JetConfirmationModal from './../../Jetstream/ConfirmationModal'
import JetSecondaryButton from './../../Jetstream/SecondaryButton'
import debounce from 'lodash/debounce'

export default {
    components: { AppLayout, JetButton, JetConfirmationModal, JetSecondaryButton },
    props: ['sacres', 'filters'],
    data() {
        return {
            restoreForm: this.$inertia.form(),
            sacreBeingRestored: null,
            search: this.filters.search || '',
        }
    },
    watch: {
        search: debounce(function (value) {
            this.$inertia.replace('/sacres/archived', {
                data: value ? { search: value } : {},
                preserveScroll: true,
                preserveState: true,
            })
        }, 300),
    },
    methods: {
        confirmRestore(sacre) {
            this.sacreBeingRestored = sacre
        },
        restoreSacre() {
            this.restoreForm.post('/sacres/archived/' + this.sacreBeingRestored.id + '/restore', {
                preserveScroll: true,
            }).then(() => {
                this.sacreBeingRestored = null
            })
        },
        formatDate(value) {
            return value ? new Date(value).toLocaleDateString('en-GB') : '—'
        },
    },
}
</script>
