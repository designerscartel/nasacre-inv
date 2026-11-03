<template>
    <app-layout>
        <template #header>
            <edit-page-header
                :title="'Edit booking ' + booking.data.year"
                description="Update booking availability, pricing, venue, and attendee information."
                back-href="/bookings"
                back-label="Back to bookings">
                <template #status>
                    <span v-if="booking.data.active" class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Accepting bookings</span>
                    <span v-else class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded-full">Closed</span>
                </template>
            </edit-page-header>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <update-details-form :booking="booking.data"/>
                <jet-section-border />
            </div>
        </div>

    </app-layout>
</template>

<script>
    import AppLayout from './../../Layouts/AppLayout'
    import EditPageHeader from './../../Components/EditPageHeader'
    import JetSectionBorder from './../../Jetstream/SectionBorder'
    import UpdateDetailsForm from "./UpdateDetailsForm";

    export default {
        props: [
            'booking',
        ],
        components: {
            UpdateDetailsForm,
            EditPageHeader,
            AppLayout,
            JetSectionBorder,
        },
    }
</script>
