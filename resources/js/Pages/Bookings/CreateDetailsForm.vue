<template>
    <jet-form-section @submitted="createBookingInformation">
        <template #title>
            Booking Information
        </template>

        <template #description>
            Update the Booking's information.
        </template>

        <template #form>


            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="email" value="Email" />
                <jet-input id="email" type="text" class="mt-1 block w-full" v-model="form.email" autocomplete="email" />
                <jet-input-error :message="form.error('email')" class="mt-2" />
            </div>

            <!-- Subscribed -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="subscribed" value="Subscribed"/>
                <jet-input id="subscribed" type="text" class="mt-1 block w-full" v-model="form.subscribed"/>
                <jet-input-error :message="form.error('subscribed')" class="mt-2"/>
            </div>

            <!-- None Subscribed -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="none_subscribed" value="None Subscribed"/>
                <jet-input id="none_subscribed" type="text" class="mt-1 block w-full" v-model="form.none_subscribed"/>
                <jet-input-error :message="form.error('none_subscribed')" class="mt-2"/>
            </div>

            <!-- Additional -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="additional" value="Additional"/>
                <jet-input id="additional" type="text" class="mt-1 block w-full" v-model="form.additional"/>
                <jet-input-error :message="form.error('additional')" class="mt-2"/>
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

            <!-- Venue -->
            <div class="col-span-6">
                <jet-label for="venue" value="venue"/>
                <v-md-editor v-model="form.venue" class="mt-1" height="400px"
                             left-toolbar="undo redo bold italic quote ul ol link"
                ></v-md-editor>
                <jet-input-error :message="form.error('venue')" class="mt-2" />
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
                    subscribed: '',
                    none_subscribed: '',
                    additional: '',
                    date: '',
                    from: '',
                    message: '',
                    venue: '',
                }, {
                    bag: 'createBookingInformation',
                    resetOnSuccess: false,
                }),
            }
        },
        methods: {
            createBookingInformation() {
                this.form.post('/bookings', {
                    preserveScroll: true
                });
            }
        },
    }
</script>
