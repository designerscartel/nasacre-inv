<template>
    <jet-form-section sticky-actions @submitted="updateInvoiceInformation">
        <template #title>
            Invoice Information
        </template>

        <template #description>
            Update the invoice sender, subscription amount, date, and message.
        </template>

        <template #form>

            <div class="col-span-6 border-b border-gray-200 pb-2">
                <h3 class="font-semibold text-gray-800">Invoice settings</h3>
                <p class="text-sm text-gray-500 mt-1">Configure the sender, subscription amount, and invoice date.</p>
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="email" value="Sender email"/>
                <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" autocomplete="email"/>
                <p class="text-xs text-gray-500 mt-1">Used as the sender or reply address for invoice emails.</p>
                <jet-input-error :message="form.error('email')" class="mt-2"/>
            </div>

            <!-- Subs -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="subs" value="Subscription amount"/>
                <jet-input id="subs" type="number" step="0.01" class="mt-1 block w-full" v-model="form.subs"/>
                <jet-input-error :message="form.error('subs')" class="mt-2"/>
            </div>

            <!-- Date -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="date" value="Date"/>
                <datepicker input-class="form-input rounded-md shadow-sm mt-1 block w-full" v-model="form.date" name="date"></datepicker>
                <jet-input-error :message="form.error('date')" class="mt-2"/>
            </div>

            <!-- From -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="from" value="Sender name"/>
                <jet-input id="from" type="text" class="mt-1 block w-full" v-model="form.from"/>
                <jet-input-error :message="form.error('from')" class="mt-2"/>
            </div>

            <!-- Message -->
            <div class="col-span-6 border-b border-gray-200 pb-2 mt-2">
                <h3 class="font-semibold text-gray-800">Invoice message</h3>
                <p class="text-sm text-gray-500 mt-1">Edit the message included with the invoice.</p>
            </div>

            <div class="col-span-6">
                <jet-label for="message" value="Message"/>
                <v-md-editor v-model="form.message" class="mt-1" height="400px"
                             left-toolbar="undo redo bold italic quote ul ol link"
                ></v-md-editor>
                <jet-input-error :message="form.error('message')" class="mt-2" />
            </div>

        </template>

        <template #actions>
            <span v-if="hasUnsavedChanges" class="text-sm font-medium text-yellow-700 mr-3">
                Unsaved changes
            </span>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save changes
            </jet-button>
        </template>
    </jet-form-section>
</template>

<script>
    import JetButton from './../../Jetstream/Button'
    import JetFormSection from './../../Jetstream/FormSection'
    import JetInput from './../../Jetstream/Input'
    import JetTextarea from './../../Jetstream/Textarea'
    import JetInputError from './../../Jetstream/InputError'
    import JetSelect from './../../Jetstream/Select'
    import JetLabel from './../../Jetstream/Label'
    import JetActionMessage from './../../Jetstream/ActionMessage'
    import JetSecondaryButton from './../../Jetstream/SecondaryButton'
    import Datepicker from 'vuejs-datepicker';
    import WarnsUnsavedChanges from './../../Mixins/WarnsUnsavedChanges'

    export default {
        mixins: [WarnsUnsavedChanges],
        components: {
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetInput,
            JetTextarea,
            JetSelect,
            JetInputError,
            JetLabel,
            JetSecondaryButton,
            Datepicker
        },

        props: [
            'invoice',
        ],

        data() {
            return {
                form: this.$inertia.form({
                    '_method': 'PUT',
                    email: this.invoice.email,
                    subs: this.invoice.subs,
                    date: this.invoice.date,
                    from: this.invoice.from,
                    message: this.invoice.message,
                }, {
                    bag: 'updateInvoiceInformation',
                    resetOnSuccess: false,
                }),
            }
        },

        methods: {
            updateInvoiceInformation() {
                this.submitWithoutUnsavedWarning(() => this.form.put('/invoices/' + this.invoice.id, {
                    preserveScroll: true
                }));
            },
        },
    }
</script>
