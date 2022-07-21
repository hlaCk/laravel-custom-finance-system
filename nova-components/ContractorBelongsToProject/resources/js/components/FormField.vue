<template>
    <loading-view :loading="loading">
        <default-field :field="field" :errors="errors">
            <template slot="field">
                <select-control
                    :id="field.attribute"
                    v-model="value"
                    class="w-full form-control form-select"
                    :class="[{'hidden': !entry_category_has_constructor},errorClasses]"
                    :options="field.options"
                    :selected="value"
                    :disabled="isReadonly">

                    <option value="" disabled>
                        {{ __('-') }}
                    </option>
                </select-control>
            </template>
        </default-field>

<!--        <form-select-field
            id="contractor_select_field"
            :class="{'hidden': !entry_category_has_constructor}"
            :field="field"
            :value="value"
            @change="hanlder"
            @input="hanlder"
            @set-value="hanlder"
        ></form-select-field>-->
    </loading-view>
</template>

<script>
import {FormField, HandlesValidationErrors} from "laravel-nova";
import init from './mixins/init';


export default {
    mixins: [
        FormField, HandlesValidationErrors,
        ...init,
    ],
    props: ["resourceName", "resourceId", "field"],

    methods: {
        hanlder(v) {
            console.error('Form field: ', v)
        },
        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = this.field.value || "";
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute, this.value || "");
            formData.append(this.field.attribute+'-addon', this.field.value || "*");
        },

        /**
         * Update the field's internal value.
         */
        handleChange(value) {
            this.value = value;
        },

    },
}
</script>
