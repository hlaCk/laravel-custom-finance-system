import { ClassAttributor, Scope, StyleAttributor } from 'parchment';

const config = {
    scope: 7,
    whitelist: ['small', 'large', 'huge'],
};

const LineHeightClass = new ClassAttributor('font', 'ql-font', config);

class FontStyleAttributor extends StyleAttributor {
    value(node) {
        console.warn(node);
        return super.value(node).replace(/["']/g, '');
    }
}

const LineHeightStyle = new FontStyleAttributor('line-height', 'line-height', config);

export { LineHeightStyle, LineHeightClass };

/*

import {  ClassAttributor, Scope, StyleAttributor } from 'parchment';
// export declare enum Scope {
//     TYPE = 3,
//         LEVEL = 12,
//         ATTRIBUTE = 13,
//         BLOT = 14,
//         INLINE = 7,
//         BLOCK = 11,
//         BLOCK_BLOT = 10,
//         INLINE_BLOT = 6,
//         BLOCK_ATTRIBUTE = 9,
//         INLINE_ATTRIBUTE = 5,
//         ANY = 15,
// }

const LineHeightClass = new ClassAttributor('line-height', 'ql-line-height', {
    scope: 11,
    whitelist: ['small', 'large', 'huge'],
});
const LineHeightStyle = new StyleAttributor('line-height', 'line-height', {
    scope: 11,
    whitelist: ['10px', '18px', '32px'],
});

export { LineHeightClass, LineHeightStyle };
*/
