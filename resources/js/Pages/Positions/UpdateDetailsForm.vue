<template>
    <jet-form-section @submitted="updateSacreInformation">
        <template #title>
            Position Information
        </template>

        <template #description>
            Update the Position's information and contacts.
        </template>

        <template #form>

            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="title" value="Title" />
                <jet-input id="title" type="text" class="mt-1 block w-full" v-model="form.title" autocomplete="title" />
                <jet-input-error :message="form.error('title')" class="mt-2" />
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

        props: ['position'],

        data() {
            return {
                form: this.$inertia.form({
                    '_method': 'PUT',
                    title: this.position.title,
                }, {
                    bag: 'updatePositionInformation',
                    resetOnSuccess: false,
                }),
            }
        },

        methods: {
            updateSacreInformation() {
                this.form.put('/positions/' + this.position.id, {
                    preserveScroll: true
                });
            },
        },
    }
</script>
