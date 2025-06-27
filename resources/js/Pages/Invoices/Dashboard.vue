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
                                            Send
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
                Send Invoice
            </template>

            <template #content>
                Are you sure you would like to send this Invoice?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="InvoiceBeingSent = null">
                    Nevermind
                </jet-secondary-button>

                <jet-confirm-button class="ml-2" @click.native="sendInvoice"
                                    :class="{ 'opacity-25': sendInvoiceForm.processing }"
                                    :disabled="sendInvoiceForm.processing">
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
                }, {
                    bag: 'sendInvoiceInformation',
                    resetOnSuccess: true,
                }),

                InvoiceId: null,
                InvoiceBeingSent: null
            }
        },
        methods: {
            confirmInvoiceSend(Invoice) {
                this.sendInvoiceForm.send = true
                this.InvoiceBeingSent = Invoice
            },

            sendInvoice() {
                this.sendInvoiceForm.post('/invoices/' +  this.InvoiceBeingSent.id + '/send', {
                    preserveScroll: true,
                    preserveState: true,
                }).then(() => {
                    this.InvoiceBeingSent = null
                })
            },
        }
    }
</script>
