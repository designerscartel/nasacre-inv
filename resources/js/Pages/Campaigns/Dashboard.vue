<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                NASACRE Campaigns
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

                        <div class="mt-8 md:flex">

                            <h1 class="text-2xl md:w-5/6">
                                NASACRE Campaigns
                            </h1>

                            <div class="md:w-1/6 text-right">
                                <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                                        @click="addCampaignDialog">
                                    Create campaign
                                </button>
                            </div>

                        </div>

                    </div>

                    <div class="bg-gray-200 bg-opacity-25">
                        <table class="table-auto w-full">
                            <thead class="text-left">
                            <tr>
                                <th class="px-8 py-2">Campaign</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-if="campaigns == null">
                                <td colspan="2" class="border px-8 py-2">No Campaigns</td>
                            </tr>
                            <template v-else>
                                <tr v-for="campaign in campaigns.data" :key="campaign.id">
                                    <td class="border px-8 py-2">{{ campaign.title }}</td>
                                    <td class="border px-4 py-2">
                                        <button class="text-sm" @click="updateCampaignDialog(campaign)">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <!-- New Campaign Modal -->
        <jet-dialog-modal :show="showNewCampaignDialog" @close="showNewCampaignDialog = false">
            <template #title>
                Add Campaign
            </template>

            <template #content>
                <!-- Title -->
                <div class="mt-4">
                    <jet-label for="title" value="Title"/>
                    <jet-input id="title" type="text" class="mt-1 block w-full" v-model="addCampaignForm.title"/>
                    <jet-input-error :message="addCampaignForm.error('title')" class="mt-2"/>
                </div>

            </template>

            <template #footer>
                <jet-secondary-button @click.native="showNewCampaignDialog = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="addCampaign" :class="{ 'opacity-25': addCampaignForm.processing }"
                            :disabled="addCampaignForm.processing">
                    Save
                </jet-button>

            </template>
        </jet-dialog-modal>

        <!-- Update Campaign Modal -->
        <jet-dialog-modal :show="showUpdateCampaignDialog" @close="showUpdateCampaignDialog = false">
            <template #title>
                Update Campaign
            </template>

            <template #content>
                <!-- Title -->
                <div class="mt-4">
                    <jet-label for="title" value="Title"/>
                    <jet-input id="title" type="text" class="mt-1 block w-full" v-model="updateCampaignForm.title"/>
                    <jet-input-error :message="updateCampaignForm.error('title')" class="mt-2"/>
                </div>

            </template>

            <template #footer>
                <jet-secondary-button @click.native="showUpdateCampaignDialog = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="updateCampaign"
                            :class="{ 'opacity-25': showUpdateCampaignDialog.processing }"
                            :disabled="showUpdateCampaignDialog.processing">
                    Save
                </jet-button>

            </template>
        </jet-dialog-modal>

    </app-layout>
</template>

<script>
    import AppLayout from './../../Layouts/AppLayout'
    import JetActionMessage from './../../Jetstream/ActionMessage'
    import JetActionSection from './../../Jetstream/ActionSection'
    import JetButton from './../../Jetstream/Button'
    import JetConfirmationModal from './../../Jetstream/ConfirmationModal'
    import JetDangerButton from './../../Jetstream/DangerButton'
    import JetDialogModal from './../../Jetstream/DialogModal'
    import JetFormSection from './../../Jetstream/FormSection'
    import JetInput from './../../Jetstream/Input'
    import JetSelect from './../../Jetstream/Select'
    import JetInputError from './../../Jetstream/InputError'
    import JetLabel from './../../Jetstream/Label'
    import JetSecondaryButton from './../../Jetstream/SecondaryButton'
    import JetSectionBorder from './../../Jetstream/SectionBorder'

    export default {
        props: [
            'campaigns',
        ],
        components: {
            AppLayout,
            JetActionMessage,
            JetActionSection,
            JetButton,
            JetConfirmationModal,
            JetDangerButton,
            JetDialogModal,
            JetFormSection,
            JetInput,
            JetSelect,
            JetInputError,
            JetLabel,
            JetSecondaryButton,
            JetSectionBorder,
        },
        data() {
            return {
                addCampaignForm: this.$inertia.form({
                    title: '',
                }, {
                    bag: 'addCampaignForm',
                    resetOnSuccess: true,
                }),

                updateCampaignForm: this.$inertia.form({
                    title: '',
                }, {
                    bag: 'updateCampaignForm',
                    resetOnSuccess: true,
                }),

                contactId: null,
                showNewCampaignDialog: false,
                showUpdateCampaignDialog: false,
            }
        },
        methods: {
            addCampaign() {
                this.addCampaignForm.post('/campaigns', {
                    preserveScroll: true
                }).then(() => {
                    this.showNewCampaignDialog = false
                    if (!this.addCampaignForm.hasErrors()) {
                        this.showNewCampaignDialog = false
                    }
                })
            },
            addCampaignDialog() {
                this.showNewCampaignDialog = true
            },
            updateCampaign() {
                this.updateCampaignForm.put('/campaigns/' + this.campaignId, {
                    preserveScroll: true
                }).then(() => {
                    this.campaignId = null;
                    if (!this.updateCampaignForm.hasErrors()) {
                        this.showUpdateCampaignDialog = false
                    }
                })
            },
            updateCampaignDialog(campaign) {
                this.updateCampaignForm.id = campaign.id
                this.updateCampaignForm.title = campaign.title
                this.campaignId = campaign.id
                this.showUpdateCampaignDialog = true
            },
        }
    }
</script>
