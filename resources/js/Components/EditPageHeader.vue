<template>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="min-w-0">
            <inertia-link :href="backHref" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; {{ backLabel }}
            </inertia-link>
            <div class="flex flex-wrap items-center gap-3 mt-2">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight break-words">{{ title }}</h2>
                <slot name="status"></slot>
            </div>
            <p v-if="description" class="text-sm text-gray-500 mt-1">{{ description }}</p>
        </div>

        <div v-if="$slots.actions" class="flex-shrink-0">
            <slot name="actions"></slot>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            title: String,
            description: String,
            backHref: String,
            backLabel: String,
        },
    }
</script>
