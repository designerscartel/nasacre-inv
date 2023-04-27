<template>
    <label v-bind:for="forid" class="mt-2 inline-flex items-center cursor-pointer font-medium text-sm text-gray-700"
           @click="change()">
        <div class="inline relative">
            <div class="w-10 h-6 bg-gray-200 rounded-full shadow-inner"></div>
            <span
                class="absolute block w-4 h-4 mt-1 ml-1 rounded-full shadow inset-y-0 left-0 focus-within:shadow-outline transition-transform duration-300 ease-in-out"
                :class="alignmentClasses">
                <input type="checkbox" class="absolute opacity-0 w-0 h-0"/>
            </span>
        </div>
        <span class="ml-3">{{ label }}</span>
    </label>
</template>

<script>
export default {
    props: ['value', 'label', 'forid'],
    data() {
        return {
            checked: this.value
        }
    },
    methods: {
        focus() {
            this.$refs.input.focus()
        },
        change: function () {
            this.checked = !this.checked;
            this.$emit('input', this.checked);
        }
    },
    created() {
        if (this.value) {
            this.checked = true;
        }
    },
    computed: {
        alignmentClasses() {
            if (this.checked) {
                return 'bg-green-500 transform translate-x-full'
            } else {
                return 'bg-white'
            }
        }
    },
    watch: {
        value (val) {
            if (val) {
                this.checked = true;
            } else {
                this.checked = false;
            }
        }
    }
}
</script>