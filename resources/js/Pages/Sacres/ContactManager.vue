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

                <!-- Add new contact dialog-->
                <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                        @click="addContactDialog()">
                        Add new contact
                </button>

                <div class="space-y-6">
                    <div class="flex items-center justify-between" v-for="contact in contacts">
                        <div class="flex items-center">
                            <div class="ml-4">{{ contact.name }}</div>
                        </div>
                        <div class="flex items-center">

                            <!-- Manage Team Member Role -->
                            <!--
                            <div class="ml-2 text-sm text-gray-400" v-else-if="availableRoles.length > 0">
                                {{ displayableRole(user.membership.role) }}
                            </div>
                            -->
                            <!-- Remove Team Member -->
                            <!--
                            <button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none"
                                    @click="confirmTeamMemberRemoval(user)"
                                    v-if="userPermissions.canRemoveTeamMembers">
                                Remove
                            </button>
                            -->
                        </div>
                    </div>
                </div>
            </template>
        </jet-action-section>


        <!-- Role Management Modal -->
        <jet-dialog-modal :show="newContactDialog" @close="newContactDialog = false">
            <template #title>
                Add Contact
            </template>

            <template #content>
                Add contact form

                <!-- Name -->
                <div class="mt-4">
                    <jet-label for="name" value="Name" />
                    <jet-input id="name" type="text" class="mt-1 block w-full" v-model="addContactForm.name" />
                    <jet-input-error :message="addContactForm.error('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <jet-label for="email" value="Email" />
                    <jet-input id="email" type="text" class="mt-1 block w-full" v-model="addContactForm.email" />
                    <jet-input-error :message="addContactForm.error('email')" class="mt-2" />
                </div>

                <!-- Telephone -->
                <div class="mt-4">
                    <jet-label for="tel" value="Telephone" />
                    <jet-input id="tel" type="text" class="mt-1 block w-full" v-model="addContactForm.tel" />
                    <jet-input-error :message="addContactForm.error('tel')" class="mt-2" />
                </div>

                <!-- Role Description -->
                <div class="mt-4">
                    <jet-label for="role_dscpn" value="Description" />
                    <jet-input id="role_dscpn" type="text" class="mt-1 block w-full" v-model="addContactForm.role_dscpn" />
                    <jet-input-error :message="addContactForm.error('role_dscpn')" class="mt-2" />
                </div>

                <!-- Position -->
                <div class="mt-4">
                    <jet-label for="position_id" value="Position" />
                    <jet-select id="position_id" type="select" class="mt-1 block w-full" v-model="addContactForm.position_id" :options="positions" />
                    <jet-input-error :message="addContactForm.error('position_id')" class="mt-2" />
                </div>

            </template>

            <template #footer>
                <jet-secondary-button @click.native="newContactDialog = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="addContact" :class="{ 'opacity-25': addContactForm.processing }" :disabled="addContactForm.processing">
                    Save
                </jet-button>

            </template>
        </jet-dialog-modal>


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

                newContactDialog: false,
            }
        },

        methods: {
            addContact() {
                this.addContactForm.post('/sacres/' + this.sacre.id + '/contacts', {
                    preserveScroll: true
                }).then(() => {
                    this.newContactDialog = false
                })
            },
            addContactDialog() {
                this.newContactDialog = true
            },
        },
    }
</script>
