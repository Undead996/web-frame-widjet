
module.exports = {
    publicPath: process.env.NODE_ENV === 'production' ? "./SPA" : "./",
    // publicPath: "./",
    outputDir: "../SPA",
    assetsDir: "./",
    devServer: {
      proxy: 'http://192.168.100.24:80'
    }

  }