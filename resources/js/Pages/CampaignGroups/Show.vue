<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Campaign Email Emails
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

                        <div class="mt-8 md:flex">

                            <h1 class="text-2xl md:w-5/6">
                                NASACRE Campaign Emails
                            </h1>

                            <div class="md:w-1/6 text-right">
                                <button class="bg-gray-800 hover:bg-gray-7000 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150"
                                        @click="addEmailDialog">
                                    Create email
                                </button>
                            </div>

                        </div>

                    </div>

                    <div class="bg-gray-200 bg-opacity-25">
                        <table class="table-auto w-full">
                            <thead class="text-left">
                            <tr>
                                <th class="px-8 py-2">Name</th>
                                <th class="px-8 py-2">Email</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-if="group.data.emails.length">
                                <tr v-for="email in group.data.emails" :key="email.id">
                                    <td class="border px-8 py-2">{{ email.name }}</td>
                                    <td class="border px-8 py-2">{{ email.email }}</td>
                                    <td class="border px-4 py-2">
                                        <button class="text-sm mr-2" @click="updateEmailDialog(email)">
                                            Edit
                                        </button>

                                        <!-- Delete Email -->
                                        <button class="cursor-pointer text-sm text-red-500 focus:outline-none"
                                                @click="confirmEmailDeletion(email)">
                                            Delete
                                        </button>

                                    </td>
                                </tr>
                            </template>
                            <tr v-else>
                                <td colspan="3" class="border px-8 py-2">No Emails</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

                        <div class="md:flex md:justify-end">

                            <div class="md:w-1/6 text-right">
                                <button class="bg-gray-800 hover:bg-gray-7000 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150"
                                        @click="addEmailDialog">
                                    Create email
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>


        <!-- New Email Modal -->
        <jet-dialog-modal :show="showNewEmailDialog" @close="showNewEmailDialog = false">
            <template #title>
                Add Email
            </template>

            <template #content>

                <!-- Name -->
                <div class="mt-4">
                    <jet-label for="name" value="Name"/>
                    <jet-input id="name" type="text" class="mt-1 block w-full" v-model="addEmailForm.name"/>
                    <jet-input-error :message="addEmailForm.error('name')" class="mt-2"/>
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <jet-label for="email" value="Email"/>
                    <jet-input id="email" type="text" class="mt-1 block w-full" v-model="addEmailForm.email"/>
                    <jet-input-error :message="addEmailForm.error('email')" class="mt-2"/>
                </div>
                
            </template>

            <template #footer>
                <jet-secondary-button @click.native="showNewEmailDialog = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="addEmail" :class="{ 'opacity-25': addEmailForm.processing }"
                            :disabled="addEmailForm.processing">
                    Save
                </jet-button>

            </template>
        </jet-dialog-modal>

        <!-- Update Email Modal -->
        <jet-dialog-modal :show="showUpdateEmailDialog" @close="showUpdateEmailDialog = false">
            <template #title>
                Update Email
            </template>

            <template #content>

                <!-- Name -->
                <div class="mt-4">
                    <jet-label for="name" value="Name"/>
                    <jet-input id="name" type="text" class="mt-1 block w-full" v-model="updateEmailForm.name"/>
                    <jet-input-error :message="updateEmailForm.error('Name')" class="mt-2"/>
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <jet-label for="email" value="Email"/>
                    <jet-input id="email" type="text" class="mt-1 block w-full" v-model="updateEmailForm.email"/>
                    <jet-input-error :message="updateEmailForm.error('email')" class="mt-2"/>
                </div>

            </template>

            <template #footer>
                <jet-secondary-button @click.native="showUpdateEmailDialog = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="updateEmail"
                            :class="{ 'opacity-25': showUpdateEmailDialog.processing }"
                            :disabled="showUpdateEmailDialog.processing">
                    Save
                </jet-button>

            </template>
        </jet-dialog-modal>

        <!-- Delete Email Modal -->
        <jet-confirmation-modal :show="emailBeingDeleted" @close="emailBeingDeleted = null">
            <template #title>
                Delete Email
            </template>

            <template #content>
                Are you sure you would like to delete this email?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="emailBeingDeleted = null">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="deleteEmail"
                                   :class="{ 'opacity-25': deleteEmailForm.processing }"
                                   :disabled="deleteEmailForm.processing">
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
    import JetDialogModal from './../../Jetstream/DialogModal'
    import JetFormSection from './../../Jetstream/FormSection'
    import JetInput from './../../Jetstream/Input'
    import JetSelect from './../../Jetstream/Select'
    import JetInputError from './../../Jetstream/InputError'
    import JetLabel from './../../Jetstream/Label'
    import JetSecondaryButton from './../../Jetstream/SecondaryButton'
    import JetSectionBorder from './../../Jetstream/SectionBorder'

    export default {
        props: [
            'group',
        ],
        components: {
            AppLayout,
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
        data() {
            return {
                addEmailForm: this.$inertia.form({
                    name: '',
                    email: '',
                }, {
                    bag: 'addEmailForm',
                    resetOnSuccess: true,
                }),

                updateEmailForm: this.$inertia.form({
                    name: '',
                    email: '',
                }, {
                    bag: 'updateEmailForm',
                    resetOnSuccess: true,
                }),

                deleteEmailForm: this.$inertia.form(),

                emailId: null,
                showNewEmailDialog: false,
                showUpdateEmailDialog: false,
                emailBeingDeleted: null
            }
        },
        methods: {
            addEmail() {
                this.addEmailForm.post('/groups/' + this.group.data.id + '/emails',  {
                    preserveScroll: true
                }).then(() => {
                    if (!this.addEmailForm.hasErrors()) {
                        this.showNewEmailDialog = false
                    }
                })
            },
            addEmailDialog() {
                this.showNewEmailDialog = true
            },
            updateEmail() {
                this.updateEmailForm.put('/groups/' + this.group.data.id + '/emails/' + this.emailId,  {
                    preserveScroll: true
                }).then(() => {
                    this.emailId = null;
                    if (!this.updateEmailForm.hasErrors()) {
                        this.showUpdateEmailDialog = false
                    }
                })
            },
            updateEmailDialog(email) {
                this.updateEmailForm.id = email.id
                this.updateEmailForm.name = email.name
                this.updateEmailForm.email = email.email
                this.emailId = email.id
                this.showUpdateEmailDialog = true
            },

            confirmEmailDeletion(email) {
                this.emailBeingDeleted = email
            },

            deleteEmail() {
                this.deleteEmailForm.delete('/groups/' + this.group.data.id + '/emails/' +  this.emailBeingDeleted.id, {
                    preserveScroll: true,
                    preserveState: true,
                }).then(() => {
                    this.emailBeingDeleted = null
                })
            },
        }
    }
</script>
