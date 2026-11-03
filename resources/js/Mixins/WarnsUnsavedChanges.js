export default {
    data() {
        return {
            unsavedBaseline: null,
            allowUnsavedVisit: false,
            originalInertiaVisit: null,
            guardedInertiaVisit: null,
        }
    },

    computed: {
        hasUnsavedChanges() {
            return this.unsavedBaseline !== null
                && this.unsavedBaseline !== this.unsavedSnapshot()
        },
    },

    mounted() {
        this.markChangesSaved()
        this.originalInertiaVisit = this.$inertia.visit
        this.guardedInertiaVisit = (...args) => {
            const options = args[1] || {}
            const method = (options.method || 'get').toLowerCase()

            if (method === 'get' && !this.allowUnsavedVisit && this.hasUnsavedChanges
                && !window.confirm('You have unsaved changes. Leave this page and discard them?')) {
                return Promise.resolve()
            }

            return this.originalInertiaVisit.apply(this.$inertia, args)
        }
        this.$inertia.visit = this.guardedInertiaVisit
        window.addEventListener('beforeunload', this.warnBeforeUnload)
    },

    beforeDestroy() {
        window.removeEventListener('beforeunload', this.warnBeforeUnload)

        if (this.$inertia.visit === this.guardedInertiaVisit) {
            this.$inertia.visit = this.originalInertiaVisit
        }
    },

    methods: {
        unsavedSnapshot() {
            return JSON.stringify(this.form.data())
        },
        markChangesSaved() {
            this.unsavedBaseline = this.unsavedSnapshot()
        },
        submitWithoutUnsavedWarning(submit) {
            this.allowUnsavedVisit = true

            return submit().then(result => {
                this.allowUnsavedVisit = false

                if (this.form.successful) {
                    this.markChangesSaved()
                }

                return result
            }).catch(error => {
                this.allowUnsavedVisit = false
                throw error
            })
        },
        warnBeforeUnload(event) {
            if (!this.hasUnsavedChanges) {
                return
            }

            event.preventDefault()
            event.returnValue = ''
        },
    },
}
