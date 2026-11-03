<template>
    <jet-form-section sticky-actions @submitted="updateSacreInformation">
        <template #title>
            SACRE Information
        </template>

        <template #description>
            Update the SACRE's identity, region, membership, and finance details.
        </template>

        <template #form>

            <div class="col-span-6 border-b border-gray-200 pb-2">
                <h3 class="font-semibold text-gray-800">Identity and region</h3>
                <p class="text-sm text-gray-500 mt-1">Manage the SACRE name, region, codes, and address.</p>
            </div>


            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="title" value="SACRE name" />
                <jet-input id="title" type="text" class="mt-1 block w-full" v-model="form.title" autocomplete="title" />
                <jet-input-error :message="form.error('title')" class="mt-2" />
            </div>

            <!-- Region -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="region_id" value="Region" />
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
            <div class="col-span-6 border-b border-gray-200 pb-2 mt-2">
                <h3 class="font-semibold text-gray-800">Membership and services</h3>
                <p class="text-sm text-gray-500 mt-1">Record current membership and enabled services.</p>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-toggle type="toggle" class="mt-1 block w-full" v-model="form.member" :label="'Member'" :forid="'member'"/>
                <jet-input-error :message="form.error('member')" class="mt-2" />
            </div>

            <!-- Past Member -->
            <div class="col-span-6 sm:col-span-4">
                <jet-toggle type="toggle" class="mt-1 block w-full" v-model="form.past_member" :label="'Past Member'" :forid="'past_member'"/>
                <jet-input-error :message="form.error('past_member')" class="mt-2" />
            </div>

            <!-- Virtual Training -->
            <div class="col-span-6 sm:col-span-4">
                <jet-toggle type="toggle" class="mt-1 block w-full" v-model="form.virtual_training" :label="'Virtual Training'" :forid="'virtual_training'"/>
                <jet-input-error :message="form.error('virtual_training')" class="mt-2" />
            </div>


            <!-- PO -->
            <div class="col-span-6 border-b border-gray-200 pb-2 mt-2">
                <h3 class="font-semibold text-gray-800">Finance details</h3>
                <p class="text-sm text-gray-500 mt-1">Store purchase order and finance references used for invoicing.</p>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="po" value="Purchase order reference" />
                <jet-input id="po" type="text" class="mt-1 block w-full" v-model="form.po" />
                <jet-input-error :message="form.error('po')" class="mt-2" />
            </div>

            <!-- Finance -->
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="finance" value="Finance reference" />
                <jet-input id="finance" type="text" class="mt-1 block w-full" v-model="form.finance" />
                <jet-input-error :message="form.error('finance')" class="mt-2" />
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
    import JetToggle from './../../Jetstream/Toggle'
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
            JetToggle,
            JetInputError,
            JetLabel,
            JetSecondaryButton,
        },

        props: ['sacre', 'regions'],

        data() {
            return {
                form: this.$inertia.form({
                    '_method': 'PUT',
                    region_id: this.sacre.region_id,
                    title: this.sacre.title,
                    member: this.sacre.member,
                    past_member: this.sacre.past_member,
                    address: this.sacre.address,
                    short_code: this.sacre.short_code,
                    code: this.sacre.code,
                    po: this.sacre.po,
                    finance: this.sacre.finance,
                    virtual_training: this.sacre.virtual_training,
                }, {
                    bag: 'updateSacreBookingInformation',
                    resetOnSuccess: false,
                }),
            }
        },

        methods: {
            updateSacreInformation() {
                this.submitWithoutUnsavedWarning(() => this.form.put('/sacres/' + this.sacre.id, {
                    preserveScroll: true
                }));
            },
        },
    }
</script>
