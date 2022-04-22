module.exports = {
    module: {
        rules: [
            {
                test: /\.s(c|a)ss$/,
                use: [
                    'vue-style-loader',
                    'css-loader',
                    {
                        loader: 'sass-loader',
                        // Requires sass-loader@^7.0.0
                        options: {
                            implementation: require('sass'),
                            indentedSyntax: true // optional
                        },
                        // Requires >= sass-loader@^8.0.0
                        // options: {
                        //     implementation: require('sass'),
                        //     sassOptions: {
                        //         indentedSyntax: true // optional
                        //     },
                        // },
                    },
                ],
            },
        ],
    },
    resolve: {
        alias: {
            // If using the runtime only build
            vue$: 'vue/dist/vue.runtime.esm.js' // 'vue/dist/vue.runtime.common.js' for webpack 1
            // Or if using full build of Vue (runtime + compiler)
            // vue$: 'vue/dist/vue.esm.js'      // 'vue/dist/vue.common.js' for webpack 1
        }
    }
}
