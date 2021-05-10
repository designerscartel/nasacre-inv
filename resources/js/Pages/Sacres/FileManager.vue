<template>

    <div>
        <jet-section-border/>

        <!-- Manage SACRE Files -->
        <jet-action-section class="mt-10 sm:mt-0">
            <template #title>
                SACRE Files
            </template>

            <template #description>
                All of the files that are part of this SACRE.
            </template>

            <!-- File List -->
            <template #content>

                <div class="space-y-6">
                    <div class="flex items-center justify-between" v-for="file in files">

                        <div class="flex items-center">
                            <div class="ml-4 align-middle">
                                <a target="_blank" :href="'/sacres/' + sacre.id + '/files/'+ file.id" >{{ file.filename }}</a>
                            </div>
                        </div>

                        <div class="flex items-center">

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


        <!-- Delete File Modal -->
        <jet-confirmation-modal :show="FileBeingDeleted" @close="FileBeingDeleted = null">
            <template #title>
                Delete SACRE File
            </template>

            <template #content>
                Are you sure you would like to delete this File?
            </template>

            <template #footer>
                <jet-secondary-button @click.native="FileBeingDeleted = null">
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
        'files',
    ],

    data() {
        return {
            addFileForm: this.$inertia.form({
                file: null
            }, {
                bag: 'addFileForm',
                resetOnSuccess: true,
            }),
            deleteFileForm: this.$inertia.form(),
            FileId: null,
            showNewFileDialog: false,
            showUpdateFileDialog: false,
            FileBeingDeleted: null
        }
    },

    methods: {
        addFile() {
            this.addFileForm.post('/sacres/' + this.sacre.id + '/files', {
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
        confirmFileDeletion(File) {
            this.FileBeingDeleted = File
        },
        deleteFile() {

        },
    },
}
</script>
