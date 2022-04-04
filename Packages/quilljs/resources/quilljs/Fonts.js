import {Quill, quillEditor} from "vue-quill-editor";

// font:
var FontAttributor = Quill.import('attributors/class/font');
FontAttributor.whitelist = null;//['impact', 'courier', 'comic', 'arial', 'tahoma','serif'];

export {FontAttributor};
