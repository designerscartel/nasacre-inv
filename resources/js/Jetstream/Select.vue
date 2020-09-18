<template>

    <select class="form-input rounded-md shadow-sm" :value="value" @input="$emit('input', $event.target.value)">
        <option disabled value="">Select option</option>
        <option v-for="option in options" v-bind:value="option.id">
            {{ option.title }}
        </option>
    </select>

</template>

<script>
    export default {
        props: ['value', 'options'],

        methods: {
            focus() {
                this.$refs.input.focus()
            }
        },
        created() {

        }
    }
</script>