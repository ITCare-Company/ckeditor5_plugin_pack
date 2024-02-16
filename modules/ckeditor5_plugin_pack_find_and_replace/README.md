# CKEditor Find/Replace

This plugin adds Find and Replace functionality for CKEditor in Drupal 10:

- Quickly search the text as well as replace words inside it.
- Common options available for matching:
  - case
  - whole word
  - cyclic

## MAINTAINING DLL INTEGRATION

The core maintainers of CKEditor 5 maintain a find/replace plugin that is not
included in Drupal core.  The necessary files are available in the associated
npm package.

1. Insure node version >= 16.0
2. Check the [current CKEditor](https://git.drupalcode.org/project/drupal/-/blob/10.0.x/core/package.json) package
   versions using the latest tag value for Drupal core. As of this release that
   version is 35.4.0.
3. Install the npm package: `npm i @ckeditor/ckeditor5-find-and-replace@~35.4.0`
4. Overwrite the existing directory in the `js` directory with
   `node_modules/@ckeditor/ckeditor5-find-and-replace/build`
5. Copy the CSS files into the `css` directory from
   `node_modules/@ckeditor/ckeditor5-find-and-replace/theme`

## Related Blog Posts
[Everything you need to know about CKEditor 5 integration for Drupal 9](https://www.qed42.com/insights/coe/drupal/everything-you-need-know-about-ckeditor-5-integration-drupal-9)
