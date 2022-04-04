import {quillEditor, Quill} from "vue-quill-editor";

var ListItem = Quill.import('formats/list/item');

/**
 * @deprecated not used
 */
export class CustomListItem extends ListItem {
    formatAt(index, length, name, value) {
        console.warn(arguments);
        if (name === 'list') {
            // Allow changing or removing list format
            super.formatAt(name, value);
        }
        // Otherwise ignore
    }
}
