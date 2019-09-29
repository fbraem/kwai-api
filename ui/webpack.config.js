const webpack = require('webpack');
const path = require('path');
const HtmlPlugin = require('html-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');
const { VueLoaderPlugin } = require('vue-loader');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

function resolve(dir) {
  return path.join(__dirname, dir);
}

module.exports = (env, argv) => {

  function isDev() {
    return argv.mode === 'development';
  }

  var config = {
    watch: isDev(),
    mode: argv.mode,
    entry: {
      site: resolve('src/site/main.js'),
    },
    output: {
      path: path.join(__dirname, 'build'),
      filename: '[name].[chunkhash].js',
      chunkFilename: '[name].[chunkhash].js',
      publicPath: '/ui/build/'
    },
    optimization: {
      runtimeChunk: 'single',
      minimizer: [
        new OptimizeCSSAssetsPlugin({
          cssProcessorPluginOptions: {
            preset: [ 'default', { discardComments: { removeAll: true } } ]
          }
        }),
        new UglifyJSPlugin({
          cache: true,
          parallel: true,
          sourceMap: isDev()
        }),
      ],
      splitChunks: {
        chunks: 'all',
        maxInitialRequests: Infinity,
        minSize: 0,
        cacheGroups: {
          vendor: {
            test: /[\\/]node_modules[\\/]/,
            name(module) {
              const packageName = module.context.match(/[\\/]node_modules[\\/](.*?)([\\/]|$)/)[1];
              return `npm.${packageName.replace('@', '')}`;
            },
          },
          styles: {
            test: /\.css$/,
            name: 'styles',
            chunks: 'all',
            enforce: true
          },
          common: {
            name: 'common',
            minChunks: 2,
            chunks: 'async',
            priority: 10,
            reuseExistingChunk: true,
            enforce: true
          }
        }
      }
    },
    module: {
      rules: [
        {
          test: /\.vue$/,
          loader: 'vue-loader',
        },
        {
          test: /\.js$/,
          exclude: /node_modules/,
          loader: 'babel-loader'
        },
        {
          test: /\.scss$/,
          use: [
            'vue-style-loader',
            MiniCssExtractPlugin.loader,
            'css-loader',
            'sass-loader',
          ]
        },
        {
          test: /\.css$/,
          use: [
            MiniCssExtractPlugin.loader,
            'css-loader',
          ]
        },
        {
          test: /\.(woff(2)?|eot|svg|otf|ttf)(\?v=[0-9]\.[0-9]\.[0-9])?$/,
          loader: 'file-loader',
          options: {
            name: '[name]_[hash].[ext]',
            outputPath: 'fonts/'
          }
        },
        {
          test: /\.(png|jpe?g|gif)$/i,
          loaders: [
            'file-loader?name=assets/[name]_[hash].[ext]&publicPath=ui/build/',
            {
              loader: 'image-webpack-loader',
              query: {
                bypassOnDebug: true,
                gifsicle: {
                  interlaced: false
                },
                optipng: {
                  optimizationLevel: 7
                },
                mozjpeg: {
                  progressive: true,
                  quality: 65
                }
              }
            },
          ]
        },
      ]
    },
    resolve: {
      extensions: [ '*', '.js', '.vue', '.json' ],
      alias: {
        vue$: isDev() ? 'vue/dist/vue.common.js' : 'vue/dist/vue.common.min.js',
        '@': resolve('src'),
        config: path.join(__dirname, 'src', 'site', 'config', argv.mode),
      },
      mainFiles: [ 'index' ],
    },
    plugins: [
      new CleanWebpackPlugin(),
      new VueLoaderPlugin(),
      new MiniCssExtractPlugin({
        filename: '[name].[contenthash].css',
        /* chunkFilename: '[name].[hash].css', */
        publicPath: './build',
      }),
      new webpack.ContextReplacementPlugin(
        /moment[\/\\]locale$/,
        /nl|en/
      ),
      new HtmlPlugin({
        filename: '../../index.html',
        template: 'src/index.template.html',
        chunksSortMode: 'dependency'
      }),
    ]
  };

  return config;
};
