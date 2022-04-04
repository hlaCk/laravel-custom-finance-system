import {Quill, quillEditor} from "vue-quill-editor";

var Parchment = Quill.import('parchment');

class LineHeightAttributor extends Parchment.Attributor.Style {
    add(node, value) {
        if (value === '+1' || value === '-1') {
            let indent = this.value(node) || 0;
            let plusMode = value === '+1';
            value = (plusMode ? (indent + 1) : (indent - 1));
            if (value === 1) {
                value = plusMode ? 2 : 0;
            }
        }
        if (value === 0) {
            this.remove(node);
            return true;
        } else {
            return super.add(node, value);
        }
    }

    canAdd(node, value) {
        return super.canAdd(node, value) || super.canAdd(node, parseInt(value));
    }

    value(node) {
        return parseInt(super.value(node)) || undefined;  // Don't return NaN
    }
}

export const LineHeightStyle = new LineHeightAttributor('lineheight', 'line-height', {
    scope: Parchment.Scope.BLOCK,
    whitelist: [1, 2, 3, 4, 5, 6, 7, 8],
});
