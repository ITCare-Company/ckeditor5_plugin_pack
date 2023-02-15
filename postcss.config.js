const { styles } = require('@ckeditor/ckeditor5-dev-utils');

const options = styles.getPostCssConfig({
  themeImporter: {
    themePath: require.resolve('@ckeditor/ckeditor5-theme-lark'),
  },
});
options.plugins = options.plugins.filter(
  (plugin) => plugin.postcssPlugin !== 'postcss-ckeditor5-theme-logger',
);
options.plugins.push(
  require('modify-selectors')({
    enable: true,
    modify: [
      {
        match: '.ck-content',
        with(selector) {
          return selector.replace('.ck-content', '').trim();
        },
      },
    ],
  }),
);

module.exports = options;
