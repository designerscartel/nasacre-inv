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


                            <div class="md:w-2/6 text-right">

                                <inertia-link href="/groups"
                                              class="bg-gray-800 hover:bg-gray-7000 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150 mr-2">
                                    Campaign Groups
                                </inertia-link>

                                <inertia-link href="/campaigns/create"
                                              class="bg-gray-800 hover:bg-gray-7000 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150">
                                    Create campaign
                                </inertia-link>

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
                            <template v-if="campaigns.data.length">
                                <tr v-if="campaigns" v-for="campaign in campaigns.data" :key="campaign.id">
                                    <td class="border px-8 py-2">{{ campaign.subject }}</td>
                                    <td class="border px-4 py-2">
                                        <inertia-link class="text-sm mr-2" :href="'/campaigns/'+ campaign.id">
                                            Edit
                                        </inertia-link>

                                        <!-- Send Campaign -->
                                        <button class="cursor-pointer text-sm text-green-500 focus:outline-none"
                                                @click="confirmCampaignSend(campaign)">
                                            Send
                                        </button>

                                    </td>
                                </tr>
                            </template>
                            <tr v-else>
                                <td colspan="2" class="border px-8 py-2">No Campaigns</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

                        <div class="md:flex md:justify-end">

                            <div class="md:w-1/6 text-right">
                                <inertia-link href="/campaigns/create"
                                              class="bg-gray-800 hover:bg-gray-7000 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150">
                                    Create campaign
                                </inertia-link>
                            </div>

                        </div>
                    </div>


                </div>
            </div>
        </div>

        <!-- Send Campaign Modal -->
        <jet-dialog-modal :show="campaignBeingSent" @close="campaignBeingSent = null">
            <template #title>
                Send Campaign
            </template>

            <template #content>
                Are you sure you would like to send this campaign?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="campaignBeingSent = null">
                    Nevermind
                </jet-secondary-button>

                <jet-confirm-button class="ml-2" @click.native="sendCampaign"
                                   :class="{ 'opacity-25': sendCampaignForm.processing }"
                                   :disabled="sendCampaignForm.processing">
                    Send
                </jet-confirm-button>
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
    import JetConfirmButton from './../../Jetstream/ConfirmButton'
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
            JetConfirmButton,
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

                sendCampaignForm: this.$inertia.form({
                    send: '',
                }, {
                    bag: 'sendCampaignInformation',
                    resetOnSuccess: true,
                }),

                campaignId: null,
                campaignBeingSent: null
            }
        },
        methods: {
            confirmCampaignSend(campaign) {
                this.sendCampaignForm.send = true
                this.campaignBeingSent = campaign
            },

            sendCampaign() {
                this.sendCampaignForm.post('/campaigns/' +  this.campaignBeingSent.id + '/send', {
                    preserveScroll: true,
                    preserveState: true,
                }).then(() => {
                    this.campaignBeingSent = null
                })
            },
        }
    }
</script>
