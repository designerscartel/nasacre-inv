<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                NASACRE Bookings
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

                        <div class="mt-8 md:flex">

                            <h1 class="text-2xl md:w-5/6">
                                NASACRE Bookings
                            </h1>


                            <div class="md:w-2/6 text-right">
                                <a :href="'/bookings/'+ booking.data.id+'/csv'"
                                              class="bg-gray-800 hover:bg-gray-7000 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150">
                                    Download CSV of confirmed bookings
                                </a>
                            </div>

                        </div>

                    </div>

                    <div class="bg-gray-200 bg-opacity-25">
                        <table class="table-auto w-full">
                            <thead class="text-left">
                            <tr>
                                <th class="px-8 py-2">Booking</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-if="booking.data.bookings.length">
                                <tr v-for="bookingObj in booking.data.bookings" :key="bookingObj.id">
                                    <td class="border px-8 py-2">{{ bookingObj.name }}</td>
                                    <td class="border px-4 py-2">
                                        <button class="ml-2 text-sm text-gray-400 underline"
                                                @click="updateBookingDialog(bookingObj)">
                                            Edit
                                        </button>
                                        <a target="_blank" class="text-sm mr-2" :href="'/bookings/'+ bookingObj.id+'/pdf'">
                                            PDF
                                        </a>
                                        <button class="cursor-pointer text-sm text-red-500 focus:outline-none"
                                                @click="confirmBookingDeletion(bookingObj)">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr v-else>
                                <td colspan="2" class="border px-8 py-2">No Bookings</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

                        <div class="md:flex md:justify-end">

                            <div class="md:w-1/6 text-right">

                            </div>

                        </div>
                    </div>


                </div>
            </div>
        </div>


        <!-- Update Booking Modal -->
        <jet-dialog-modal max-width="7xl" :show="showUpdateBookingDialog" @close="showUpdateBookingDialog = false">
            <template #title>
                Update Booking
            </template>

            <template #content>

                <div class="md:grid md:grid-cols-2 md:gap-6">

                    <!-- PO Number -->
                    <div class="mt-4">
                        <jet-label for="po_number" value="PO Number"/>
                        <jet-input id="po_number" type="text" class="mt-1 block w-full" v-model="updateBookingForm.po_number"/>
                        <jet-input-error :message="updateBookingForm.error('po_number')" class="mt-2"/>
                    </div>

                    <!-- Name -->
                    <div class="mt-4">
                        <jet-label for="name" value="Name"/>
                        <jet-input id="name" type="text" class="mt-1 block w-full" v-model="updateBookingForm.name"/>
                        <jet-input-error :message="updateBookingForm.error('name')" class="mt-2"/>
                    </div>

                    <!-- Email -->
                    <div class="mt-4">
                        <jet-label for="email" value="Email"/>
                        <jet-input id="email" type="text" class="mt-1 block w-full" v-model="updateBookingForm.email"/>
                        <jet-input-error :message="updateBookingForm.error('email')" class="mt-2"/>
                    </div>

                    <!-- Phone -->
                    <div class="mt-4">
                        <jet-label for="phone" value="Phone"/>
                        <jet-input id="phone" type="text" class="mt-1 block w-full" v-model="updateBookingForm.phone"/>
                        <jet-input-error :message="updateBookingForm.error('phone')" class="mt-2"/>
                    </div>

                    <!-- Delegate One Name -->
                    <div class="mt-4">
                        <jet-label for="delegate_one_name" value="Delegate One Name"/>
                        <jet-input id="delegate_one_name" type="text" class="mt-1 block w-full" v-model="updateBookingForm.delegate_one_name"/>
                        <jet-input-error :message="updateBookingForm.error('delegate_one_name')" class="mt-2"/>
                    </div>

                    <!-- Delegate One Email -->
                    <div class="mt-4">
                        <jet-label for="delegate_one_email" value="Delegate One Email"/>
                        <jet-input id="delegate_one_email" type="text" class="mt-1 block w-full" v-model="updateBookingForm.delegate_one_email"/>
                        <jet-input-error :message="updateBookingForm.error('delegate_one_email')" class="mt-2"/>
                    </div>

                    <!-- Delegate One Diet -->
                    <div class="mt-4">
                        <jet-label for="delegate_one_diet" value="Delegate One Diet"/>
                        <jet-input id="delegate_one_diet" type="text" class="mt-1 block w-full" v-model="updateBookingForm.delegate_one_diet"/>
                        <jet-input-error :message="updateBookingForm.error('delegate_one_diet')" class="mt-2"/>
                    </div>


                    <!-- Delegate Two Name -->
                    <div class="mt-4">
                        <jet-label for="delegate_two_name" value="Delegate Two Name"/>
                        <jet-input id="delegate_two_name" type="text" class="mt-1 block w-full" v-model="updateBookingForm.delegate_two_name"/>
                        <jet-input-error :message="updateBookingForm.error('delegate_two_name')" class="mt-2"/>
                    </div>

                    <!-- Delegate Two Email -->
                    <div class="mt-4">
                        <jet-label for="delegate_two_email" value="Delegate Two Email"/>
                        <jet-input id="delegate_two_email" type="text" class="mt-1 block w-full" v-model="updateBookingForm.delegate_two_email"/>
                        <jet-input-error :message="updateBookingForm.error('delegate_two_email')" class="mt-2"/>
                    </div>

                    <!-- Delegate Two Diet -->
                    <div class="mt-4">
                        <jet-label for="delegate_two_diet" value="Delegate Two Diet"/>
                        <jet-input id="delegate_two_diet" type="text" class="mt-1 block w-full" v-model="updateBookingForm.delegate_two_diet"/>
                        <jet-input-error :message="updateBookingForm.error('delegate_two_diet')" class="mt-2"/>
                    </div>


                    <!-- Virtual One Name -->
                    <div class="mt-4">
                        <jet-label for="virtual_one_name" value="Virtual One Name"/>
                        <jet-input id="virtual_one_name" type="text" class="mt-1 block w-full" v-model="updateBookingForm.virtual_one_name"/>
                        <jet-input-error :message="updateBookingForm.error('virtual_one_name')" class="mt-2"/>
                    </div>

                    <!-- Virtual One Email -->
                    <div class="mt-4">
                        <jet-label for="virtual_one_email" value="Virtual One Email"/>
                        <jet-input id="virtual_one_email" type="text" class="mt-1 block w-full" v-model="updateBookingForm.virtual_one_email"/>
                        <jet-input-error :message="updateBookingForm.error('virtual_one_email')" class="mt-2"/>
                    </div>

                    <!-- Virtual Two Name -->
                    <div class="mt-4">
                        <jet-label for="virtual_two_name" value="Virtual Two Name"/>
                        <jet-input id="virtual_two_name" type="text" class="mt-1 block w-full" v-model="updateBookingForm.virtual_two_name"/>
                        <jet-input-error :message="updateBookingForm.error('virtual_two_name')" class="mt-2"/>
                    </div>

                    <!-- Virtual Two Email -->
                    <div class="mt-4">
                        <jet-label for="virtual_two_email" value="Virtual Two Email"/>
                        <jet-input id="virtual_two_email" type="text" class="mt-1 block w-full" v-model="updateBookingForm.virtual_two_email"/>
                        <jet-input-error :message="updateBookingForm.error('virtual_two_email')" class="mt-2"/>
                    </div>

                </div>

                <!-- Confirmed -->
                <div class="mt-4">
                    <jet-toggle type="toggle" class="mt-1 block w-full" v-model="updateBookingForm.confirmed" :label="'Confirmed'" :forid="'confirmed'"/>
                    <jet-input-error :message="updateBookingForm.error('confirmed')" class="mt-2" />
                </div>

            </template>

            <template #footer>
                <jet-secondary-button @click.native="showUpdateBookingDialog = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="updateBooking"
                            :class="{ 'opacity-25': updateBookingForm.processing }"
                            :disabled="updateBookingForm.processing">
                    Save
                </jet-button>

            </template>
        </jet-dialog-modal>

        <!-- Delete Booking Modal -->
        <jet-confirmation-modal :show="bookingBeingDeleted" @close="bookingBeingDeleted = null">
            <template #title>
                Delete Booking
            </template>

            <template #content>
                Are you sure you would like to delete this Booking?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="bookingBeingDeleted = null">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="deleteBooking"
                                   :class="{ 'opacity-25': deleteBookingForm.processing }"
                                   :disabled="deleteBookingForm.processing">
                    Delete
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>


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
import JetToggle from "../../Jetstream/Toggle";

