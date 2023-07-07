import settings from "src/settings";
const { defineConfig } = require('@vue/cli-service')

module.exports = defineConfig({
    devServer: {
        host: settings.FRONTEND.Host,
        port: settings.FRONTEND.Port,
    },

  transpileDependencies: true,
  pluginOptions: {
    vuetify: {
			// https://github.com/vuetifyjs/vuetify-loader/tree/next/packages/vuetify-loader
		}
  }
})
