<template>
    <div>
        <h1>Preview File</h1>
        <iframe :src="file.url" width="100%" height="600px"></iframe>
        <button @click="close">Close</button>
    </div>
</template>

<script>
export default {
    props: {
        file: Object,
    },
    methods: {
        close() {
            this.$inertia.visit(this.route('dashboard')); // Redirect to dashboard or previous page
        },
    },
};
</script>

<style scoped>
/* Add any necessary styles here */
</style>
