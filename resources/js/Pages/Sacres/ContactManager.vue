<template>

    <div>
        <jet-section-border/>

        <!-- Manage SACRE Contacts -->
        <jet-action-section class="mt-10 sm:mt-0">
            <template #title>
                SACRE Contacts
            </template>

            <template #description>
                All of the contacts that are part of this SACRE.
            </template>

            <!-- Contact List -->
            <template #content>

                <div class="space-y-6">
                    <div class="flex items-center justify-between" v-for="contact in contacts">

                        <div class="flex items-center">
                            <div class="ml-4">{{ contact.name }} <span v-if="contact.email">{{ contact.email}}</span></div>
                        </div>

                        <div class="flex items-center">

                            <div class="ml-2 text-sm text-gray-400">
                                {{ contact.position.title }}
                            </div>

                            <button class="ml-2 text-sm text-gray-400 underline" @click="updateContactDialog(contact)">
                                Edit
                            </button>

                            <!-- Delete Contact -->
                            <button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none"
                                    @click="confirmContactDeletion(contact)">
                                Delete
                            </button>

                        </div>
                    </div>
                </div>
            </template>

            <template #actions>
                <!-- Add new contact dialog-->
                <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                        @click="addContactDialog()">
                    Add new contact
                </button>

            </template>


        </jet-action-section>


        <!-- New Contact Modal -->
        <jet-dialog-modal :show="showNewContactDialog" @close="showNewContactDialog = false">
            <template #title>
                Add Contact
            </template>

            <template #content>
                <!-- Name -->
                <div class="mt-4">
                    <jet-label for="name" value="Name"/>
                    <jet-input id="name" type="text" class="mt-1 block w-full" v-model="addContactForm.name"/>
                    <jet-input-error :message="addContactForm.error('name')" class="mt-2"/>
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <jet-label for="email" value="Email"/>
                    <jet-input id="email" type="text" class="mt-1 block w-full" v-model="addContactForm.email"/>
                    <jet-input-error :message="addContactForm.error('email')" class="mt-2"/>
                </div>

                <!-- Telephone -->
                <div class="mt-4">
                    <jet-label for="tel" value="Telephone"/>
                    <jet-input id="tel" type="text" class="mt-1 block w-full" v-model="addContactForm.tel"/>
                    <jet-input-error :message="addContactForm.error('tel')" class="mt-2"/>
                </div>

                <!-- Role Description -->
                <div class="mt-4">
                    <jet-label for="role_dscpn" value="Description"/>
                    <jet-input id="role_dscpn" type="text" class="mt-1 block w-full"
                               v-model="addContactForm.role_dscpn"/>
                    <jet-input-error :message="addContactForm.error('role_dscpn')" class="mt-2"/>
                </div>

                <!-- Position -->
                <div class="mt-4">
                    <jet-label for="position_id" value="Position"/>
                    <jet-select id="position_id" type="select" class="mt-1 block w-full"
                                v-model="addContactForm.position_id" :options="positions"/>
                    <jet-input-error :message="addContactForm.error('position_id')" class="mt-2"/>
                </div>

            </template>

            <template #footer>
                <jet-secondary-button @click.native="showNewContactDialog = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="addContact" :class="{ 'opacity-25': addContactForm.processing }"
                            :disabled="addContactForm.processing">
                    Save
                </jet-button>

            </template>
        </jet-dialog-modal>


        <!-- Update Contact Modal -->
        <jet-dialog-modal :show="showUpdateContactDialog" @close="showUpdateContactDialog = false">
            <template #title>
                Update Contact
            </template>

            <template #content>
                <!-- Name -->
                <div class="mt-4">
                    <jet-label for="name" value="Name"/>
                    <jet-input id="name" type="text" class="mt-1 block w-full" v-model="updateContactForm.name"/>
                    <jet-input-error :message="updateContactForm.error('name')" class="mt-2"/>
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <jet-label for="email" value="Email"/>
                    <jet-input id="email" type="text" class="mt-1 block w-full" v-model="updateContactForm.email"/>
                    <jet-input-error :message="updateContactForm.error('email')" class="mt-2"/>
                </div>

                <!-- Telephone -->
                <div class="mt-4">
                    <jet-label for="tel" value="Telephone"/>
                    <jet-input id="tel" type="text" class="mt-1 block w-full" v-model="updateContactForm.tel"/>
                    <jet-input-error :message="updateContactForm.error('tel')" class="mt-2"/>
                </div>

                <!-- Role Description -->
                <div class="mt-4">
                    <jet-label for="role_dscpn" value="Description"/>
                    <jet-input id="role_dscpn" type="text" class="mt-1 block w-full"
                               v-model="updateContactForm.role_dscpn"/>
                    <jet-input-error :message="updateContactForm.error('role_dscpn')" class="mt-2"/>
                </div>

                <!-- Position -->
                <div class="mt-4">
                    <jet-label for="position_id" value="Position"/>
                    <jet-select id="position_id" type="select" class="mt-1 block w-full"
                                v-model="updateContactForm.position_id" :options="positions"/>
                    <jet-input-error :message="updateContactForm.error('position_id')" class="mt-2"/>
                </div>

            </template>

            <template #footer>
                <jet-secondary-button @click.native="showUpdateContactDialog = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="updateContact"
                            :class="{ 'opacity-25': updateContactForm.processing }"
                            :disabled="updateContactForm.processing">
                    Save
                </jet-button>

            </template>
        </jet-dialog-modal>


        <!-- Delete Contact Modal -->
        <jet-confirmation-modal :show="contactBeingDeleted" @close="contactBeingDeleted = null">
            <template #title>
                Delete SACRE contact
            </template>

            <template #content>
                Are you sure you would like to delete this Contact?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="contactBeingDeleted = null">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="deleteContact"
                                   :class="{ 'opacity-25': deleteContactForm.processing }"
                                   :disabled="deleteContactForm.processing">
                    Delete
                </jet-danger-button>
            </template>
        </jet-confirmation-modal>


    </div>
