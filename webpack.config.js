const path = require("path");
const isDev = process.env.NODE_ENV === "development";

const HTMLWebpackLoader = require("html-webpack-plugin");
const {CleanWebpackPlugin} = require("clean-webpack-plugin");

const filename = (ext) => isDev ? `[name].${ext}` : `[name].[contenthash].${ext}`;
const dirname = () => isDev ? "DeveloperBuild" : "ProductionBuild";

module.exports = {
    mode: "development",
    context: path.resolve(__dirname, "source"),
    entry: {
        // name: "./name.js"
        main: "./_Frontend/Main/main_bundle.js"
    },
    output: {
        filename: `./${filename("js")}`,
        path: path.resolve(__dirname, `dist/${dirname()}`)
    },
    devServer: {
        contentBase: path.resolve(__dirname, "source/_Frontend/Main"),
        open: true,
        compress: true,
        hot: true,
        port: 3000,
    },
    plugins: [
        new HTMLWebpackLoader({
            template: path.resolve(__dirname, "source/_Frontend/Main/main.html"),
            filename: "main.html",
            minify: {
                collapseWhitespace: !isDev
            },
        }),
        new CleanWebpackPlugin(),
    ],
    module: {
        rules: [
            {
                test: /\.html$/,
                loader: "html-loader",
            },
            {
                test: /\.s[ac]ss$/i,
                use: [
                    "style-loader",
                    "css-loader",
                    "sass-loader",
                ],
            },
        ],
    },
};