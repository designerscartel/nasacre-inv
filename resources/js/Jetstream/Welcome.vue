<template>
    <div>
        <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

            <div class="mt-8 text-2xl">
                Welcome to your Jetstream application!
            </div>

            <div class="mt-6 text-gray-500">
                Laravel Jetstream provides a beautiful, robust starting point for your next Laravel application. Laravel is designed
                to help you build your application using a development environment that is simple, powerful, and enjoyable. We believe
                you should love expressing your creativity through programming, so we have spent time carefully crafting the Laravel
                ecosystem to be a breath of fresh air. We hope you love it.
            </div>
        </div>

        <div class="bg-gray-200 bg-opacity-25">
            <table class="table-auto w-full">
                <thead class="text-left">
                <tr>
                    <th class="px-8 py-2">Sacre</th>
                    <th class="px-4 py-2">Region</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-if="sacres" v-for="sacre in sacres" :key="sacre.id">
                    <td class="border px-8 py-2">{{ sacre.title }}</td>
                    <td class="border px-4 py-2">{{ sacre.region.title }}</td>
                    <td class="border px-4 py-2">
                        <inertia-link :href="'/sacres/'+ sacre.id" >
                            Edit
                        </inertia-link>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>
</template>

<script>
    import JetNavLink from "./NavLink";

    export default {
        components: {
            JetNavLink,
        },
        props: [
            'sacres',
        ],
    }
</script>
