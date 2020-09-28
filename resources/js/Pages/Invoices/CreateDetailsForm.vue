<template>
    <jet-form-section @submitted="createInvoiceInformation">
        <template #title>
            Invoice Information
        </template>

        <template #description>
            Update the Invoice's information.
        </template>

        <template #form>


            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="email" value="Email" />
                <jet-input id="email" type="text" class="mt-1 block w-full" v-model="form.email" autocomplete="email" />
                <jet-input-error :message="form.error('email')" class="mt-2" />
            </div>

            <!-- Subs -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="subs" value="Subs"/>
                <jet-input id="subs" type="text" class="mt-1 block w-full" v-model="form.subs"/>
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
                <jet-label for="from" value="From"/>
                <jet-input id="from" type="text" class="mt-1 block w-full" v-model="form.from"/>
                <jet-input-error :message="form.error('from')" class="mt-2"/>
            </div>

            <!-- Message -->
            <div class="col-span-6">
                <jet-label for="message" value="Message"/>
                <v-md-editor v-model="form.message" class="mt-1" height="400px"
                             left-toolbar="undo redo bold italic quote ul ol link"
                ></v-md-editor>
                <jet-input-error :message="form.error('message')" class="mt-2" />
            </div>

        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
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

    export default {
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
            Datepicker,
        },

        props: [
            'invoice',
            'Datepicker'
        ],

        data() {
            return {
                form: this.$inertia.form({
                    '_method': 'POST',
                    invoice_group_id: '',
                    email: '',
                    subs: '',
                    date: '',
                    from: '',
                    message: '',
                }, {
                    bag: 'createInvoiceInformation',
                    resetOnSuccess: false,
                }),
            }
        },
        methods: {
            createInvoiceInformation() {
                this.form.post('/invoices', {
                    preserveScroll: true
                });
            }
        },
    }
</script>