export default {
    props: [
        'booking',
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
        JetToggle,
    },
    data() {
        return {
            deleteBookingForm: this.$inertia.form(),
            updateBookingForm: this.$inertia.form({
                id: '',
                po_number: '',
                name: '',
                email: '',
                phone: '',
                delegate_one_name: '',
                delegate_one_email: '',
                delegate_one_diet: '',
                delegate_two_name: '',
                delegate_two_email: '',
                delegate_two_diet: '',
                virtual_one_name: '',
                virtual_one_email: '',
                virtual_two_name: '',
                virtual_two_email: '',
                confirmed: '',
            }, {
                bag: 'updateBookingForm',
                resetOnSuccess: true,
            }),

            bookingBeingDeleted: null,
            bookingId: null,
            showUpdateBookingDialog: false,
        }
    },

    methods: {

        confirmBookingDeletion(booking) {
            this.bookingBeingDeleted = booking
        },

        deleteBooking() {
            this.deleteBookingForm.delete('/bookings/' + this.booking.data.id + '/delete/' + this.bookingBeingDeleted.id, {
                preserveScroll: true,
                preserveState: true,
            }).then(() => {
                this.bookingBeingDeleted = null
            })
        },

        updateBooking() {
            this.updateBookingForm.put('/bookings/' + this.booking.data.id + '/amend/' + this.bookingId, {
                preserveScroll: true
            }).then(() => {
                this.bookingId = null;
                if (!this.updateBookingForm.hasErrors()) {
                    this.showUpdateBookingDialog = false
                }
            })
        },
        updateBookingDialog(booking) {
            this.updateBookingForm.id = booking.id
            this.updateBookingForm.po_number = booking.po_number
            this.updateBookingForm.name = booking.name
            this.updateBookingForm.email = booking.email
            this.updateBookingForm.phone = booking.phone
            this.updateBookingForm.date = booking.date
            this.updateBookingForm.delegate_one_name = booking.delegate_one_name
            this.updateBookingForm.delegate_one_email = booking.delegate_one_email
            this.updateBookingForm.delegate_one_diet = booking.delegate_one_diet
            this.updateBookingForm.delegate_two_name = booking.delegate_two_name
            this.updateBookingForm.delegate_two_email = booking.delegate_two_email
            this.updateBookingForm.delegate_two_diet = booking.delegate_two_diet
            this.updateBookingForm.virtual_one_name = booking.virtual_one_name
            this.updateBookingForm.virtual_one_email = booking.virtual_one_email
            this.updateBookingForm.virtual_two_name = booking.virtual_two_name
            this.updateBookingForm.virtual_two_email = booking.virtual_two_email
            this.updateBookingForm.confirmed = booking.confirmed
            this.bookingId = booking.id
            this.showUpdateBookingDialog = true
        },

    },

}
</script>
