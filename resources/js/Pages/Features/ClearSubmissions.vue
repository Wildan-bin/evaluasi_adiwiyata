<template>
    <div>
        <h1>Clear Submissions</h1>
        <button @click="clearSubmissions">Clear All Submissions</button>
    </div>
</template>

<script>
export default {
    methods: {
        async clearSubmissions() {
            if (confirm('Are you sure you want to clear all submissions?')) {
                try {
                    await this.$inertia.delete(this.route('submissions.clear'));
                    alert('Submissions cleared successfully.');
                } catch (error) {
                    alert('Failed to clear submissions. Please try again.');
                }
            }
        },
    },
};
</script>

<style scoped>
/* Add any necessary styles here */
</style>
