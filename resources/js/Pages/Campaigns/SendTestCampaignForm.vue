<template>
    <jet-form-section @submitted="sendTestCampaign">
        <template #title>
            Send Test Campaign
        </template>

        <template #description>
            Email this campaign to one test recipient before sending it to the campaign group. This does not queue a campaign delivery.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="test_campaign_name" value="Test recipient name"/>
                <jet-input id="test_campaign_name" type="text" class="mt-1 block w-full" v-model="form.name" autocomplete="name"/>
                <jet-input-error :message="form.error('name')" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="test_campaign_email" value="Test recipient email"/>
                <jet-input id="test_campaign_email" type="email" class="mt-1 block w-full" v-model="form.email" autocomplete="email"/>
                <jet-input-error :message="form.error('email')" class="mt-2"/>
            </div>

            <div class="col-span-6 text-sm text-yellow-800 bg-yellow-50 border border-yellow-200 rounded p-3">
                The email subject starts with [TEST]. No campaign group recipients are contacted.
            </div>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Test campaign sent.
            </jet-action-message>

            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Send Test Campaign
            </jet-button>
        </template>
    </jet-form-section>
</template>

<script>
    import JetActionMessage from './../../Jetstream/ActionMessage'
    import JetButton from './../../Jetstream/Button'
    import JetFormSection from './../../Jetstream/FormSection'
    import JetInput from './../../Jetstream/Input'
    import JetInputError from './../../Jetstream/InputError'
    import JetLabel from './../../Jetstream/Label'

    export default {
        components: {
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
        },

        props: ['campaign'],

        data() {
            return {
                form: this.$inertia.form({
                    name: '',
                    email: '',
                }, {
                    bag: 'sendTestCampaign',
                    resetOnSuccess: false,
                }),
            }
        },

        methods: {
            sendTestCampaign() {
                this.form.post('/campaigns/' + this.campaign.id + '/test-email', {
                    preserveScroll: true,
                })
            },
        },
    }
</script>
