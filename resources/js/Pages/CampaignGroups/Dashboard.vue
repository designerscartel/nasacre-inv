<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                NASACRE Campaign Groups
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

                        <div class="mt-8 md:flex">

                            <h1 class="text-2xl md:w-5/6">
                                NASACRE Campaign Groups
                            </h1>

                            <div class="md:w-1/6 text-right">
                                <button class="bg-gray-800 hover:bg-gray-7000 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150"
                                        @click="addGroupDialog">
                                    Create group
                                </button>
                            </div>

                        </div>

                    </div>

                    <div class="bg-gray-200 bg-opacity-25">
                        <table class="table-auto w-full">
                            <thead class="text-left">
                            <tr>
                                <th class="px-8 py-2">Group</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-if="groups.data.length">
                                <tr v-for="group in groups.data" :key="group.id">
                                    <td class="border px-8 py-2">{{ group.title }}</td>
                                    <td class="border px-4 py-2">
                                        <button class="text-sm mr-2" @click="updateGroupDialog(group)">
                                            Edit
                                        </button>

                                        <inertia-link class="text-sm" :href="'/groups/'+ group.id">
                                            Emails
                                        </inertia-link>
                                    </td>
                                </tr>
                            </template>
                            <tr v-else>
                                <td colspan="2" class="border px-8 py-2">No Groups</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

                        <div class="md:flex md:justify-end">

                            <div class="md:w-1/6 text-right">
                                <button class="bg-gray-800 hover:bg-gray-7000 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150"
                                        @click="addGroupDialog">
                                    Create group
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>


        <!-- New Group Modal -->
        <jet-dialog-modal :show="showNewGroupDialog" @close="showNewGroupDialog = false">
            <template #title>
                Add Group
            </template>

            <template #content>
                <!-- Title -->
                <div class="mt-4">
                    <jet-label for="title" value="Title"/>
                    <jet-input id="title" type="text" class="mt-1 block w-full" v-model="addGroupForm.title"/>
                    <jet-input-error :message="addGroupForm.error('title')" class="mt-2"/>
                </div>

            </template>

            <template #footer>
                <jet-secondary-button @click.native="showNewGroupDialog = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="addGroup" :class="{ 'opacity-25': addGroupForm.processing }"
                            :disabled="addGroupForm.processing">
                    Save
                </jet-button>

            </template>
        </jet-dialog-modal>

        <!-- Update Group Modal -->
        <jet-dialog-modal :show="showUpdateGroupDialog" @close="showUpdateGroupDialog = false">
            <template #title>
                Update Group
            </template>

            <template #content>
                <!-- Title -->
                <div class="mt-4">
                    <jet-label for="title" value="Title"/>
                    <jet-input id="title" type="text" class="mt-1 block w-full" v-model="updateGroupForm.title"/>
                    <jet-input-error :message="updateGroupForm.error('title')" class="mt-2"/>
                </div>

            </template>

            <template #footer>
                <jet-secondary-button @click.native="showUpdateGroupDialog = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="updateGroup"
                            :class="{ 'opacity-25': showUpdateGroupDialog.processing }"
                            :disabled="showUpdateGroupDialog.processing">
                    Save
                </jet-button>

            </template>
        </jet-dialog-modal>

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
            'groups',
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
                addGroupForm: this.$inertia.form({
                    title: '',
                }, {
                    bag: 'addGroupForm',
                    resetOnSuccess: true,
                }),

                updateGroupForm: this.$inertia.form({
                    title: '',
                }, {
                    bag: 'updateGroupForm',
                    resetOnSuccess: true,
                }),

                contactId: null,
                showNewGroupDialog: false,
                showUpdateGroupDialog: false,
            }
        },
        methods: {
            addGroup() {
                this.addGroupForm.post('/groups', {
                    preserveScroll: true
                }).then(() => {
                    this.showNewGroupDialog = false
                    if (!this.addGroupForm.hasErrors()) {
                        this.showNewGroupDialog = false
                    }
                })
            },
            addGroupDialog() {
                this.showNewGroupDialog = true
            },
            updateGroup() {
                this.updateGroupForm.put('/groups/' + this.groupId, {
                    preserveScroll: true
                }).then(() => {
                    this.groupId = null;
                    if (!this.updateGroupForm.hasErrors()) {
                        this.showUpdateGroupDialog = false
                    }
                })
            },
            updateGroupDialog(group) {
                this.updateGroupForm.id = group.id
                this.updateGroupForm.title = group.title
                this.groupId = group.id
                this.showUpdateGroupDialog = true
            },
        }
    }
</script>
