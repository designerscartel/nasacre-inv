<template>
    <div>
        <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

            <div class="mt-8 md:flex">

                <h1 class="text-2xl md:w-5/6">
                    Welcome to the NASACRE application!
                </h1>

                <div class="md:w-1/6 text-right">
                    <inertia-link href="/sacres/create"
                                  class="bg-gray-800 hover:bg-gray-7000 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150">
                        Create Sacre
                    </inertia-link>
                </div>

            </div>

            <p class="mt-6 text-gray-500">
                Laravel Jetstream provides a beautiful, robust starting point for your next Laravel application. Laravel
                is designed
                to help you build your application using a development environment that is simple, powerful, and
                enjoyable. We believe
                you should love expressing your creativity through programming, so we have spent time carefully crafting
                the Laravel
                ecosystem to be a breath of fresh air. We hope you love it.
            </p>


        </div>

        <div class="bg-gray-200 bg-opacity-25">
            <table class="table-auto w-full">
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
                                  class="bg-green-200 text-white text-xs px-2 py-1 inline-block rounded-full">Past Member</span>
                            <span v-else class="bg-gray-200 text-gray-600 text-xs px-2 py-1 inline-block rounded-full">Non-Member</span>
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
                    <td colspan="4" class="border px-8 py-2">No SACRE'S</td>
                </tr>

                </tbody>
            </table>
        </div>

        <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

            <div class="md:flex md:justify-end">

                <div class="md:w-1/6 text-right">
                    <inertia-link href="/sacres/create"
                                  class="bg-gray-800 hover:bg-gray-7000 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150">
                        Create Sacre
                    </inertia-link>
                </div>

            </div>
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
                sacreBeingDeleted: null

            }
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