</template>

<script>
    import JetActionMessage from './../../Jetstream/ActionMessage'
    import JetActionSection from './../../Jetstream/ActionSection'
    import JetButton from './../../Jetstream/Button'
    import JetConfirmationModal from './../../Jetstream/ConfirmationModal'
    import JetDangerButton from './../../Jetstream/DangerButton'
    import JetDialogModal from './../../Jetstream/DialogModal'
    import JetFormSection from './../../Jetstream/FormSection'
    import JetInput from './../../Jetstream/Input'
    import JetSelect from './../../Jetstream/Select'
    import JetInputError from './../../Jetstream/InputError'
    import JetLabel from './../../Jetstream/Label'
    import JetSecondaryButton from './../../Jetstream/SecondaryButton'
    import JetSectionBorder from './../../Jetstream/SectionBorder'

    export default {
        components: {
            JetActionMessage,
            JetActionSection,
            JetButton,
            JetConfirmationModal,
            JetDangerButton,
            JetDialogModal,
            JetFormSection,
            JetInput,
            JetSelect,
            JetInputError,
            JetLabel,
            JetSecondaryButton,
            JetSectionBorder,
        },

        props: [
            'sacre',
            'contacts',
            'positions'
        ],

        data() {
            return {
                addContactForm: this.$inertia.form({
                    name: '',
                    email: '',
                    tel: '',
                    role_dscpn: '',
                    default: '',
                    position_id: '',
                }, {
                    bag: 'addContactForm',
                    resetOnSuccess: true,
                }),

                updateContactForm: this.$inertia.form({
                    id: '',
                    name: '',
                    email: '',
                    tel: '',
                    role_dscpn: '',
                    default: '',
                    position_id: '',
                }, {
                    bag: 'updateContactForm',
                    resetOnSuccess: true,
                }),

                deleteContactForm: this.$inertia.form(),


                contactId: null,
                showNewContactDialog: false,
                showUpdateContactDialog: false,
                contactBeingDeleted: null
            }
        },

        methods: {
            addContact() {
                this.addContactForm.post('/sacres/' + this.sacre.id + '/contacts', {
                    preserveScroll: true
                }).then(() => {
                    this.showNewContactDialog = false
                    if (!this.addContactForm.hasErrors()) {
                        this.showNewContactDialog = false
                    }
                })
            },
            addContactDialog() {
                this.showNewContactDialog = true
            },
            updateContact() {
                this.updateContactForm.put('/sacres/' + this.sacre.id + '/contacts/' + this.contactId, {
                    preserveScroll: true
                }).then(() => {
                    this.contactId = null;
                    if (!this.updateContactForm.hasErrors()) {
                        this.showUpdateContactDialog = false
                    }
                })
            },
            updateContactDialog(contact) {
                this.updateContactForm.id = contact.id
                this.updateContactForm.name = contact.name
                this.updateContactForm.email = contact.email
                this.updateContactForm.tel = contact.tel
                this.updateContactForm.role_dscpn = contact.role_dscpn
                this.updateContactForm.default = contact.default
                this.updateContactForm.position_id = contact.position_id
                this.contactId = contact.id
                this.showUpdateContactDialog = true
            },

            confirmContactDeletion(contact) {
                this.contactBeingDeleted = contact
            },

            deleteContact() {
                this.deleteContactForm.delete('/sacres/' + this.sacre.id + '/contacts/' + this.contactBeingDeleted.id, {
                    preserveScroll: true,
                    preserveState: true,
                }).then(() => {
                    this.contactBeingDeleted = null
                })
            },
        },
    }
</script>
