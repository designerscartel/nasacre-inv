<template>
    <jet-form-section @submitted="updateSacreInformation">
        <template #title>
            Invoice Information
        </template>

        <template #description>
            Update the Invoice's information.
        </template>

        <template #form>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="email" value="Email"/>
                <jet-input id="email" type="text" class="mt-1 block w-full" v-model="form.email" autocomplete="email"/>
                <jet-input-error :message="form.error('email')" class="mt-2"/>
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
                <jet-input id="date" type="text" class="mt-1 block w-full" v-model="form.date"/>
                <jet-input-error :message="form.error('date')" class="mt-2"/>
            </div>

            <!-- Year -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="year" value="Year"/>
                <jet-input id="year" type="text" class="mt-1 block w-full" v-model="form.year"/>
                <jet-input-error :message="form.error('year')" class="mt-2"/>
            </div>

            <!-- From -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="from" value="From"/>
                <jet-input id="from" type="text" class="mt-1 block w-full" v-model="form.from"/>
                <jet-input-error :message="form.error('from')" class="mt-2"/>
            </div>

            <!-- Message -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="message" value="Message"/>
                <jet-textarea id="message" type="text" class="mt-1 block w-full" v-model="form.message"/>
                <jet-input-error :message="form.error('message')" class="mt-2"/>
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
                    year: this.invoice.year,
                    from: this.invoice.from,
                    message: this.invoice.message,
                }, {
                    bag: 'updateInvoiceInformation',
                    resetOnSuccess: false,
                }),
            }
        },

        methods: {
            updateSacreInformation() {
                this.form.put('/invoices/' + this.invoice.id, {
                    preserveScroll: true
                });
            },
        },
    }
</script>
