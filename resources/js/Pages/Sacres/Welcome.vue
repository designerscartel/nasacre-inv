<template>
    <div>
        <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

            <div class="flex flex-wrap items-center justify-between gap-4">

                <h1 class="text-2xl font-semibold text-gray-800">
                    SACRE Directory
                </h1>

                <div class="text-right">
                    <inertia-link href="/sacres/archived"
                                  class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                        Archived SACREs
                    </inertia-link>
                    <inertia-link href="/sacres/create"
                                  class="bg-gray-800 hover:bg-gray-700 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150">
                        Create Sacre
                    </inertia-link>
                </div>

            </div>

            <p class="mt-3 text-gray-500">Manage SACRE membership, contacts, invoices, and records.</p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
                <div class="border rounded-lg p-4 bg-gray-50">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Total SACREs</div>
                    <div class="text-2xl font-semibold text-gray-800 mt-1">{{ summary.total }}</div>
                </div>
                <div class="border rounded-lg p-4 bg-green-50">
                    <div class="text-xs uppercase tracking-wide text-green-700">Current Members</div>
                    <div class="text-2xl font-semibold text-green-800 mt-1">{{ summary.members }}</div>
                </div>
                <div class="border rounded-lg p-4 bg-gray-50">
                    <div class="text-xs uppercase tracking-wide text-gray-500">Non-Members</div>
                    <div class="text-2xl font-semibold text-gray-800 mt-1">{{ summary.non_members }}</div>
                </div>
            </div>

            <div class="mt-6">
                <label for="sacre-search" class="block text-sm font-medium text-gray-700">Find a SACRE</label>
                <input id="sacre-search" v-model="search" type="search" placeholder="Search by name, code, or region"
                       class="form-input mt-1 block w-full sm:max-w-md rounded-md shadow-sm">
                <p class="text-sm text-gray-500 mt-2">
                    {{ sacres.meta.total }} result<span v-if="sacres.meta.total !== 1">s</span>
                </p>
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
                <template v-if="sacres.data.length">
                    <tr v-for="sacre in sacres.data" :key="sacre.id">
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
                                Archive
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

        <div v-if="sacres.meta.last_page > 1" class="flex items-center justify-between px-6 sm:px-8 py-4 bg-white border-t border-gray-200">
            <inertia-link v-if="sacres.links.prev" :href="sacres.links.prev" preserve-scroll preserve-state
                          class="text-sm font-medium text-gray-700 hover:text-gray-900">
                &larr; Previous
            </inertia-link>
            <span v-else></span>

            <span class="text-sm text-gray-500">
                Page {{ sacres.meta.current_page }} of {{ sacres.meta.last_page }}
            </span>

            <inertia-link v-if="sacres.links.next" :href="sacres.links.next" preserve-scroll preserve-state
                          class="text-sm font-medium text-gray-700 hover:text-gray-900">
                Next &rarr;
            </inertia-link>
            <span v-else></span>
        </div>

        <!-- Archive Sacre Modal -->
        <jet-confirmation-modal :show="sacreBeingDeleted" @close="sacreBeingDeleted = null">
            <template #title>
                Archive SACRE
            </template>

            <template #content>
                Archive this SACRE? Its contacts and file records can be restored later, and stored PDFs will be preserved.
            </template>

            <template #footer>
                <jet-secondary-button @click.native="sacreBeingDeleted = null">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="deleteSacre"
                                   :class="{ 'opacity-25': deleteSacreForm.processing }"
                                   :disabled="deleteSacreForm.processing">
                    Archive
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
    import debounce from 'lodash/debounce'

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
            'filters',
            'summary',
        ],
        data() {
            return {
                deleteSacreForm: this.$inertia.form(),
                sacreBeingDeleted: null,
                search: this.filters.search || '',

            }
        },
        watch: {
            search: debounce(function (value) {
                this.$inertia.replace('/dashboard', {
                    data: value ? { search: value } : {},
                    preserveScroll: true,
                    preserveState: true,
                })
            }, 300),
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
