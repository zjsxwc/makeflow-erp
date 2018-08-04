/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

    config.language = 'zh-cn';
    config.extraPlugins = "okimage";
    config.toolbar = [
        { name: 'document', items: [ 'Source','-', 'Undo', 'Redo','-','PasteText','PasteFromWord','-','FontSize','RemoveFormat','Bold', 'Italic', 'Underline'] },
        { name: 'links', items: [ 'Link', 'Unlink' ] },
        { name: 'insert', items: [ 'okimage' ] }

    ];

    config.imageUploadUrl = '/user/upload-images';
};
