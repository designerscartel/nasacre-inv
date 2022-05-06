<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit SACRE
            </h2>
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
            AppLayout,
            JetSectionBorder,
            ContactManager,
            FileManager,
            ShareManager
        },
    }
</script>
