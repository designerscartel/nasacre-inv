<template>
    <jet-form-section @submitted="createSacreInformation">
        <template #title>
            Sacre Information
        </template>

        <template #description>
            Update the Sacre's information and contacts.
        </template>

        <template #form>


            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="title" value="Title" />
                <jet-input id="title" type="text" class="mt-1 block w-full" v-model="form.title" autocomplete="title" />
                <jet-input-error :message="form.error('title')" class="mt-2" />
            </div>

            <!-- Region -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="region_id" value="region" />
                <jet-select id="region_id" type="select" class="mt-1 block w-full" v-model="form.region_id" :options="regions" />
                <jet-input-error :message="form.error('region_id')" class="mt-2" />
            </div>

            <!-- Short Code -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="short_code" value="Short Code" />
                <jet-input id="short_code" type="text" class="mt-1 block w-full" v-model="form.short_code" autocomplete="short_code" />
                <jet-input-error :message="form.error('short_code')" class="mt-2" />
            </div>

            <!-- Code -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="code" value="Code" />
                <jet-input id="code" type="text" class="mt-1 block w-full" v-model="form.code" autocomplete="code" />
                <jet-input-error :message="form.error('code')" class="mt-2" />
            </div>

            <!-- Address -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="address" value="Address" />
                <jet-textarea id="address" type="text" class="mt-1 block w-full" v-model="form.address" />
                <jet-input-error :message="form.error('address')" class="mt-2" />
            </div>

            <!-- Member -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="member" value="Member" />
                <jet-input id="member" type="text" class="mt-1 block w-full" v-model="form.member" />
                <jet-input-error :message="form.error('member')" class="mt-2" />
            </div>

            <!-- PO -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="po" value="PO" />
                <jet-input id="po" type="text" class="mt-1 block w-full" v-model="form.po" />
                <jet-input-error :message="form.error('po')" class="mt-2" />
            </div>

            <!-- Finance -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="finance" value="Finance" />
                <jet-input id="finance" type="text" class="mt-1 block w-full" v-model="form.finance" />
                <jet-input-error :message="form.error('finance')" class="mt-2" />
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

        props: ['sacre', 'regions'],

        data() {
            return {
                form: this.$inertia.form({
                    '_method': 'POST',
                    region_id: '',
                    title: '',
                    member:'',
                    address: '',
                    short_code: '',
                    code: '',
                    po: '',
                    finance: '',
                }, {
                    bag: 'createSacreInformation',
                    resetOnSuccess: false,
                }),
            }
        },

        methods: {
            createSacreInformation() {
                this.form.post('/sacres', {
                    preserveScroll: true
                });
            },
        },
    }
</script>
