<template>
    <app-layout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Place areas</h2>
                <a href="/places" class="text-sm text-gray-600 hover:text-gray-900">Back to places</a>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden">
                    <div class="p-6 sm:px-8 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl">Areas</h1>
                            <p class="mt-1 text-sm text-gray-500">Group places under one or more geographical areas.</p>
                        </div>
                        <button class="bg-gray-800 hover:bg-gray-700 text-xs text-white font-semibold py-2 px-4 rounded" @click="openCreate">
                            Create area
                        </button>
                    </div>

                    <div v-if="$page.errors.area" class="m-6 rounded bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $page.errors.area }}
                    </div>

                    <table class="table-auto w-full">
                        <thead class="bg-gray-50 text-left text-sm text-gray-600">
                            <tr>
                                <th class="px-8 py-3">Area</th>
                                <th class="px-4 py-3">Places</th>
                                <th class="px-8 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="area in areas" :key="area.id" class="border-t">
                                <td class="px-8 py-3 font-medium">{{ area.name }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ area.places_count }}</td>
                                <td class="px-8 py-3 text-right text-sm">
                                    <button class="mr-4 text-gray-700 hover:text-black" @click="openEdit(area)">Edit</button>
                                    <button class="text-red-600 hover:text-red-800 disabled:opacity-40" :disabled="area.places_count > 0" @click="destroy(area)">Delete</button>
                                </td>
                            </tr>
                            <tr v-if="!areas.length">
                                <td colspan="3" class="px-8 py-8 text-center text-gray-500">No areas have been added.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <jet-dialog-modal :show="showDialog" @close="showDialog = false">
            <template #title>{{ editingId ? 'Edit area' : 'Create area' }}</template>
            <template #content>
                <jet-label for="area-name" value="Area name" />
                <jet-input id="area-name" v-model="form.name" type="text" class="mt-1 block w-full" @keyup.enter.native="save" />
                <jet-input-error :message="form.error('name')" class="mt-2" />
            </template>
            <template #footer>
                <jet-secondary-button @click.native="showDialog = false">Cancel</jet-secondary-button>
                <jet-button class="ml-2" :disabled="form.processing" @click.native="save">Save</jet-button>
            </template>
        </jet-dialog-modal>
    </app-layout>
</template>

<script>
import AppLayout from './../../Layouts/AppLayout'
import JetButton from './../../Jetstream/Button'
import JetDialogModal from './../../Jetstream/DialogModal'
import JetInput from './../../Jetstream/Input'
import JetInputError from './../../Jetstream/InputError'
import JetLabel from './../../Jetstream/Label'
import JetSecondaryButton from './../../Jetstream/SecondaryButton'

export default {
    props: ['areas'],
    components: { AppLayout, JetButton, JetDialogModal, JetInput, JetInputError, JetLabel, JetSecondaryButton },
    data() {
        return {
            editingId: null,
            showDialog: false,
            form: this.$inertia.form({ name: '' }, { bag: 'areaForm', resetOnSuccess: true }),
        }
    },
    methods: {
        openCreate() {
            this.editingId = null
            this.form.name = ''
            this.showDialog = true
        },
        openEdit(area) {
            this.editingId = area.id
            this.form.name = area.name
            this.showDialog = true
        },
        save() {
            const request = this.editingId
                ? this.form.put('/place-areas/' + this.editingId, { preserveScroll: true })
                : this.form.post('/place-areas', { preserveScroll: true })

            request.then(() => {
                if (!this.form.hasErrors()) this.showDialog = false
            })
        },
        destroy(area) {
            if (area.places_count || !window.confirm('Delete ' + area.name + '?')) return
            this.$inertia.delete('/place-areas/' + area.id, { preserveScroll: true })
        },
    },
}
</script>
