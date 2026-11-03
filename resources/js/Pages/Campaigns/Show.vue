<template>
    <app-layout>
        <template #header>
            <edit-page-header
                :title="campaign.data.subject"
                :description="'Edit campaign for ' + campaign.data.group.title + ' and send a test before delivery.'"
                back-href="/campaigns"
                back-label="Back to campaigns"/>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

                <update-details-form
                        :campaign="campaign.data"
                        :groups="groups.data"/>

                <jet-section-border />

                <send-test-campaign-form :campaign="campaign.data"/>

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
    import SendTestCampaignForm from "./SendTestCampaignForm";

    export default {
        props: [
            'campaign',
            'groups'
        ],
        components: {
            UpdateDetailsForm,
            SendTestCampaignForm,
            EditPageHeader,
            AppLayout,
            JetSectionBorder,
        },
    }
</script>
