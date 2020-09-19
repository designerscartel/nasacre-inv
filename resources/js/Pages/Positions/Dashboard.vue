<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                NASACRE Positions
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

                        <div class="mt-8 md:flex">

                            <h1 class="text-2xl md:w-5/6">
                                NASACRE Positions
                            </h1>

                            <div class="md:w-1/6 text-right">
                                <button class="bg-gray-800 hover:bg-gray-7000 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150"
                                        @click="addPositionDialog">
                                    Create position
                                </button>
                            </div>

                        </div>

                    </div>

                    <div class="bg-gray-200 bg-opacity-25">
                        <table class="table-auto w-full">
                            <thead class="text-left">
                            <tr>
                                <th class="px-8 py-2">Position</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-if="positions.data.length">
                                <tr v-if="positions" v-for="position in positions.data" :key="position.id">
                                    <td class="border px-8 py-2">{{ position.title }}</td>
                                    <td class="border px-4 py-2">

                                        <button class="text-sm mr-2" @click="updatePositionDialog(position)">
                                            Edit
                                        </button>

                                    </td>
                                </tr>
                            </template>
                            <tr v-else>
                                <td colspan="2" class="border px-8 py-2">No Positions</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

                        <div class="md:flex md:justify-end">

                            <div class="md:w-1/6 text-right">
                                <button class="bg-gray-800 hover:bg-gray-7000 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150"
                                        @click="addPositionDialog">
                                    Create position
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- New Position Modal -->
        <jet-dialog-modal :show="showNewPositionDialog" @close="showNewPositionDialog = false">
            <template #title>
                Add Position
            </template>

            <template #content>
                <!-- Title -->
                <div class="mt-4">
                    <jet-label for="title" value="Title"/>
                    <jet-input id="title" type="text" class="mt-1 block w-full" v-model="addPositionForm.title"/>
                    <jet-input-error :message="addPositionForm.error('title')" class="mt-2"/>
                </div>

            </template>

            <template #footer>
                <jet-secondary-button @click.native="showNewPositionDialog = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="addPosition" :class="{ 'opacity-25': addPositionForm.processing }"
                            :disabled="addPositionForm.processing">
                    Save
                </jet-button>

            </template>
        </jet-dialog-modal>

        <!-- Update Position Modal -->
        <jet-dialog-modal :show="showUpdatePositionDialog" @close="showUpdatePositionDialog = false">
            <template #title>
                Update Position
            </template>

            <template #content>
                <!-- Title -->
                <div class="mt-4">
                    <jet-label for="title" value="Title"/>
                    <jet-input id="title" type="text" class="mt-1 block w-full" v-model="updatePositionForm.title"/>
                    <jet-input-error :message="updatePositionForm.error('title')" class="mt-2"/>
                </div>

            </template>

            <template #footer>
                <jet-secondary-button @click.native="showUpdatePositionDialog = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="updatePosition"
                            :class="{ 'opacity-25': showUpdatePositionDialog.processing }"
                            :disabled="showUpdatePositionDialog.processing">
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
            'positions',
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
                addPositionForm: this.$inertia.form({
                    title: '',
                }, {
                    bag: 'addPositionForm',
                    resetOnSuccess: true,
                }),

                updatePositionForm: this.$inertia.form({
                    title: '',
                }, {
                    bag: 'updatePositionForm',
                    resetOnSuccess: true,
                }),

                positionId: null,
                showNewPositionDialog: false,
                showUpdatePositionDialog: false,
            }
        },
        methods: {
            addPosition() {
                this.addPositionForm.post('/positions', {
                    preserveScroll: true
                }).then(() => {
                    if (!this.addPositionForm.hasErrors()) {
                        this.showNewPositionDialog = false
                    }
                })
            },
            addPositionDialog() {
                this.showNewPositionDialog = true
            },
            updatePosition() {
                this.updatePositionForm.put('/positions/' + this.positionId, {
                    preserveScroll: true
                }).then(() => {
                    this.positionId = null;
                    if (!this.updatePositionForm.hasErrors()) {
                        this.showUpdatePositionDialog = false
                    }
                })
            },
            updatePositionDialog(position) {
                this.updatePositionForm.id = position.id
                this.updatePositionForm.title = position.title
                this.positionId = position.id
                this.showUpdatePositionDialog = true
            },
        }
    }
</script>
