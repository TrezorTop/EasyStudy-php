const path = require("path");
const isDev = process.env.NODE_ENV === "development";

const filename = (ext) => isDev ? `[name].${ext}` : `[name].[contenthash].${ext}`;
const dirname = () => isDev ? "DeveloperBuild" : "ProductionBuild";

module.exports = {
    mode: "development",
    context: path.resolve(__dirname, "source"),
    entry: {
        main: "./main.js"
    },
    output: {
        filename: `./${filename("js")}`,
        path: path.resolve(__dirname, `dist/${dirname()}`)
    },
    devServer: {
        contentBase: path.resolve(__dirname, 'dist/DeveloperBuild')
    },
};