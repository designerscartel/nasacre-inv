<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                NASACRE Regions
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

                        <div class="mt-8 md:flex">

                            <h1 class="text-2xl md:w-5/6">
                                NASACRE Regions
                            </h1>

                            <div class="md:w-1/6 text-right">
                                <button class="bg-gray-800 hover:bg-gray-7000 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150"
                                        @click="addRegionDialog">
                                    Create region
                                </button>
                            </div>

                        </div>

                    </div>

                    <div class="bg-gray-200 bg-opacity-25">
                        <table class="table-auto w-full">
                            <thead class="text-left">
                            <tr>
                                <th class="px-8 py-2">Region</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <template v-if="regions.data.length">
                                <tr v-for="region in regions.data" :key="region.id">
                                    <td class="border px-8 py-2">{{ region.title }}</td>
                                    <td class="border px-4 py-2">

                                        <button class="text-sm mr-2" @click="updateRegionDialog(region)">
                                            Edit
                                        </button>

                                    </td>
                                </tr>
                            </template>
                            <tr v-else>
                                <td colspan="2" class="border px-8 py-2">No Regions</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

                        <div class="md:flex md:justify-end">

                            <div class="md:w-1/6 text-right">
                                <button class="bg-gray-800 hover:bg-gray-7000 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150"
                                        @click="addRegionDialog">
                                    Create region
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>


        <!-- New Region Modal -->
        <jet-dialog-modal :show="showNewRegionDialog" @close="showNewRegionDialog = false">
            <template #title>
                Add Region
            </template>

            <template #content>
                <!-- Title -->
                <div class="mt-4">
                    <jet-label for="title" value="Title"/>
                    <jet-input id="title" type="text" class="mt-1 block w-full" v-model="addRegionForm.title"/>
                    <jet-input-error :message="addRegionForm.error('title')" class="mt-2"/>
                </div>

            </template>

            <template #footer>
                <jet-secondary-button @click.native="showNewRegionDialog = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="addRegion" :class="{ 'opacity-25': addRegionForm.processing }"
                            :disabled="addRegionForm.processing">
                    Save
                </jet-button>

            </template>
        </jet-dialog-modal>

        <!-- Update Region Modal -->
        <jet-dialog-modal :show="showUpdateRegionDialog" @close="showUpdateRegionDialog = false">
            <template #title>
                Update Region
            </template>

            <template #content>
                <!-- Title -->
                <div class="mt-4">
                    <jet-label for="title" value="Title"/>
                    <jet-input id="title" type="text" class="mt-1 block w-full" v-model="updateRegionForm.title"/>
                    <jet-input-error :message="updateRegionForm.error('title')" class="mt-2"/>
                </div>

            </template>

            <template #footer>
                <jet-secondary-button @click.native="showUpdateRegionDialog = false">
                    Nevermind
                </jet-secondary-button>

                <jet-button class="ml-2" @click.native="updateRegion"
                            :class="{ 'opacity-25': showUpdateRegionDialog.processing }"
                            :disabled="showUpdateRegionDialog.processing">
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
            'regions',
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
                addRegionForm: this.$inertia.form({
                    title: '',
                }, {
                    bag: 'addRegionForm',
                    resetOnSuccess: true,
                }),

                updateRegionForm: this.$inertia.form({
                    title: '',
                }, {
                    bag: 'updateRegionForm',
                    resetOnSuccess: true,
                }),

                regionId: null,
                showNewRegionDialog: false,
                showUpdateRegionDialog: false,
            }
        },
        methods: {
            addRegion() {
                this.addRegionForm.post('/regions', {
                    preserveScroll: true
                }).then(() => {
                    if (!this.addRegionForm.hasErrors()) {
                        this.showNewRegionDialog = false
                    }
                })
            },
            addRegionDialog() {
                this.showNewRegionDialog = true
            },
            updateRegion() {
                this.updateRegionForm.put('/regions/' + this.regionId, {
                    preserveScroll: true
                }).then(() => {
                    this.regionId = null;
                    if (!this.updateRegionForm.hasErrors()) {
                        this.showUpdateRegionDialog = false
                    }
                })
            },
            updateRegionDialog(region) {
                this.updateRegionForm.id = region.id
                this.updateRegionForm.title = region.title
                this.regionId = region.id
                this.showUpdateRegionDialog = true
            },
        }
    }
</script>
