<template>
    <jet-form-section @submitted="sendTestInvoice">
        <template #title>
            Send Test Invoice
        </template>

        <template #description>
            Email a real invoice PDF to one test address before sending a batch. This does not create a delivery record or change the batch number.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="test_invoice_sacre" value="SACRE shown on invoice"/>
                <select id="test_invoice_sacre" v-model="form.sacre_id" class="form-select mt-1 block w-full">
                    <option value="">Select a SACRE</option>
                    <option v-for="sacre in sacres" :key="sacre.id" :value="sacre.id">
                        {{ sacre.title }}
                    </option>
                </select>
                <jet-input-error :message="form.error('sacre_id')" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="test_invoice_email" value="Test recipient email"/>
                <jet-input id="test_invoice_email" type="email" class="mt-1 block w-full" v-model="form.email" autocomplete="email"/>
                <jet-input-error :message="form.error('email')" class="mt-2"/>
            </div>

            <div class="col-span-6 text-sm text-yellow-800 bg-yellow-50 border border-yellow-200 rounded p-3">
                The email subject starts with [TEST]. No finance contacts are copied.
            </div>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Test invoice sent.
            </jet-action-message>

            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Send Test Invoice
            </jet-button>
        </template>
    </jet-form-section>
</template>

<script>
    import JetActionMessage from './../../Jetstream/ActionMessage'
    import JetButton from './../../Jetstream/Button'
    import JetFormSection from './../../Jetstream/FormSection'
    import JetInput from './../../Jetstream/Input'
    import JetInputError from './../../Jetstream/InputError'
    import JetLabel from './../../Jetstream/Label'

    export default {
        components: {
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
        },

        props: ['invoice', 'sacres'],

        data() {
            return {
                form: this.$inertia.form({
                    sacre_id: '',
                    email: '',
                }, {
                    bag: 'sendTestInvoice',
                    resetOnSuccess: false,
                }),
            }
        },

        methods: {
            sendTestInvoice() {
                this.form.post('/invoices/' + this.invoice.id + '/test-email', {
                    preserveScroll: true,
                })
            },
        },
    }
</script>
