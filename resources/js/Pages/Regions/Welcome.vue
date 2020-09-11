<template>
    <div>
        <div class="p-6 sm:px-8 bg-white border-b border-gray-200">

            <div class="mt-8 md:flex">

                <h1 class="text-2xl md:w-5/6">
                    NASACRE REGIONS
                </h1>

                <div class="md:w-1/6 text-right">
                    <inertia-link href="/regions/create" class="bg-gray-800 hover:bg-gray-7000 text-xs text-white font-semibold py-2 px-4 rounded transition ease-in-out duration-150">
                        Create region
                    </inertia-link>
                </div>

            </div>

        </div>

        <div class="bg-gray-200 bg-opacity-25">
            <table class="table-auto w-full">
                <thead class="text-left">
                <tr>
                    <th class="px-8 py-2">Region</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-if="regions" v-for="region in regions" :key="region.id">
                    <td class="border px-8 py-2">{{ region.title }}</td>
                    <td class="border px-4 py-2">
                        <inertia-link :href="'/regions/'+ region.id" >
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

    export default {
        props: [
            'regions',
        ],
    }
</script>
