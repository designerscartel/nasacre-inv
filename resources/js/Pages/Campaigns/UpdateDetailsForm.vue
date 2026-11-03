<template>
    <jet-form-section sticky-actions @submitted="updateCampaignInformation">
        <template #title>
            Campaign Information
        </template>

        <template #description>
            Update the campaign sender, audience, subject, and content.
        </template>

        <template #form>

            <div class="col-span-6 border-b border-gray-200 pb-2">
                <h3 class="font-semibold text-gray-800">Delivery settings</h3>
                <p class="text-sm text-gray-500 mt-1">Choose the audience and sender details used for this campaign.</p>
            </div>

            <!-- Group -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="campaign_group_id" value="Group"/>
                <jet-select id="campaign_group_id" type="select" class="mt-1 block w-full"
                            v-model="form.campaign_group_id" :options="groups"/>
                <jet-input-error :message="form.error('campaign_group_id')" class="mt-2"/>
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="email" value="Sender email" />
                <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" autocomplete="email" />
                <p class="text-xs text-gray-500 mt-1">Shown as the sender or reply address on campaign emails.</p>
                <jet-input-error :message="form.error('email')" class="mt-2" />
            </div>

            <div class="col-span-6 border-b border-gray-200 pb-2 mt-2">
                <h3 class="font-semibold text-gray-800">Campaign content</h3>
                <p class="text-sm text-gray-500 mt-1">Set the email subject, destination link, and message.</p>
            </div>

            <!-- Subject -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="subject" value="Subject"/>
                <jet-input id="subject" type="text" class="mt-1 block w-full" v-model="form.subject"/>
                <jet-input-error :message="form.error('subject')" class="mt-2"/>
            </div>

            <!-- Url -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="url" value="URL"/>
                <jet-input id="url" type="url" class="mt-1 block w-full" v-model="form.url"/>
                <p class="text-xs text-gray-500 mt-1">Use the full address, including https://.</p>
                <jet-input-error :message="form.error('url')" class="mt-2"/>
            </div>

            <!-- Message -->
            <div class="col-span-6">
                <jet-label for="message" value="Message"/>
                <v-md-editor v-model="form.message" class="mt-1" height="400px"
                             left-toolbar="undo redo bold italic quote ul ol link"
                ></v-md-editor>
                <jet-input-error :message="form.error('message')" class="mt-2" />
            </div>

        </template>

        <template #actions>
            <span v-if="hasUnsavedChanges" class="text-sm font-medium text-yellow-700 mr-3">
                Unsaved changes
            </span>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save changes
            </jet-button>
        </template>
    </jet-form-section>
</template>

<script>
    import JetButton from './../../Jetstream/Button'
    import JetFormSection from './../../Jetstream/FormSection'
    import JetInput from './../../Jetstream/Input'
    import JetTextarea from './../../Jetstream/Textarea'
    import JetInputError from './../../Jetstream/InputError'
    import JetSelect from './../../Jetstream/Select'
    import JetLabel from './../../Jetstream/Label'
    import JetActionMessage from './../../Jetstream/ActionMessage'
    import JetSecondaryButton from './../../Jetstream/SecondaryButton'
    import WarnsUnsavedChanges from './../../Mixins/WarnsUnsavedChanges'

    export default {
        mixins: [WarnsUnsavedChanges],
        components: {
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetInput,
            JetTextarea,
            JetSelect,
            JetInputError,
            JetLabel,
            JetSecondaryButton,
        },

        props: [
            'campaign',
            'groups'
        ],

        data() {
            return {
                form: this.$inertia.form({
                    '_method': 'PUT',
                    campaign_group_id: this.campaign.campaign_group_id,
                    email: this.campaign.email,
                    subject: this.campaign.subject,
                    url: this.campaign.url,
                    message: this.campaign.message,
                }, {
                    bag: 'updateCampaignInformation',
                    resetOnSuccess: false,
                }),
            }
        },

        methods: {
            updateCampaignInformation() {
                this.submitWithoutUnsavedWarning(() => this.form.put('/campaigns/' + this.campaign.id, {
                    preserveScroll: true
                }));
            },
        },
    }
</script>
