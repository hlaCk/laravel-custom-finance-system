<template>
    <default-field
        :field="field"
        :errors="errors"
        :full-width-content="field.width"
    >
        <template slot="field">
            <quill-editor
                :style="css"
                v-model="value"
                ref="myQuillEditor"
                :options="editorOption"
                @blur="onEditorBlur($event)"
                @focus="onEditorFocus($event)"
                @ready="onEditorReady($event)"
            ></quill-editor>
        </template>
    </default-field>
</template>

<script>
import {FormField, HandlesValidationErrors} from "laravel-nova";
import {Quill, quillEditor} from "vue-quill-editor";
import BlotFormatter from "quill-blot-formatter";
import {ImageExtend, QuillWatch} from "quill-image-extend-module";
import {VideoBlot} from "../../quilljs/VideoBlot";
import {ImageBlot} from "../../quilljs/ImageBlot";
import Tooltip from "quill/ui/tooltip";
import {CustomImageSpec} from "../../quilljs/CustomImageSpec";
import {LineHeightStyle} from "../../quilljs/LineHeightStyle";
import {FontAttributor} from "../../quilljs/Fonts";
import _icons from "../icons";
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

// import Link from './formats/link';
Quill.register({
    "modules/ImageExtend": ImageExtend,
    "modules/blotFormatter": BlotFormatter,
    "ui/tooltip": Tooltip,
    "formats/video": VideoBlot,
    "formats/image": ImageBlot,

    "attributors/class/font": FontAttributor,
    "formats/lineheight": LineHeightStyle,
});

// const $CP = {
//     $quill: null,
//     quill(q) {
//         return q || this.$quill;
//     },
//     getToolbarContainer(q) {
//         return this.quill(q).theme.modules.toolbar.container;
//     },
//     getColorPickerContainer(q) {
//         return this.getToolbarContainer(q).querySelector('.ql-color.ql-picker.ql-color-picker');
//     },
//     getOpenedColorPickerContainer(q) {
//         return this.getToolbarContainer(q).querySelector('.ql-color.ql-picker.ql-color-picker.ql-expanded');
//     },
//     getColorPickerInput(q) {
//         return this.getToolbarContainer(q).querySelector('#color-picker')
//     },
//     getColorPickerLabel(q) {
//         return this.getToolbarContainer(q).querySelector('span.ql-picker-item[data-value="color-picker"]')
//     },
// };

