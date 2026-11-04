<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Places of Interest</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden">
                    <div class="p-6 sm:px-8 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div>
                                <h1 class="text-2xl">Places of Interest</h1>
                                <p class="mt-1 text-sm text-gray-500">Manage venues, contacts and the areas they serve.</p>
                            </div>
                            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                                <a href="/regions" class="text-sm text-gray-600 hover:text-gray-900">Manage regions</a>
                                <button class="bg-gray-800 hover:bg-gray-700 text-xs text-white font-semibold py-2 px-4 rounded" @click="openCreate">Add place</button>
                            </div>
                        </div>

                        <form class="mt-6 grid gap-3 md:grid-cols-3" @submit.prevent="filter">
                            <input v-model="search" type="search" placeholder="Search places, religion or contact" class="rounded border-gray-300 md:col-span-2" />
                            <select v-model="region" class="rounded border-gray-300" @change="filter">
                                <option value="">All regions</option>
                                <option v-for="option in regions" :key="option.id" :value="option.id">{{ option.title }}</option>
                            </select>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table-auto w-full min-w-full">
                            <thead class="bg-gray-50 text-left text-sm text-gray-600">
                                <tr>
                                    <th class="px-6 py-3">Place</th>
                                    <th class="px-4 py-3">Religion</th>
                                    <th class="px-4 py-3">Regions</th>
                                    <th class="px-4 py-3">Contact</th>
                                    <th class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="place in places.data" :key="place.id" class="border-t align-top">
                                    <td class="px-6 py-4">
                                        <div class="font-medium">{{ place.name }}</div>
                                        <span v-if="place.do_not_list" class="inline-block mt-1 rounded bg-red-100 px-2 py-1 text-xs text-red-700">Do not list</span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600">{{ place.religion || '—' }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-600">
                                        <span v-for="(placeRegion, index) in place.regions" :key="placeRegion.id">{{ index ? ', ' : '' }}{{ placeRegion.title }}</span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600">
                                        <div>{{ place.contact_name || '—' }}</div>
                                        <a v-if="place.organisation_email" :href="'mailto:' + place.organisation_email" class="text-blue-600">{{ place.organisation_email }}</a>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm whitespace-no-wrap">
                                        <button class="mr-4 text-gray-700 hover:text-black" @click="openEdit(place)">Edit</button>
                                        <button class="text-red-600 hover:text-red-800" @click="destroy(place)">Delete</button>
                                    </td>
                                </tr>
                                <tr v-if="!places.data.length">
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">No places match these filters.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="places.links.length > 3" class="p-6 flex flex-wrap gap-2 border-t">
                        <a v-for="link in places.links" :key="link.label" :href="link.url || '#'" v-html="link.label"
                           class="px-3 py-2 text-sm rounded border" :class="{ 'bg-gray-800 text-white': link.active, 'text-gray-400 pointer-events-none': !link.url }"></a>
                    </div>
                </div>
            </div>
        </div>

        <jet-dialog-modal :show="showDialog" @close="showDialog = false">
            <template #title>{{ editingId ? 'Edit place' : 'Add place' }}</template>
            <template #content>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <jet-label for="place-name" value="Place name" />
                        <jet-input id="place-name" v-model="form.name" class="mt-1 block w-full" />
                        <jet-input-error :message="form.error('name')" class="mt-2" />
                    </div>
                    <field-input id="religion" label="Religion or belief" v-model="form.religion" :error="form.error('religion')" />
                    <field-input id="contact-name" label="Contact name" v-model="form.contact_name" :error="form.error('contact_name')" />
                    <field-input id="organisation-email" label="Organisation email" type="email" v-model="form.organisation_email" :error="form.error('organisation_email')" />
                    <field-input id="personal-email" label="Personal email" type="email" v-model="form.personal_email" :error="form.error('personal_email')" />
                    <field-input id="phone" label="Phone" v-model="form.phone" :error="form.error('phone')" />
                    <field-input id="website" label="Website" v-model="form.website" :error="form.error('website')" />
                    <field-input id="last-training" label="Most recent training" v-model="form.last_training" :error="form.error('last_training')" />
                    <field-input id="charge" label="Charge to speak" v-model="form.charge_to_speak" :error="form.error('charge_to_speak')" />
                    <div class="md:col-span-2">
                        <jet-label for="regions" value="Regions" />
                        <select id="regions" v-model="form.region_ids" multiple class="mt-1 block w-full rounded border-gray-300 h-32">
                            <option v-for="option in regions" :key="option.id" :value="option.id">{{ option.title }}</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Hold Ctrl or Command to select multiple regions.</p>
                        <jet-input-error :message="form.error('region_ids')" class="mt-2" />
                    </div>
                    <div class="md:col-span-2">
                        <jet-label for="notes" value="Notes" />
                        <textarea id="notes" v-model="form.notes" rows="4" class="mt-1 block w-full rounded border-gray-300"></textarea>
                        <jet-input-error :message="form.error('notes')" class="mt-2" />
                    </div>
                    <label class="md:col-span-2 flex items-center text-sm text-gray-700">
                        <input v-model="form.do_not_list" type="checkbox" class="rounded border-gray-300 mr-2" />
                        Do not include this place in public lists
                    </label>
                </div>
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

const FieldInput = {
    props: ['id', 'label', 'value', 'error', 'type'],
    components: { JetInput, JetInputError, JetLabel },
    template: `<div><jet-label :for="id" :value="label"/><jet-input :id="id" :type="type || 'text'" :value="value" @input="$emit('input', $event)" class="mt-1 block w-full"/><jet-input-error :message="error" class="mt-2"/></div>`,
}

const emptyPlace = () => ({
    name: '', religion: '', contact_name: '', organisation_email: '', personal_email: '', phone: '', website: '',
    last_training: '', charge_to_speak: '', notes: '', do_not_list: false, region_ids: [],
})

export default {
    props: ['places', 'regions', 'filters'],
    components: { AppLayout, FieldInput, JetButton, JetDialogModal, JetInput, JetInputError, JetLabel, JetSecondaryButton },
    data() {
        return {
            search: this.filters.search,
            region: this.filters.region,
            editingId: null,
            showDialog: false,
            form: this.$inertia.form(emptyPlace(), { bag: 'placeForm' }),
        }
    },
    methods: {
        filter() {
            this.$inertia.get('/places', { search: this.search, region: this.region }, { preserveState: true })
        },
        openCreate() {
            this.editingId = null
            Object.assign(this.form, emptyPlace())
            this.showDialog = true
        },
        openEdit(place) {
            this.editingId = place.id
            Object.keys(emptyPlace()).forEach(key => {
                this.form[key] = key === 'region_ids' ? place.regions.map(region => region.id) : (place[key] || (key === 'do_not_list' ? false : ''))
            })
            this.showDialog = true
        },
        save() {
            const request = this.editingId
                ? this.form.put('/places/' + this.editingId, { preserveScroll: true })
                : this.form.post('/places', { preserveScroll: true })

            request.then(() => {
                if (!this.form.hasErrors()) this.showDialog = false
            })
        },
        destroy(place) {
            if (!window.confirm('Delete ' + place.name + '?')) return
            this.$inertia.delete('/places/' + place.id, { preserveScroll: true })
        },
    },
}
</script>
