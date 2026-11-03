<template>
    <app-layout>
        <template #header>
            <edit-page-header
                :title="'Edit invoice ' + invoice.data.year"
                description="Update invoice content and send a safe test before delivery."
                back-href="/invoices"
                back-label="Back to invoices"/>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

                <update-details-form
                        :invoice="invoice.data"/>

                <jet-section-border />

                <send-test-invoice-form
                        :invoice="invoice.data"
                        :sacres="sacres"/>

                <jet-section-border />

            </div>
        </div>


    </app-layout>
</template>

<script>
    import AppLayout from './../../Layouts/AppLayout'
    import EditPageHeader from './../../Components/EditPageHeader'
    import JetSectionBorder from './../../Jetstream/SectionBorder'
    import UpdateDetailsForm from "./UpdateDetailsForm";
    import SendTestInvoiceForm from "./SendTestInvoiceForm";

    export default {
        props: [
            'invoice',
            'sacres',
        ],
        components: {
            UpdateDetailsForm,
            SendTestInvoiceForm,
            EditPageHeader,
            AppLayout,
            JetSectionBorder,
        },
    }
</script>
