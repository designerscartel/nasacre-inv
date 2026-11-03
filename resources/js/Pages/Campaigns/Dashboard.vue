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
                        <table class="table-fixed w-full">
                            <colgroup>
                                <col class="w-1/4">
                                <col class="w-1/2">
                                <col class="w-1/4">
                            </colgroup>
                            <thead class="text-left">
                            <tr>
                                <th class="px-8 py-2">Campaign</th>
                                <th class="px-4 py-2">Latest Delivery</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-if="campaigns.data.length">
                                <tr v-if="campaigns" v-for="campaign in campaigns.data" :key="campaign.id">
                                    <td class="border px-8 py-2 align-top break-words">{{ campaign.subject }}</td>
                                    <td class="border px-4 py-2 align-top break-words">
                                        <div v-if="campaign.delivery">
                                            <div class="text-sm font-semibold">Batch {{ campaign.delivery.batch }}</div>
                                            <div class="text-xs mt-1">
                                                <span class="text-green-700 mr-3">{{ campaign.delivery.sent }} sent</span>
                                                <span class="text-yellow-700 mr-3">{{ campaign.delivery.queued }} queued</span>
                                                <span class="text-red-700">{{ campaign.delivery.failed }} failed</span>
                                            </div>
                                            <details v-if="campaign.delivery.queued || campaign.delivery.failed" class="mt-2 text-xs min-w-0">
                                                <summary class="cursor-pointer text-gray-700">View delivery details</summary>
                                                <ul class="mt-2 space-y-2">
                                                    <li v-for="recipient in campaign.delivery.recipients" :key="recipient.id" class="break-words">
                                                        <span :class="deliveryStatusClass(recipient.status)" class="font-semibold uppercase">
                                                            {{ recipient.status }}
                                                        </span>
                                                        — {{ recipient.name || recipient.email }}
                                                        <span v-if="recipient.email"> ({{ recipient.email }})</span>
                                                        <div v-if="recipient.failure_message" class="text-red-700 mt-1">
                                                            {{ recipient.failure_message }}
                                                        </div>
                                                    </li>
                                                </ul>
                                            </details>
                                            <details v-if="campaign.delivery_batches.length" class="mt-2 text-xs min-w-0">
                                                <summary class="cursor-pointer text-blue-700">View batch history</summary>
                                                <div v-for="batch in campaign.delivery_batches" :key="batch.batch"
                                                     class="mt-3 border-t border-gray-300 pt-2">
                                                    <div class="flex flex-wrap items-center justify-between">
                                                        <div class="min-w-0 break-words">
                                                            <span class="font-semibold">Batch {{ batch.batch }}</span>
                                                            <span v-if="batch.started_at"> — {{ formatDate(batch.started_at) }}</span>
                                                        </div>
                                                        <button v-if="batch.retryable_failed"
                                                                class="text-red-600 focus:outline-none ml-3"
                                                                @click="confirmFailedRetry(campaign, batch)">
                                                            Retry {{ batch.retryable_failed }} failed
                                                        </button>
                                                    </div>
                                                    <div class="mt-1">
                                                        <span class="text-green-700 mr-2">{{ batch.sent }} sent</span>
                                                        <span class="text-yellow-700 mr-2">{{ batch.queued }} queued</span>
                                                        <span class="text-red-700">{{ batch.failed }} failed</span>
                                                    </div>
                                                    <ul class="mt-2 space-y-2">
                                                        <li v-for="recipient in batch.recipients" :key="recipient.id" class="break-words">
                                                            <span :class="deliveryStatusClass(recipient.status)" class="font-semibold uppercase">
                                                                {{ recipient.status }}
                                                            </span>
                                                            — {{ recipient.name || recipient.email }}
                                                            <span v-if="recipient.email"> ({{ recipient.email }})</span>
                                                            <span v-if="deliveryTimestamp(recipient)">
                                                                — {{ formatDate(deliveryTimestamp(recipient)) }}
                                                            </span>
                                                            <div v-if="recipient.failure_message" class="text-red-700 mt-1">
                                                                {{ recipient.failure_message }}
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </details>
                                        </div>
                                        <span v-else class="text-sm text-gray-500">Not sent</span>
                                    </td>
                                    <td class="border px-4 py-2 align-top break-words">
                                        <inertia-link class="text-sm mr-2" :href="'/campaigns/'+ campaign.id">
                                            Edit
                                        </inertia-link>

                                        <!-- Send Campaign -->
                                        <button class="cursor-pointer text-sm text-green-500 focus:outline-none"
                                                @click="confirmCampaignSend(campaign)">
                                            {{ campaign.batch > 0 ? 'Resend' : 'Send' }}
                                        </button>

                                        <button v-if="campaign.delivery && campaign.delivery.failed"
                                                class="cursor-pointer text-sm text-red-600 focus:outline-none ml-2"
                                                @click="confirmFailedRetry(campaign, campaign.delivery)">
                                            Retry failed
                                        </button>

                                    </td>
                                </tr>
                            </template>
                            <tr v-else>
                                <td colspan="3" class="border px-8 py-2">No Campaigns</td>
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
                {{ campaignBeingSent && campaignBeingSent.batch > 0 ? 'Resend Campaign' : 'Send Campaign' }}
            </template>

            <template #content>
                <div v-if="campaignBeingSent">
                    <p>
                        Send this campaign to its current group as batch {{ campaignBeingSent.batch + 1 }}?
                    </p>
                    <label v-if="campaignBeingSent.batch > 0" class="flex items-start mt-4 text-sm font-semibold text-red-900">
                        <input type="checkbox" v-model="sendCampaignForm.confirm_resend" class="mt-1 mr-2">
                        <span>I understand this campaign has already been sent and want to resend it.</span>
                    </label>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.native="campaignBeingSent = null">
                    Nevermind
                </jet-secondary-button>

                <jet-confirm-button class="ml-2" @click.native="sendCampaign"
                                   :class="{ 'opacity-25': sendCampaignForm.processing || !canSendCampaign }"
                                   :disabled="sendCampaignForm.processing || !canSendCampaign">
                    {{ campaignBeingSent && campaignBeingSent.batch > 0 ? 'Resend' : 'Send' }}
                </jet-confirm-button>
            </template>
        </jet-dialog-modal>

        <jet-dialog-modal :show="campaignBeingRetried" @close="campaignBeingRetried = null">
            <template #title>Retry Failed Campaign Emails</template>

            <template #content>
                <div v-if="campaignBeingRetried">
                    <p>
                        Retry {{ batchBeingRetried.retryable_failed || batchBeingRetried.failed }} failed email(s) from batch
                        {{ batchBeingRetried.batch }} as a new batch?
                    </p>
                    <p class="mt-3 text-sm text-gray-600">
                        Successfully sent and queued recipients will not receive another email.
                    </p>
                    <label class="flex items-start mt-4 text-sm font-semibold text-red-900">
                        <input type="checkbox" v-model="retryFailedForm.confirm_retry" class="mt-1 mr-2">
                        <span>I understand and want to retry only the failed emails.</span>
                    </label>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.native="campaignBeingRetried = null">
                    Nevermind
                </jet-secondary-button>

                <jet-confirm-button class="ml-2" @click.native="retryFailed"
                                    :class="{ 'opacity-25': retryFailedForm.processing || !retryFailedForm.confirm_retry }"
                                    :disabled="retryFailedForm.processing || !retryFailedForm.confirm_retry">
                    Retry failed
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
                    confirm_resend: false,
                }, {
                    bag: 'sendCampaignInformation',
                    resetOnSuccess: true,
                }),

                retryFailedForm: this.$inertia.form({
                    confirm_retry: false,
                    retry_batch: null,
                }, {
                    bag: 'retryFailedCampaignDeliveries',
                    resetOnSuccess: true,
                }),

                campaignId: null,
                campaignBeingSent: null,
                campaignBeingRetried: null,
                batchBeingRetried: null
            }
        },
        methods: {
            confirmCampaignSend(campaign) {
                this.sendCampaignForm.send = true
                this.sendCampaignForm.confirm_resend = false
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
            confirmFailedRetry(campaign, batch) {
                this.retryFailedForm.confirm_retry = false
                this.retryFailedForm.retry_batch = batch.batch
                this.campaignBeingRetried = campaign
                this.batchBeingRetried = batch
            },
            retryFailed() {
                this.retryFailedForm.post('/campaigns/' + this.campaignBeingRetried.id + '/retry-failed', {
                    preserveScroll: true,
                    preserveState: true,
                }).then(() => {
                    this.campaignBeingRetried = null
                    this.batchBeingRetried = null
                })
            },
            formatDate(value) {
                return value ? new Date(value).toLocaleString() : ''
            },
            deliveryTimestamp(recipient) {
                return recipient.sent_at || recipient.failed_at || recipient.queued_at
            },
            deliveryStatusClass(status) {
                return {
                    'text-green-700': status === 'sent',
                    'text-yellow-700': status === 'queued',
                    'text-red-700': status === 'failed',
                }
            },
        },
        computed: {
            canSendCampaign() {
                return this.campaignBeingSent
                    && (this.campaignBeingSent.batch === 0 || this.sendCampaignForm.confirm_resend)
            },
        }
    }
</script>
