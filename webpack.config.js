const path = require( "path" );
const isDev = process.env.NODE_ENV === "development";

const HTMLWebpackLoader = require( "html-webpack-plugin" );
const { CleanWebpackPlugin } = require( "clean-webpack-plugin" );
const CopyWebpackPlugin = require( "copy-webpack-plugin" );

const filename = ( ext ) => isDev ? `[name].${ ext }` : `[name].[contenthash].${ ext }`;
const dirname = () => isDev ? "DeveloperBuild" : "ProductionBuild";

module.exports = {
    mode: "development",
    context: path.resolve( __dirname, "source" ),
    entry: {
        // name: "./name.js"
        main: "./_Frontend/Index/index_bundle.js"
    },
    output: {
        filename: `./${ filename( "js" ) }`,
        path: path.resolve( __dirname, `dist/${ dirname() }` )
    },
    devServer: {
        contentBase: path.resolve( __dirname, "source/_Frontend/Index" ),
        open: true,
        compress: true,
        hot: true,
        port: 3000,
    },
    plugins: [
        new HTMLWebpackLoader( {
            template: path.resolve( __dirname, "source/_Frontend/Index/index.html" ),
            filename: "main.php",
            minify: {
                collapseWhitespace: !isDev
            },
        } ),
        new CleanWebpackPlugin(),
        new CopyWebpackPlugin( {
            patterns: [
                /*
                Копируешь вот это, вставляешь ниже основного обьекта ( main.php ),
                вместо pageName имя страницы ( должно совпадать с html'кой )

                {
                    from: path.resolve( __dirname, "source/_Backend/pages/pageName.php" ),
                    to: path.resolve( __dirname, `dist/${ dirname() }/php`)
                },
                 */
                {
                    from: path.resolve( __dirname, "source/_Backend/pages/index.php" ),
                    to: path.resolve( __dirname, `dist/${ dirname() }/php` )
                },
            ],
            options: {
                concurrency: 100,
            },
        } ),
    ],
    module: {
        rules: [
            {
                test: /\.html$/,
                loader: "html-loader",
            },
            {
                test: /\.css$/i,
                use: [
                    "style-loader",
                    "css-loader",
                ],
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