const Delta = Quill.import('delta')
export default {
    mixins: [FormField, HandlesValidationErrors],
    components: {
        quillEditor,
    },
    props: ["resourceName", "resourceId", "field"],
    data() {
        return {
            persisted: [],
            toolbarTips: this.field.tooltip,
            editorOption: {
                placeholder: this.field.placeholder,
                modules: {
                    blotFormatter: {
                        specs: [CustomImageSpec],
                    },
                    ImageExtend: {
                        loading: true,
                        size: this.field.maxFileSize ? this.field.maxFileSize : 2,
                        name: "attachment",
                        action: `/nova-vendor/quilljs/${this.resourceName}/upload/${this.field.attribute.split(this.field.split)[0]}`,
                        response: (res) => {
                            return res.url;
                        },
                        headers: (xhr) => {
                            xhr.setRequestHeader(
                                "X-CSRF-TOKEN",
                                document.head.querySelector('meta[name="csrf-token"]').content
                            );
                        },
                        sizeError: () => {
                            this.$toasted.show(
                                `Image size exceeds ${
                                    this.field.maxFileSize ? this.field.maxFileSize : 2
                                }MB`,
                                {type: "error"}
                            );
                        },
                        change: (xhr, formData) => {
                            const draftId = this._uuid()
                            formData.append("draftId", draftId)
                            this.persisted.push(draftId)

                        },
                    },
                    toolbar: {
                        container: this.field.options,
                        handlers: {
                            image() {
                                QuillWatch.emit(this.quill.id);
                                // setTimeout(()=>{
                                //     let imgs = Array.from(this.quill.container.querySelectorAll('img')).filter(x=>!x.classList.length)
                                //     imgs.forEach(x => x.classList.add('ql-image'))
                                // }, 100);
                            },
                            video(value) {
                                this.quill.theme.tooltip.edit("video");
                            },
                        },
                    },
                },
            },
        };
    },
    methods: {
        _uuid() {
            var d = Date.now();
            if (
                typeof performance !== "undefined" &&
                typeof performance.now === "function"
            ) {
                d += performance.now(); //use high-precision timer if available
            }
            return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, function (
                c
            ) {
                var r = (d + Math.random() * 16) % 16 | 0;
                d = Math.floor(d / 16);
                return (c === "x" ? r : (r & 0x3) | 0x8).toString(16);
            });
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
            if (!formData.has('persisted')) {
                formData.append('persisted', JSON.stringify(this.persisted));
            }
        },

        /**
         * Update the field's internal value.
         */
        handleChange(value) {
            this.value = value;
        },

        onEditorBlur(quill) {
            // console.log("editor blur!", quill);
        },
        onEditorFocus(quill) {
            // console.log("editor focus!", quill);
        },
        onEditorReady(quill) {
            // console.log("editor ready!", quill);
        },
        onEditorChange({quill, html, text}) {
            this.content = html;
        },
        autotip() {
            if (this.toolbarTips) {
                for (let item of this.toolbarTips) {
                    let tip = document.querySelector(".quill-editor " + item.Choice);
                    if (!tip) continue;
                    tip.setAttribute("title", item.title);
                }
            }
        },

        getQuill() {
            return this.quill || this.editor;
        },
        getToolbarContainer() {
            return this.getQuill().theme.modules.toolbar.container;
        },
        getColorPickerContainer() {
            return this.getToolbarContainer().querySelector('.ql-color.ql-picker.ql-color-picker');
        },
        getColorPickerInput() {
            return this.getToolbarContainer().querySelector('.ql-color #color-picker')
        },
        getColorPickerLabel() {
            return this.getToolbarContainer().querySelector('.ql-color span.ql-picker-item[data-value="color-picker"]')
        },
        getBGColorPickerContainer() {
            return this.getToolbarContainer().querySelector('.ql-background.ql-picker.ql-color-picker');
        },
        getBGColorPickerInput() {
            return this.getToolbarContainer().querySelector('.ql-background #color-picker')
        },
        getBGColorPickerLabel() {
            return this.getToolbarContainer().querySelector('.ql-background span.ql-picker-item[data-value="color-picker"]')
        },
    },
    computed: {
        editor() {
            return this.$refs.myQuillEditor.quill;
        },
        css() {
            return {
                height: this.field.height + 41 + "px",
                "padding-bottom": this.field.paddingBottom + 40 + "px",
            };
        },
    },
    mounted() {
        this.autotip()
        Object.keys(_icons).forEach(selector => {
            let $src = _icons[selector];
            document.querySelectorAll(selector)
                .forEach(x => {
                    if (!x.innerHTML) {
                        x.innerHTML = $src;
                    }
                });
        });

        // document.querySelectorAll('.ql-font .ql-picker-options .ql-picker-item')
        //     .forEach(x => {
        //         if (!x.innerHTML) {
        //             x.innerHTML = x.getAttribute('data-value');
        //         }
        //     });
        // console.log(this.editor.theme.modules.toolbar.container.querySelectorAll('button'))
        // console.log(this.editor.theme.modules.toolbar.container.querySelector('.ql-picker-item[data-value="color-picker"]'))
        // console.warn(this.field.options)
        //     this.buildButtons([].slice.call(this.editor.theme.modules.toolbar.container.querySelectorAll('button')), Icons);

        let color_picker = this.getColorPickerInput();
        if (!color_picker) {
            color_picker = document.createElement('input');
            color_picker.id = 'color-picker';
            color_picker.type = 'color';
            // color_picker.style.display = 'none';
            color_picker.style.display = '';
            color_picker.classList.add('ql-picker-item');
            color_picker.setAttribute('data-value', 'color-picker');
            color_picker.value = '';
            this.getColorPickerLabel().replaceWith(color_picker);
            color_picker.addEventListener('change', () => {
                this.getColorPickerContainer().classList.remove('ql-expanded')
                this.getQuill().format('color', color_picker.value);
            }, false);
        }

        let bg_picker = this.getBGColorPickerInput();
        if (!bg_picker) {
            bg_picker = document.createElement('input');
            bg_picker.id = 'color-picker';
            bg_picker.type = 'color';
            // bg_picker.style.display = 'none';
            bg_picker.style.display = '';
            bg_picker.classList.add('ql-picker-item');
            bg_picker.setAttribute('data-value', 'color-picker');
            bg_picker.value = '';
            this.getBGColorPickerLabel().replaceWith(bg_picker);
            bg_picker.addEventListener('change', () => {
                this.getBGColorPickerContainer().classList.remove('ql-expanded')
                this.getQuill().format('background', bg_picker.value);
            }, false);
        }
    },
};
</script>

<style>
.ql-editor p {
    margin-top: 18px;
    font-size: 18px;
}

.ql-video {
    width: 800px;
    height: 450px;
}
</style>
