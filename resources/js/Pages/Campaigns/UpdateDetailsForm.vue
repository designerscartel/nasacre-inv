<template>
    <jet-form-section @submitted="updateSacreInformation">
        <template #title>
            Campaign Information
        </template>

        <template #description>
            Update the Campaign's information.
        </template>

        <template #form>

            <!-- Group -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="campaign_group_id" value="Group"/>
                <jet-select id="campaign_group_id" type="select" class="mt-1 block w-full"
                            v-model="form.campaign_group_id" :options="groups"/>
                <jet-input-error :message="form.error('campaign_group_id')" class="mt-2"/>
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="email" value="Email" />
                <jet-input id="email" type="text" class="mt-1 block w-full" v-model="form.email" autocomplete="email" />
                <jet-input-error :message="form.error('email')" class="mt-2" />
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
                <jet-input id="url" type="text" class="mt-1 block w-full" v-model="form.url"/>
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
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save
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

    export default {
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
            updateSacreInformation() {
                this.form.put('/campaigns/' + this.campaign.id, {
                    preserveScroll: true
                });
            },
        },
    }
</script>
