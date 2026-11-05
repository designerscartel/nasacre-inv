<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100 px-4 py-12">
        <div class="w-full max-w-md">
            <a href="/" class="block mx-auto mb-8 w-64" aria-label="Home">
                <application-logo />
            </a>

            <div class="bg-white px-6 py-8 shadow-md overflow-hidden rounded-lg sm:px-10">
                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit">
                    <div>
                        <jet-label for="email" value="Email" />
                        <jet-input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <jet-input-error :message="form.error('email')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <jet-label for="password" value="Password" />
                        <jet-input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full"
                            required
                            autocomplete="current-password"
                        />
                        <jet-input-error :message="form.error('password')" class="mt-2" />
                    </div>

                    <label class="flex items-center mt-4">
                        <input v-model="form.remember" type="checkbox" class="form-checkbox">
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>

                    <div class="flex items-center justify-end mt-6">
                        <a
                            v-if="canResetPassword"
                            href="/forgot-password"
                            class="underline text-sm text-gray-600 hover:text-gray-900"
                        >
                            Forgot your password?
                        </a>

                        <jet-button class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Login
                        </jet-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import ApplicationLogo from './../../Jetstream/ApplicationLogo'
    import JetButton from './../../Jetstream/Button'
    import JetInput from './../../Jetstream/Input'
    import JetInputError from './../../Jetstream/InputError'
    import JetLabel from './../../Jetstream/Label'

    export default {
        components: {
            ApplicationLogo,
            JetButton,
            JetInput,
            JetInputError,
            JetLabel,
        },

        props: {
            canResetPassword: Boolean,
            status: String,
        },

        data() {
            return {
                form: this.$inertia.form({
                    email: '',
                    password: '',
                    remember: false,
                }),
            }
        },

        methods: {
            submit() {
                this.form.post('/login', {
                    onFinish: () => this.form.password = '',
                })
            },
        },
    }
</script>
