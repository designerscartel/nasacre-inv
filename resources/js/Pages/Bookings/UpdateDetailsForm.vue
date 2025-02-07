<template>
    <jet-form-section @submitted="updateSacreInformation">
        <template #title>
            Booking Information
        </template>

        <template #description>
            Update the Booking's information.
        </template>

        <template #form>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="email" value="Email"/>
                <jet-input id="email" type="text" class="mt-1 block w-full" v-model="form.email" autocomplete="email"/>
                <jet-input-error :message="form.error('email')" class="mt-2"/>
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
                <jet-label for="additional_amount" value="Additional Amount"/>
                <jet-input id="additional_amount" type="text" class="mt-1 block w-full" v-model="form.additional_amount"/>
                <jet-input-error :message="form.error('additional_amount')" class="mt-2"/>
            </div>

            <!-- New Style -->
            <div class="col-span-6 sm:col-span-4">
                <label class="flex items-center w-6/12 mt-2">
                    <input type="checkbox" class="form-checkbox" v-model="form.new">
                    <span class="ml-2 text-sm text-gray-600">New Booking System</span>
                </label>
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
                <v-md-editor v-model="form.message" class="mt-1" height="400px" left-toolbar="undo redo bold italic quote ul ol link"
                ></v-md-editor>
                <jet-input-error :message="form.error('message')" class="mt-2" />
            </div>

            <!-- Venue -->
            <div class="col-span-6">
                <jet-label for="venue" value="Venue"/>
                <v-md-editor v-model="form.venue" class="mt-1" height="400px" left-toolbar="undo redo bold italic quote ul ol link"
                ></v-md-editor>
                <jet-input-error :message="form.error('venue')" class="mt-2" />
            </div>

            <!-- Member Subs -->
            <div class="col-span-6">
                <jet-label for="member" value="Member Price"/>

                <template v-for="sub, index in form.memberSubs" :key="index">
                    <div class="flex flex-wrap items-center">
                        <p class="w-3/12 pr-3">Up to {{ (index + 1) }} Delegates</p>
                        <div class="w-6/12 pr-3">
                            <jet-input id="from" type="text" class="mt-1 block w-full" v-model="sub.sub"/>
                        </div>
                        <div class="w-3/12 flex flex-wrap">
                            <span class="text-green-500 cursor-pointer mr-3" @click="addMemberSub" >Add</span>
                            <span v-show="index != 0" icon="square-minus mr-3" class="text-red-500 cursor-pointer" @click="removeMemberSub(index)">Remove</span>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Subs -->
            <div class="col-span-6">
                <jet-label for="subs" value="None Member Price"/>

                <template v-for="sub, index in form.subs" :key="index">
                    <div class="flex flex-wrap items-center">
                        <p class="w-3/12 pr-3">Up to {{ (index + 1) }} Delegates</p>
                        <div class="w-6/12 pr-3">
                            <jet-input id="from" type="text" class="mt-1 block w-full" v-model="sub.sub"/>
                        </div>
                        <div class="w-3/12 flex flex-wrap">
                            <span class="text-green-500 cursor-pointer mr-3" @click="addSub" >Add</span>
                            <span v-show="index != 0" icon="square-minus mr-3" class="text-red-500 cursor-pointer" @click="removeSub(index)">Remove</span>
                        </div>
                    </div>
                </template>
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
            Datepicker
        },

        props: [
            'booking',
        ],

        data() {
            return {
                form: this.$inertia.form({
                    '_method': 'PUT',
                    email: this.booking.email,
                    subscribed: this.booking.subscribed,
                    none_subscribed: this.booking.none_subscribed,
                    additional_amount: this.booking.additional_amount,
                    date: this.booking.date,
                    from: this.booking.from,
                    message: this.booking.message,
                    venue: this.booking.venue,
                    new: this.booking.new,
                    subs: this.booking.subs,
                    memberSubs: this.booking.memberSubs,
                }, {
                    bag: 'updateBookingInformation',
                    resetOnSuccess: false,
                }),
            }
        },

        methods: {
            updateSacreInformation() {
                this.form.put('/bookings/' + this.booking.id, {
                    preserveScroll: true
                });
            },
            addSub() {
                this.form.subs.push({sub: ""})
            },
            removeSub(index) {
                this.form.subs.splice(index, 1)
            },
            addMemberSub() {
                this.form.memberSubs.push({sub: ""})
            },
            removeMemberSub(index) {
                this.form.memberSubs.splice(index, 1)
            },
        },
    }
</script>
