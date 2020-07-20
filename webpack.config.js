'use strict';

let webpack = require('webpack');
let merge = require('webpack-merge');
let path = require('path');
let BrowserSync = require('./browser-sync');

const {VueLoaderPlugin} = require('vue-loader');

if (process.env.NODE_ENV === undefined) {
    process.env.NODE_ENV = 'development';
}

module.exports = function () {
    return merge([
        {
            mode: 'development',
            entry: {
                bundle: './inc/vue/main.js',
            },
            output: {
                path: path.join(__dirname, '/inc/js/'),
                filename: '[name].js',
            },
            module: {
                rules: [
                    {
                        test: /\.js$/,
                        exclude: /(node_modules|bower_components)/,
                        use: {
                            loader: 'babel-loader',
                            options: {
                                presets: ['env'],
                                plugins: ['transform-runtime'],
                            },
                        },
                    },
                    {
                        test: /\.vue$/,
                        loader: 'vue-loader',
                        options: {
                            loaders: {
                                'sass': [
                                    'vue-style-loader',
                                    'css-loader',
                                ],
                            },

                        },
                    },
                    {
                        test: /\.s[a|c]ss$/,
                        loader: 'style-loader!css-loader!sass-loader'
                    },
                ],
            },
            plugins: [
                new VueLoaderPlugin(),
            ],
            resolve: {
                alias: {
                    'vue$': 'vue/dist/vue.esm.js'
                },
                extensions: ['*', '.js', '.vue', '.json', '.scss']
            },
            optimization: {
                minimize: false,
            },
        },

        BrowserSync('./inc'),
    ]);
};