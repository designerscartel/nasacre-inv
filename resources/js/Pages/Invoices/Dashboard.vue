<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                NASACRE Invoices
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

                        <div class="mt-8 md:flex">

                            <h1 class="text-2xl md:w-5/6">
                                NASACRE Invoices
                            </h1>


                            <div class="md:w-2/6 text-right">

                                <button class="text-sm mr-4 text-gray-600 focus:outline-none" @click="refreshStatuses">
                                    Refresh status
                                </button>

                                <inertia-link href="/invoices/create"
                                              class="bg-gray-800 hover:bg-gray-7000 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150">
                                    Create invoice
                                </inertia-link>

                            </div>

                        </div>

                    </div>

                    <div class="bg-gray-200 bg-opacity-25">
                        <table class="table-auto w-full">
                            <thead class="text-left">
                            <tr>
                                <th class="px-8 py-2">Invoice</th>
                                <th class="px-4 py-2">Latest Delivery</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-if="invoices.data.length">
                                <tr v-if="invoices" v-for="invoice in invoices.data" :key="invoice.id">
                                    <td class="border px-8 py-2">{{ invoice.year }}</td>
                                    <td class="border px-4 py-2">
                                        <div v-if="invoice.delivery">
                                            <div class="text-sm font-semibold">Batch {{ invoice.delivery.batch }}</div>
                                            <div class="text-xs mt-1">
                                                <span class="text-green-700 mr-3">{{ invoice.delivery.sent }} sent</span>
                                                <span class="text-yellow-700 mr-3">{{ invoice.delivery.queued }} queued</span>
                                                <span class="text-red-700">{{ invoice.delivery.failed }} failed</span>
                                            </div>

                                            <details v-if="invoice.delivery.queued || invoice.delivery.failed" class="mt-2 text-xs">
                                                <summary class="cursor-pointer text-gray-700">View delivery details</summary>
                                                <ul class="mt-2 space-y-2">
                                                    <li v-for="recipient in invoice.delivery.recipients" :key="recipient.id">
                                                        <span :class="deliveryStatusClass(recipient.status)" class="font-semibold uppercase">
                                                            {{ recipient.status }}
                                                        </span>
                                                        — {{ recipient.sacre || 'Unknown SACRE' }}
                                                        <span v-if="recipient.email"> ({{ recipient.email }})</span>
                                                        <div v-if="recipient.failure_message" class="text-red-700 mt-1">
                                                            {{ recipient.failure_message }}
                                                        </div>
                                                    </li>
                                                </ul>
                                            </details>
                                        </div>
                                        <span v-else class="text-sm text-gray-500">Not sent</span>
                                    </td>
                                    <td class="border px-4 py-2">
                                        <inertia-link class="text-sm mr-2" :href="'/invoices/'+ invoice.id">
                                            Edit
                                        </inertia-link>

                                        <a target="_blank" class="text-sm mr-2" :href="'/invoices/'+ invoice.id+'/list'">
                                            List Invoices
                                        </a>

                                        <!-- Send Invoices -->
                                        <button class="cursor-pointer text-sm text-green-500 focus:outline-none"
                                                @click="confirmInvoiceSend(invoice)">
                                            {{ invoice.batch > 0 ? 'Resend' : 'Send' }}
                                        </button>

                                        <button v-if="invoice.delivery && invoice.delivery.failed > 0"
                                                class="cursor-pointer text-sm text-red-600 focus:outline-none ml-2"
                                                @click="confirmFailedRetry(invoice)">
                                            Retry failed
                                        </button>

                                    </td>
                                </tr>
                            </template>
                            <tr v-else>
                                <td colspan="3" class="border px-8 py-2">No Invoices</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

                        <div class="md:flex md:justify-end">

                            <div class="md:w-1/6 text-right">
                                <inertia-link href="/invoices/create"
                                              class="bg-gray-800 hover:bg-gray-7000 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150">
                                    Create invoice
                                </inertia-link>
                            </div>

                        </div>
                    </div>


                </div>
            </div>
        </div>

        <!-- Send Invoice Modal -->
        <jet-dialog-modal :show="InvoiceBeingSent" @close="InvoiceBeingSent = null">
            <template #title>
                {{ sendPreview && sendPreview.has_been_sent ? 'Resend Invoice' : 'Send Invoice' }}
            </template>

            <template #content>
                <div v-if="previewLoading" class="text-gray-600">
                    Loading send preview...
                </div>

                <div v-else-if="previewError" class="text-red-600">
                    {{ previewError }}
                </div>

                <div v-else-if="sendPreview">
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <div class="text-xs uppercase text-gray-500">Batch</div>
                            <div class="text-lg font-semibold">{{ sendPreview.batch }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase text-gray-500">Recipients</div>
                            <div class="text-lg font-semibold text-green-700">{{ selectedRecipientCount }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase text-gray-500">Skipped</div>
                            <div class="text-lg font-semibold text-yellow-700">{{ sendPreview.skipped_count }}</div>
                        </div>
                    </div>

                    <div v-if="sendPreview.has_been_sent" class="mb-4 border border-red-300 bg-red-50 rounded p-4">
                        <div class="font-semibold text-red-800">This invoice has already been sent</div>
                        <div class="text-sm text-red-700 mt-1">Sending now will create batch {{ sendPreview.batch }}.</div>

                        <div v-if="sendPreview.previous_batches.length" class="mt-3">
                            <div class="text-sm font-semibold text-red-800 mb-1">Previous batches</div>
                            <ul class="text-sm text-red-800 space-y-1">
                                <li v-for="batch in sendPreview.previous_batches" :key="batch.batch">
                                    Batch {{ batch.batch }} — {{ batch.recipient_count }} recorded recipient<span v-if="batch.recipient_count !== 1">s</span>
                                    <span v-if="batch.sent_at"> — {{ formatDate(batch.sent_at) }}</span>
                                </li>
                            </ul>
                        </div>

                        <label class="flex items-start mt-4 text-sm font-semibold text-red-900">
                            <input type="checkbox" v-model="sendInvoiceForm.confirm_resend" class="mt-1 mr-2">
                            <span>I understand this invoice has already been sent and want to resend it.</span>
                        </label>
                    </div>

                    <div v-if="recipientSelections.length" class="mb-4">
                        <div class="font-semibold mb-2">Recipients</div>
                        <ul class="border rounded divide-y max-h-80 overflow-y-auto">
                            <li v-for="recipient in recipientSelections" :key="recipient.sacre_id" class="px-3 py-3">
                                <label class="flex items-start">
                                    <input type="checkbox" v-model="recipient.selected" class="mt-1 mr-3">
                                    <span class="flex-1">
                                        <span class="block font-medium">{{ recipient.sacre }}</span>
                                        <select v-model="recipient.contact_id"
                                                :disabled="!recipient.selected"
                                                class="form-select mt-2 block w-full text-sm">
                                            <option v-for="contact in recipient.contacts" :key="contact.id" :value="contact.id">
                                                {{ contact.name || contact.email }} — {{ contact.email }}
                                            </option>
                                        </select>
                                    </span>
                                </label>
                            </li>
                        </ul>
                    </div>

                    <div v-if="sendPreview.skipped.length" class="mb-4">
                        <div class="font-semibold mb-2">Skipped SACREs</div>
                        <ul class="border rounded divide-y max-h-64 overflow-y-auto">
                            <li v-for="skipped in sendPreview.skipped" :key="skipped.sacre_id" class="px-3 py-2">
                                <div class="font-medium">{{ skipped.sacre }}</div>
                                <div class="text-sm text-gray-600">{{ skipped.reason }}</div>
                            </li>
                        </ul>
                    </div>

                    <div v-if="selectedRecipientCount">
                        Send this invoice to {{ selectedRecipientCount }} recipient<span v-if="selectedRecipientCount !== 1">s</span>?
                    </div>
                    <div v-else class="text-red-600 font-semibold">
                        There are no valid recipients for this invoice.
                    </div>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.native="InvoiceBeingSent = null">
                    Nevermind
                </jet-secondary-button>

                <jet-confirm-button class="ml-2" @click.native="sendInvoice"
                                    :class="{ 'opacity-25': sendInvoiceForm.processing || !canSendInvoice }"
                                    :disabled="sendInvoiceForm.processing || !canSendInvoice">
                    {{ sendPreview && sendPreview.has_been_sent ? 'Resend' : 'Send' }}
                </jet-confirm-button>
            </template>
        </jet-dialog-modal>

        <!-- Retry Failed Invoice Deliveries Modal -->
        <jet-dialog-modal :show="InvoiceBeingRetried" @close="closeFailedRetry">
            <template #title>
                Retry Failed Invoice Deliveries
            </template>

            <template #content>
                <div v-if="retryPreviewLoading" class="text-gray-600">
                    Loading failed deliveries...
                </div>

                <div v-else-if="retryPreviewError" class="text-red-600">
                    {{ retryPreviewError }}
                </div>

                <div v-else-if="retryPreview">
                    <div class="mb-4">
                        Retry {{ retryPreview.failed_count }} failed recipient<span v-if="retryPreview.failed_count !== 1">s</span>
                        from batch {{ retryPreview.batch }}. This does not create a new batch.
                    </div>

                    <ul v-if="retryPreview.recipients.length" class="border rounded divide-y max-h-80 overflow-y-auto mb-4">
                        <li v-for="recipient in retryPreview.recipients" :key="recipient.id" class="px-3 py-3">
                            <div class="font-medium">{{ recipient.sacre || 'Unknown SACRE' }}</div>
                            <div class="text-sm text-gray-600">{{ recipient.email }}</div>
                            <div v-if="recipient.failure_message" class="text-sm text-red-700 mt-1">
                                {{ recipient.failure_message }}
                            </div>
                        </li>
                    </ul>

                    <label v-if="retryPreview.failed_count" class="flex items-start text-sm font-semibold text-red-900">
                        <input type="checkbox" v-model="retryFailedForm.confirm_retry" class="mt-1 mr-2">
                        <span>I confirm these failed invoice deliveries should be queued again.</span>
                    </label>

                    <div v-else class="text-gray-600">
                        There are no failed deliveries remaining in the latest batch.
                    </div>
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click.native="closeFailedRetry">
                    Nevermind
                </jet-secondary-button>

                <jet-confirm-button class="ml-2" @click.native="retryFailedDeliveries"
                                    :class="{ 'opacity-25': retryFailedForm.processing || !canRetryFailed }"
                                    :disabled="retryFailedForm.processing || !canRetryFailed">
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
            'invoices',
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

                sendInvoiceForm: this.$inertia.form({
                    send: '',
                    recipients: [],
                    confirm_resend: false,
                }, {
                    bag: 'sendInvoiceInformation',
                    resetOnSuccess: true,
                }),

                retryFailedForm: this.$inertia.form({
                    confirm_retry: false,
                }, {
                    bag: 'retryFailedInvoiceDeliveries',
                    resetOnSuccess: true,
                }),

                InvoiceId: null,
                InvoiceBeingSent: null,
                sendPreview: null,
                previewLoading: false,
                previewError: null,
                recipientSelections: [],
                InvoiceBeingRetried: null,
                retryPreview: null,
                retryPreviewLoading: false,
                retryPreviewError: null,
                statusRefreshTimer: null,
            }
        },
        methods: {
            confirmInvoiceSend(Invoice) {
                this.sendInvoiceForm.send = true
                this.InvoiceBeingSent = Invoice
                this.sendPreview = null
                this.recipientSelections = []
                this.sendInvoiceForm.confirm_resend = false
                this.previewError = null
                this.previewLoading = true

                axios.get('/invoices/' + Invoice.id + '/send-preview')
                    .then(response => {
                        this.sendPreview = response.data
                        this.recipientSelections = response.data.recipients.map(recipient => ({
                            ...recipient,
                            selected: true,
                        }))
                    })
                    .catch(() => {
                        this.previewError = 'The send preview could not be loaded. Please try again.'
                    })
                    .then(() => {
                        this.previewLoading = false
                    })
            },

            sendInvoice() {
                this.sendInvoiceForm.recipients = this.recipientSelections
                    .filter(recipient => recipient.selected)
                    .map(recipient => ({
                        sacre_id: recipient.sacre_id,
                        contact_id: recipient.contact_id,
                    }))
                this.sendInvoiceForm.post('/invoices/' +  this.InvoiceBeingSent.id + '/send', {
                    preserveScroll: true,
                    preserveState: true,
                }).then(() => {
                    this.InvoiceBeingSent = null
                })
            },
            confirmFailedRetry(invoice) {
                this.InvoiceBeingRetried = invoice
                this.retryPreview = null
                this.retryPreviewError = null
                this.retryPreviewLoading = true
                this.retryFailedForm.confirm_retry = false

                axios.get('/invoices/' + invoice.id + '/retry-failed-preview')
                    .then(response => {
                        this.retryPreview = response.data
                    })
                    .catch(() => {
                        this.retryPreviewError = 'The failed delivery preview could not be loaded. Please try again.'
                    })
                    .then(() => {
                        this.retryPreviewLoading = false
                    })
            },
            closeFailedRetry() {
                this.InvoiceBeingRetried = null
                this.retryPreview = null
                this.retryPreviewError = null
                this.retryFailedForm.confirm_retry = false
            },
            retryFailedDeliveries() {
                this.retryFailedForm.post('/invoices/' + this.InvoiceBeingRetried.id + '/retry-failed', {
                    preserveScroll: true,
                    preserveState: true,
                }).then(() => {
                    this.closeFailedRetry()
                })
            },
            formatDate(value) {
                return new Date(value).toLocaleString()
            },
            refreshStatuses() {
                this.$inertia.reload({
                    only: ['invoices'],
                    preserveScroll: true,
                    preserveState: true,
                })
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
            selectedRecipientCount() {
                return this.recipientSelections.filter(recipient => recipient.selected).length
            },
            canSendInvoice() {
                return this.sendPreview
                    && this.selectedRecipientCount > 0
                    && !this.previewLoading
                    && (!this.sendPreview.has_been_sent || this.sendInvoiceForm.confirm_resend)
            },
            canRetryFailed() {
                return this.retryPreview
                    && this.retryPreview.failed_count > 0
                    && this.retryFailedForm.confirm_retry
                    && !this.retryPreviewLoading
            },
            hasQueuedInvoices() {
                return this.invoices.data.some(invoice => invoice.delivery && invoice.delivery.queued > 0)
            },
        },
        mounted() {
            this.statusRefreshTimer = window.setInterval(() => {
                if (this.hasQueuedInvoices) {
                    this.refreshStatuses()
                }
            }, 10000)
        },
        beforeDestroy() {
            window.clearInterval(this.statusRefreshTimer)
        }
    }
</script>
