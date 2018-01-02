const webpack = require('webpack');
const path = require('path');

function resolve(dir) {
    return path.join(__dirname, '..', dir);
}

var environment = process.argv.indexOf('-p') !== -1 ? 'production' : 'development';

var config = {
    entry : {
        "vendor" : [
            "lockr", "moment", "axios", "lodash", "urijs", "marked"
        ],
        "vue" : [
            "vuex", "vue", "vue-router", "vuelidate", "vue-kindergarten", "vue-extend-layout", "vuetify", "vue-i18n"
        ],
        "auth" : "./src/apps/auth/main.js",
        "install" : "./src/apps/install/main.js",
        "users" : "./src/apps/users/main.js",
        "categories" : "./src/apps/categories/main.js",
        "news" : "./src/apps/news/main.js",
        "site" : "./src/site/main.js"
    },
    output : {
        filename : "./build/[name].js"
    },
    module : {
        loaders : [
            { test: /\.js$/,
                exclude: /node_modules/,
                loader: 'babel-loader?presets[]=es2015'
            },
            { test: /\.vue$/,
                loader: "vue-loader"
            },
            { test: /\.css$/, loader: "style-loader!css-loader" },
            { test: /\.(woff|woff2|eot|ttf|svg)(\?v=[0-9]\.[0-9]\.[0-9])?$/, loader: 'url-loader' },
            { test: /\.(png|jpe?g|gif|svg)$/i,
                loaders: [
                    "file-loader?name=build/assets/[name]_[hash].[ext]&publicPath=ui/",
                    {
                        loader : 'image-webpack-loader',
                        query : {
                            bypassOnDebug : true,
                            gifsicle: {
                                interlaced : false
                            },
                            optipng : {
                                optimizationLevel : 7
                            }
                        }
                    }
                ]
            }
        ]
    },
    resolve : {
        alias : {
            'vue$' : 'vue/dist/vue.common.js',
            '@' : resolve('ui/src'),
            'config' : path.join(__dirname, 'src', 'site', 'config', environment)
        }
    },
    plugins : [
/*
        new webpack.ProvidePlugin({
            $ : "jquery",
            jQuery : "jquery",
            "window.$" : "$",
            "window.jQuery" : "jquery"
        }),
*/
        new webpack.optimize.CommonsChunkPlugin({
            names : ['vendor', 'vue'],
            filename : "build/[name].js"
        })
    ]
};

module.exports = config;
