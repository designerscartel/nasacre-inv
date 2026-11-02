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
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-if="invoices.data.length">
                                <tr v-if="invoices" v-for="invoice in invoices.data" :key="invoice.id">
                                    <td class="border px-8 py-2">{{ invoice.year }}</td>
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

                                    </td>
                                </tr>
                            </template>
                            <tr v-else>
                                <td colspan="2" class="border px-8 py-2">No Invoices</td>
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

                InvoiceId: null,
                InvoiceBeingSent: null,
                sendPreview: null,
                previewLoading: false,
                previewError: null,
                recipientSelections: [],
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
            formatDate(value) {
                return new Date(value).toLocaleString()
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
        }
    }
</script>
