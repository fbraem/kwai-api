const webpack = require('webpack');
const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const { VueLoaderPlugin } = require('vue-loader');
const CleanObsoleteChunks = require('webpack-clean-obsolete-chunks');
// const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

function resolve(dir) {
  return path.join(__dirname, '..', dir);
}

module.exports = (env, argv) => {

  var config = {
    watch: argv.mode === 'development',
    mode: argv.mode,
    entry: {
      site: './src/site/main.js',
    },
    output: {
      path: path.join(__dirname, 'build'),
      filename: '[name].[chunkhash].js',
      chunkFilename: '[name].[chunkhash].js',
      publicPath: '/ui/build/'
    },
    optimization: {
      runtimeChunk: 'single',
      splitChunks: {
        cacheGroups: {
          default: false,
          vendors: false,
          vendor: {
            test: /[\\/]node_modules[\\/]/,
            chunks: 'all',
            name: 'vendor'
          },
          styles: {
            test: '\.css$/',
            chunks: 'all',
            name: 'styles'
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
        vue$: 'vue/dist/vue.common.js',
        '@': resolve('ui/src'),
        config: path.join(__dirname, 'src', 'site', 'config', argv.mode),
      }
    },
    plugins: [
      new CleanObsoleteChunks({
        verbose: true,
        deep: true
      }),
      new VueLoaderPlugin(),
      new MiniCssExtractPlugin({
        filename: '[name].[hash].css',
        chunkFilename: '[name].[hash].css',
        publicPath: './build',
      }),
      //  new BundleAnalyzerPlugin(),
      /*
      new webpack.optimize.CommonsChunkPlugin({
        names: ['vendor', 'vue'],
        minChunks: Infinity
      }),
      new webpack.optimize.CommonsChunkPlugin({
        name: 'manifest'
      }),
      */
      new webpack.ContextReplacementPlugin(
        /moment[\/\\]locale$/,
        /nl|en/
      ),
      new HtmlWebpackPlugin({
        filename: '../../index.html',
        template: 'src/index.template.html'
      }),
    ]
  };

  return config;
};
