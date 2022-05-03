<template>
    <div class="modal select-none fixed pin z-50 overflow-x-hidden overflow-y-auto toggledModal" @click.self="toggleModal" v-if="showModal">
        <div class="relative mx-auto flex justify-center z-20 py-view">
            <div>
                <div class="rounded-lg shadow-lg" style="width: 70vw;">
                    <div class="bg-30 px-6 py-3 flex overflow-hidden rounded-t-lg">
                        <div class="w-full">
                            <button @click="showModal=false" class="btn btn-primary btn-default">{{ __('Close') }}</button>
                        </div>
                    </div>
                    <div class="p-8 bg-white rounded-b-lg">
                        <vue-single-select
                            v-model="item"
                            ref="nqsInput"
                            input-id="nqsInput"
                            :options="options"
                            :classes="classes"
                            placeholder="type to search"
                            tabindex="1"
                            :max-results="100"
                            max-height="300px"
                            :get-option-description="getOptionDescription"
                            :get-option-value="getOptionValue"
                            :option-key="optionKey"
                            :option-label="optionLabel"
                            class="select-auto-complete w-full form-select"
                        />
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import VueSingleSelect from "vue-single-select";
import Modal from "./Modal";

export default {
    components: {
        VueSingleSelect,
        Modal
    },

    data: () => ({
        item: null,
        showModal: false,
        base_url: '/',
        push_url: '',
        options: [],
        classes: {
            wrapper: "single-select-wrapper",
            input: "search-input",
            icons: "icons",
            required: "required",
            activeClass: 'active',
            dropdown: 'dropdown'
        },
        optionKey: "value",
        optionLabel: "label",
        keyToggle: "KeyG",
        closeKeyToggle: "Escape",
    }),

    metaInfo() {
        return {
            title: 'NovaQuickSearch',
        }
    },
    mounted() {
        document.addEventListener("click", e => {
            let classList = Array.from(classList = e.target.classList)
            if (this.isModalShown() && classList.includes('toggledModal')) {
                this.toggleModal();
            }

            return false;
        })

        this.getOptions()
            .then(x => {
                    let keyLC = this.keyToggle
                    keyLC && document.addEventListener("keydown", e => {
                        let classList = Array.from(classList = e.target.classList)

                        if (e.ctrlKey && e.code.toLowerCase() === keyLC.toLowerCase()) {
                            this.toggleModal();
                            e.preventDefault();
                        }

                        return false;
                    })

                let closeKeyLC = this.closeKeyToggle
                    closeKeyLC && document.addEventListener("keydown", (e) => {
                        if (e.code.toLowerCase() === closeKeyLC.toLowerCase() && this.isModalShown()) {
                            setTimeout(() =>this.toggleModal(), 10)
                        }

                        return false;
                    })
                }
            )
    },

    methods: {
        handleClose() {
            this.$emit('close')
            this.showModal = false
        },
        toggleModal() {
            this.showModal = !this.showModal;
        },
        isModalShown() {
            return this.showModal === true;
        },
        getOptionDescription(option) {
            if (this.optionKey && this.optionLabel) {
                return option[this.optionLabel];
            }
            if (this.optionLabel) {
                return option[this.optionLabel];
            }
            if (this.optionKey) {
                return option[this.optionKey];
            }

            return option;
        },
        getOptionValue(option) {
            let value = null

            if (!value && this.optionKey) {
                value = option[this.optionKey];
            }

            if (!value && this.optionLabel) {
                value = option[this.optionLabel];
            }

            let url = (""+value).slice(0,1) === '/' ? value : this.base_url + (this.base_url.slice(-1) === '/' && "" || "/") + value
            setTimeout(() => this.isModalShown() && this.toggleModal(), 10);

            location.href = url;

            return value || option;
        },
        getOptions() {
            return Nova.request()
                .get(`/nova-api/nova-quick-search-options`, {
                    params: {},
                })
                .then(response => response.data || {})
                .then(response => response.data || {})
                .then(response => {
                    this.options = response.options || [];
                    this.base_url = response.base_url || '/';
                    this.push_url = response.push_url || '';
                })
        },
    },

    watch: {
        showModal(current, previous) {
            if (current) {
                setTimeout(() => {
                    try {
                        // console.log(document.querySelector('div.fixed.pin:not(.toggledModal)'))
                        // console.log()
                        // if (!document.getElementById('nqsInput').closest('div.fixed.pin:not(.toggledModal)')) {
                        //     document.querySelector('div.fixed.pin:not(.toggledModal)').appendChild()
                        // }
                        document.getElementById('nqsInput').focus()
                        // document.querySelector('div.fixed.pin:not(.toggledModal)').classList.add('hidden')
                    } catch (e) {
                    }
                }, 20);
            } else {
                setTimeout(() => {
                    try {
                        // document.querySelector('div.fixed.pin.hidden:not(.toggledModal)').classList.remove('hidden')
                    } catch (e) {
                    }
                }, 20);
            }
        },
    },

}
</script>

<style>
/* Scoped Styles */
</style>
