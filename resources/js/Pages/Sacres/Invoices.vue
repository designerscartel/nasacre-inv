<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ sacre.data.title }} SACRE Invoices
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

                        <div class="mt-8 md:flex">

                            <h1 class="text-2xl md:w-5/6">
                                Invoices
                            </h1>

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
                            <template v-if="sacre.data.invoices.length">
                                <tr v-for="invoice in sacre.data.invoices" :key="invoice.id">
                                    <td class="border px-8 py-2">{{ invoice.invoice.year }}</td>
                                    <td class="border px-4 py-2">
                                        
                                        <button class="mr-2 text-sm text-gray-400 underline" @click="updateInvoiceDialog(invoice)">
                                            Edit
                                        </button>

                                        <a target="_blank" class="text-sm mr-2" :href="'/sacres/'+ sacre.data.id+'/invoices/'+invoice.id+'/pdf'">
                                            PDF
                                        </a>
                                    </td>
                                </tr>
                            </template>
                            <tr v-else>
                                <td colspan="2" class="border px-8 py-2">No invoices</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <!-- Update Invoice Modal -->
        <jet-dialog-modal :show="showUpdateInvoiceDialog" @close="showUpdateInvoiceDialog = false">
            <template #title>
                Update Invoice
            </template>

            <template #content>
                <!-- PO -->
                <div class="mt-4">
                    <jet-label for="po_number" value="Po Number"/>
                    <jet-input id="po_number" type="text" class="mt-1 block w-full" v-model="updateInvoiceForm.po_number"/>
                    <jet-input-error :message="updateInvoiceForm.error('po_number')" class="mt-2"/>
                </div>

                <!-- Address -->
                <div class="mt-4">
                    <jet-label for="address" value="Address"/>
                    <jet-textarea id="address" type="text" class="mt-1 block w-full" v-model="updateInvoiceForm.address"/>
                    <jet-input-error :message="updateInvoiceForm.error('address')" class="mt-2"/>
                </div>

                <!-- Virtual Training -->
                <div class="col-span-6 sm:col-span-4">
                    <jet-toggle type="toggle" class="mt-1 block w-full" v-model="updateInvoiceForm.virtual_training" :label="'Virtual Training'" :forid="'virtual_training'"/>
                    <jet-input-error :message="updateInvoiceForm.error('virtual_training')" class="mt-2" />
                </div>

                <!-- Date -->
                <div class="mt-4">
                    <jet-label for="date" value="Date"/>
                    <datepicker :inline="true" input-class="form-input rounded-md shadow-sm mt-1 block w-full" v-model="updateInvoiceForm.date" name="date"></datepicker>
                    <jet-input-error :message="updateInvoiceForm.error('date')" class="mt-2"/>
                </div>

            </template>

            <template #footer>
                <jet-secondary-button @click.native="showUpdateInvoiceDialog = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="updateInvoice"
                            :class="{ 'opacity-25': updateInvoiceForm.processing }"
                            :disabled="updateInvoiceForm.processing">
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
import JetTextarea from "../../Jetstream/Textarea";
import JetSelect from './../../Jetstream/Select'
import JetInputError from './../../Jetstream/InputError'
import JetLabel from './../../Jetstream/Label'
import JetToggle from './../../Jetstream/Toggle'
import JetSecondaryButton from './../../Jetstream/SecondaryButton'
import JetSectionBorder from './../../Jetstream/SectionBorder'
import Datepicker from 'vuejs-datepicker';


export default {
    props: [
        'sacre',
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
        JetTextarea,
        JetSelect,
        JetToggle,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
        JetSectionBorder,
        Datepicker
    },
    data() {
        return {
            updateInvoiceForm: this.$inertia.form({
                id: '',
                po_number: '',
                address: '',
                date: '',
                virtual_training: ''
            }, {
                bag: 'updateSacreInvoiceInformation',
                resetOnSuccess: true,
            }),

            invoiceId: null,
            showUpdateInvoiceDialog: false,
        }
    },
    methods: {
        updateInvoice() {
            this.updateInvoiceForm.put('/sacres/' + this.sacre.data.id + '/invoices/' + this.invoiceId, {
                preserveScroll: true
            }).then(() => {
                this.InvoiceId = null;
                if (!this.updateInvoiceForm.hasErrors()) {
                    this.showUpdateInvoiceDialog = false
                }
            })
        },
        updateInvoiceDialog(invoice) {
            this.updateInvoiceForm.id = invoice.id
            this.updateInvoiceForm.po_number = invoice.po_number
            this.updateInvoiceForm.address = invoice.address
            this.updateInvoiceForm.date = invoice.date
            this.updateInvoiceForm.virtual_training = invoice.virtual_training
            this.invoiceId = invoice.id
            this.showUpdateInvoiceDialog = true
        },
    },
}
</script>
