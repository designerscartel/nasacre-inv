<template>
    <div>
        <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

            <div class="flex flex-wrap items-center justify-between gap-4">

                <h1 class="text-2xl font-semibold text-gray-800">
                    SACRE Directory
                </h1>

                <div class="text-right">
                    <inertia-link href="/sacres/create"
                                  class="bg-gray-800 hover:bg-gray-7000 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150">
                        Create Sacre
                    </inertia-link>
                </div>

            </div>

            <p class="mt-3 text-gray-500">Manage SACRE membership, contacts, invoices, and records.</p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
                <div class="border rounded-lg p-4 bg-gray-50">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Total SACREs</div>
                    <div class="text-2xl font-semibold text-gray-800 mt-1">{{ sacres.data.length }}</div>
                </div>
                <div class="border rounded-lg p-4 bg-green-50">
                    <div class="text-xs uppercase tracking-wide text-green-700">Current Members</div>
                    <div class="text-2xl font-semibold text-green-800 mt-1">{{ memberCount }}</div>
                </div>
                <div class="border rounded-lg p-4 bg-gray-50">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Non-Members</div>
                    <div class="text-2xl font-semibold text-gray-800 mt-1">{{ nonMemberCount }}</div>
                </div>
            </div>

            <div class="mt-6">
                <label for="sacre-search" class="block text-sm font-medium text-gray-700">Find a SACRE</label>
                <input id="sacre-search" v-model="search" type="search" placeholder="Search by name, code, or region"
                       class="form-input mt-1 block w-full sm:max-w-md rounded-md shadow-sm">
            </div>


        </div>

        <div class="bg-gray-200 bg-opacity-25 overflow-x-auto">
            <table class="table-auto w-full" style="min-width: 800px">
                <thead class="text-left">
                <tr>
                    <th class="px-8 py-2">Sacre</th>
                    <th class="px-4 py-2">Member</th>
                    <th class="px-4 py-2">Past Member</th>
                    <th class="px-4 py-2">Region</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
                </thead>
                <tbody>
                <template v-if="filteredSacres.length">
                    <tr v-for="sacre in filteredSacres" :key="sacre.id">
                        <td class="border px-8 py-2">{{ sacre.title }}</td>
                        <td class="border px-4 py-2">
                            <span v-if="sacre.member"
                                  class="bg-green-500 text-white text-xs px-2 py-1 inline-block rounded-full">Member</span>
                            <span v-else class="bg-gray-200 text-gray-600 text-xs px-2 py-1 inline-block rounded-full">Non-Member</span>
                        </td>
                        <td class="border px-4 py-2">
                            <span v-if="sacre.past_member"
                                  class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 inline-block rounded-full">Past Member</span>
                            <span v-else class="text-gray-400 text-xs">—</span>
                        </td>
                        <td class="border px-4 py-2">{{ sacre.region.title }}</td>
                        <td class="border px-4 py-2">
                            <inertia-link class="text-sm mr-2" :href="'/sacres/'+ sacre.id">
                                Edit
                            </inertia-link>
                            <inertia-link class="text-sm mr-2" :href="'/sacres/'+ sacre.id + '/invoices'">
                                Invoices
                            </inertia-link>
                            <button class="cursor-pointer text-sm text-red-500 focus:outline-none"
                                    @click="confirmSacreDeletion(sacre)">
                                Delete
                            </button>
                        </td>
                    </tr>
                </template>
                <tr v-else>
                    <td colspan="5" class="border px-8 py-4 text-center text-gray-500">No SACREs found</td>
                </tr>

                </tbody>
            </table>
        </div>

        <!-- Delete Sacre Modal -->
        <jet-confirmation-modal :show="sacreBeingDeleted" @close="sacreBeingDeleted = null">
            <template #title>
                Delete SACRE
            </template>

            <template #content>
                Are you sure you would like to delete this SACRE?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="sacreBeingDeleted = null">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="deleteSacre"
                                   :class="{ 'opacity-25': deleteSacreForm.processing }"
                                   :disabled="deleteSacreForm.processing">
                    Delete
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>

    </div>
</template>

<script>
    import JetConfirmationModal from './../../Jetstream/ConfirmationModal'
    import JetActionMessage from "../../Jetstream/ActionMessage";
    import JetActionSection from "../../Jetstream/ActionSection";
    import JetButton from "../../Jetstream/Button";
    import JetDangerButton from "../../Jetstream/DangerButton";
    import JetSecondaryButton from "../../Jetstream/SecondaryButton";
    import JetSectionBorder from "../../Jetstream/SectionBorder";

    export default {
        components: {
            JetActionMessage,
            JetActionSection,
            JetButton,
            JetConfirmationModal,
            JetDangerButton,
            JetSecondaryButton,
            JetSectionBorder,
        },
        props: [
            'sacres',
        ],
        data() {
            return {
                deleteSacreForm: this.$inertia.form(),
                sacreBeingDeleted: null,
                search: '',

            }
        },
        computed: {
            memberCount() {
                return this.sacres.data.filter(sacre => sacre.member).length
            },
            nonMemberCount() {
                return this.sacres.data.length - this.memberCount
            },
            filteredSacres() {
                const term = this.search.trim().toLowerCase()

                if (!term) {
                    return this.sacres.data
                }

                return this.sacres.data.filter(sacre => [
                    sacre.title,
                    sacre.short_code,
                    sacre.code,
                    sacre.region && sacre.region.title,
                ].some(value => value && String(value).toLowerCase().includes(term)))
            },
        },
        methods: {

            confirmSacreDeletion(contact) {
                this.sacreBeingDeleted = contact
            },

            deleteSacre() {
                this.deleteSacreForm.delete('/sacres/' + this.sacreBeingDeleted.id, {
                    preserveScroll: true,
                    preserveState: true,
                }).then(() => {
                    this.sacreBeingDeleted = null
                })
            },
        },
    }
</script>
