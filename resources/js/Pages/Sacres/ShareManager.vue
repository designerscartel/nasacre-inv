<template>

    <div>
        <jet-section-border/>

        <!-- Manage SACRE Shared Files -->
        <jet-action-section class="mt-10 sm:mt-0">
            <template #title>
                SACRE Shared Files
            </template>

            <template #description>
                All of the files that are part of this SACRE.
            </template>

            <!-- File List -->
            <template #content>

                <div class="space-y-6">
                    <div class="flex items-center justify-between" v-for="file in shared">

                        <div class="flex items-center">
                            <div class="ml-4 align-middle">
                                {{ file.filename }}
                            </div>
                        </div>

                        <div class="flex items-center">

                            <div class="ml-2 text-sm text-gray-400">
                                <a target="_blank" :href="'/sacres/' + sacre.id + '/shared/'+ file.id"  class="ml-2 text-sm text-gray-400 underline">
                                    View
                                </a>
                            </div>

                            <button class="ml-2 text-sm text-gray-400 underline" @click="updateFileDialog(file)">
                                Edit
                            </button>

                            <button class="cursor-pointer ml-6 text-sm text-red-500 focus:outline-none"
                                    @click="confirmFileDeletion(file)">
                                Delete
                            </button>

                        </div>
                    </div>
                </div>


            </template>

            <template #actions>
                <!-- Add new File dialog-->
                <button
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                    @click="addFileDialog()">
                    Add new File
                </button>

            </template>

        </jet-action-section>


        <!-- New File Modal -->
        <jet-dialog-modal :show="showNewFileDialog" @close="showNewFileDialog = false">
            <template #title>
                Add File
            </template>

            <template #content>

                <input type="file" @input="addFileForm.file = $event.target.files[0]" />
                <progress v-if="addFileForm.progress" :value="addFileForm.progress.percentage" max="100">
                    {{ addFileForm.progress.percentage }}%
                </progress>

            </template>

            <template #footer>
                <jet-secondary-button @click.native="showNewFileDialog = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="addFile" :class="{ 'opacity-25': addFileForm.processing }"
                            :disabled="addFileForm.processing">
                    Save
                </jet-button>

            </template>
        </jet-dialog-modal>

        <!-- Update Contact Modal -->
        <jet-dialog-modal :show="showUpdateFileDialog" @close="showUpdateFileDialog = false">
            <template #title>
                Update File
            </template>

            <template #content>
                <!-- Filename -->
                <div class="mt-4">
                    <jet-label for="filename" value="filename"/>
                    <jet-input id="filename" type="text" class="mt-1 block w-full" v-model="updateFileForm.filename"/>
                    <jet-input-error :message="updateFileForm.error('filename')" class="mt-2"/>
                </div>

            </template>

            <template #footer>
                <jet-secondary-button @click.native="showUpdateFileDialog = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="updateFile"
                            :class="{ 'opacity-25': updateFileForm.processing }"
                            :disabled="updateFileForm.processing">
                    Save
                </jet-button>

            </template>
        </jet-dialog-modal>


        <!-- Delete File Modal -->
        <jet-confirmation-modal :show="fileBeingDeleted" @close="fileBeingDeleted = null">
            <template #title>
                Delete SACRE File
            </template>

            <template #content>
                Are you sure you would like to delete this File?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="fileBeingDeleted = null">
                    Nevermind
                </jet-secondary-button>

                <jet-danger-button class="ml-2" @click.native="deleteFile"
                                   :class="{ 'opacity-25': deleteFileForm.processing }"
                                   :disabled="deleteFileForm.processing">
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
import JetToggle from './../../Jetstream/Toggle'
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
        JetToggle,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
        JetSectionBorder,
    },

    props: [
        'sacre',
        'shared',
    ],

    data() {
        return {
            addFileForm: this.$inertia.form({
                file: null
            }, {
                bag: 'addFileForm',
                resetOnSuccess: true,
            }),

            updateFileForm: this.$inertia.form({
                id: '',
                filename: '',
                year: '',
            }, {
                bag: 'updateFileForm',
                resetOnSuccess: true,
            }),

            deleteFileForm: this.$inertia.form(),
            fileId: null,
            showNewFileDialog: false,
            showUpdateFileDialog: false,
            fileBeingDeleted: null
        }
    },

    methods: {
        addFile() {
            this.addFileForm.post('/sacres/' + this.sacre.id + '/shared', {
                preserveScroll: true
            }).then(() => {
                if (!this.addFileForm.hasErrors()) {
                    this.showNewFileDialog = false
                }
            })
        },
        addFileDialog() {
            this.showNewFileDialog = true
        },
        updateFile() {
            this.updateFileForm.put('/sacres/' + this.sacre.id + '/shared/' + this.fileId, {
                preserveScroll: true
            }).then(() => {
                this.fileId = null;
                if (!this.updateFileForm.hasErrors()) {
                    this.showUpdateFileDialog = false
                }
            })
        },
        updateFileDialog(file) {
            this.updateFileForm.id = file.id
            this.updateFileForm.filename = file.filename
            this.updateFileForm.year = file.year
            this.fileId = file.id
            this.showUpdateFileDialog = true
        },
        confirmFileDeletion(file) {
            this.fileBeingDeleted = file
        },
        deleteFile() {
            this.deleteFileForm.delete('/sacres/' + this.sacre.id + '/shared/' + this.fileBeingDeleted.id, {
                preserveScroll: true,
                preserveState: true,
            }).then(() => {
                this.fileBeingDeleted = null
            })
        },
    },
}
</script>
