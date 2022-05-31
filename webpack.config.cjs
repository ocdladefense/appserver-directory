const webpack = require('webpack');
const path = require('path');

module.exports = {
    mode: 'development',
    context: __dirname,
    entry: ['./src/js/directory.js', './src/js/views.js'],
    output: {
      path: path.resolve(__dirname, 'dist'),
      filename: 'main.js'
    },
    devServer: {  
      static: './dist',
      compress: true,  
      port: 8080
    }, 
    devtool: 'source-map', 
    module: {
      rules: [	
        { 
          test: /\.js$/i,
          exclude: /(node_modules)/,
          use: { 
            loader: 'babel-loader', 
            options: {
            presets: ['@babel/preset-env']
          }}
        }, 
        { 
          test: /\.css$/i, 
          use: [ 'style-loader', 'css-loader' ]		
        },
        {  
          test: /\.(svg|eot|ttf|woff|woff2)$/i,  
          use: {
            loader: 'url-loader',  
            options: {    limit: 10000,    name: 'fonts/[name].[ext]'  }
          }
        },
        {
          test: /\.(png|jpg|gif)$/,
          use: [
            {
              loader: 'url-loader', options: { limit: 10000, name: '../images/[name].[ext]', outputPath: 'images'}
            },
          'img-loader'
          ],
        },
      ],
    },
    plugins: [  
        new webpack.HotModuleReplacementPlugin(),
        new webpack.ProvidePlugin(
            { 
              jQuery: 'jquery', 
              $: 'jquery', 
              jquery: 'jquery' 
            }), 
    ],
}
  