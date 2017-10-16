const webpack = require('webpack');
const path = require('path');

function resolve(dir) {
    return path.join(__dirname, '..', dir);
}

var config = {
    entry : {
        "auth" : "./src/apps/auth/main.js",
        "install" : "./src/apps/install/main.js",
        "users" : "./src/apps/users/main.js",
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
                loader: 'babel-loader',
                query: {
                    presets: ['es2015'],
                    plugins: ["transform-object-rest-spread"]
                }
            },
            { test: /\.vue$/, loader: "vue-loader" },
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
            '@' : resolve('ui/src')
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
        // Workaround to exclude moment.js locales
        new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/)
    ]
};

module.exports = config;
