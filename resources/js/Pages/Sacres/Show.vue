<template>
    <app-layout>
        <template #header>
            <edit-page-header
                :title="sacre.data.title"
                description="Manage SACRE details, contacts, files, and shared resources."
                back-href="/dashboard"
                back-label="Back to SACRE directory">
                <template #status>
                    <span v-if="sacre.data.member" class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Member</span>
                    <span v-else class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded-full">Non-member</span>
                </template>
            </edit-page-header>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

                <update-details-form
                        :sacre="sacre.data"
                        :regions="regions.data"/>

                <contact-manager
                        :sacre="sacre.data"
                        :contacts="sacre.data.contacts"
                        :positions="positions.data"/>

                <file-manager
                    :sacre="sacre.data"
                    :files="sacre.data.files"/>

                <share-manager
                    :sacre="sacre.data"
                    :shared="sacre.data.shared"/>

            </div>
        </div>

    </app-layout>
</template>

<script>
    import AppLayout from './../../Layouts/AppLayout'
    import EditPageHeader from './../../Components/EditPageHeader'
    import JetSectionBorder from './../../Jetstream/SectionBorder'
    import UpdateDetailsForm from "./UpdateDetailsForm";
    import ContactManager from "./ContactManager";
    import FileManager from "./FileManager";
    import ShareManager from "./ShareManager";

    export default {
        props: [
            'sacre',
            'regions',
            'positions',
        ],
        components: {
            UpdateDetailsForm,
            EditPageHeader,
            AppLayout,
            JetSectionBorder,
            ContactManager,
            FileManager,
            ShareManager
        },
    }
</script>
