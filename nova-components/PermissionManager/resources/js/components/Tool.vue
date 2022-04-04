<template>
    <div>
        <heading class="mb-6">{{ role.name }}</heading>

        <card v-for="(permissionsGroup, i) in permissionsGroups" :key="permissionsGroup + '-group'" class=" mb-3" style="padding: 30px;">
            <h2 class=" mb-4">{{ __(permissionsGroup[0].local_group) }}</h2>
            <p class=" mb-3" v-for="permission in permissionsGroup" :key="permission.id">
                <label :for="`permission-${permission.id}`">
                    <input class="mr-4" :id="`permission-${permission.id}`" type="checkbox" v-model="selected" :value="permission.id"> {{ permission.local_name }}
                </label>
            </p>
        </card>

        <button class="btn btn-default btn-primary" @click="onSave">حفظ</button>
    </div>
</template>

<script>
export default {
    data: () => ({
        role: {},
        permissionsGroups: [],
        selected: [],
    }),
    mounted() {
        this.getData()
    },
    methods: {
        shiftHistory() {
            let hasHistory = window.history.length > 2;
            return (
                hasHistory
                    ? this.$router.go(-1)
                    : this.$router.push('/')
            );
        },
        getData() {
            let id = this.$route.params.id;
            Nova.request().get('/nova-vendor/permission-manager/roles/' + id + '/permissions').then(data => {
                this.role = data.data.role;
                this.permissionsGroups = data.data.permissions;
                this.setDefaultSelected();
            })
        },
        setDefaultSelected() {
            let arr = this.role.permissions.map(item => item.id).flat(1);
            console.log(arr);
            this.selected = arr;
        },
        onSave() {
            let id = this.$route.params.id;
            Nova.request().post('/nova-vendor/permission-manager/roles/' + id + '/permissions', {permissions: this.selected}).then(data => {
                this.$toasted.show('تم الحفظ بنجاح', {
                    type: 'success',
                    onComplete: () => this.$router.push('/resources/roles'),
                    duration: 500
                });
                return data;
            })
            // .then(x => (location.href += '/../../resources/roles'))
        }
    }
}
</script>

<style scoped>
label {
    display: flex;
    align-items: center;
    font-size: 19px;
}
</style>
