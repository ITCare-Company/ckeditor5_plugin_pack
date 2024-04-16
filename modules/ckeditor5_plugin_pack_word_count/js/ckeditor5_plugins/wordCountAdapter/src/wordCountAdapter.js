/*
 * Copyright (c) 2003-2023, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

class WordCountAdapter {
  constructor( editor ) {
    this.editor = editor;
    this.elementId = this.editor.sourceElement.dataset.drupalSelector;
    this.isRevHistoryEnabled = false;
    if (this.elementId.includes("revision-history")) {
      this.isRevHistoryEnabled = true;
      return;
    }
    this.wordCountId = this.elementId + '-ck-word-count';
    this.wordCountWrapper = document.getElementById( this.wordCountId );
  }

  init() {
    if (this.isRevHistoryEnabled) {
      return;
    }
    const wordCountPlugin = this.editor.plugins.get( 'WordCount' );
    for (var i = 0; i < wordCountPlugin.wordCountContainer.children.length; i++) {
      wordCountPlugin.wordCountContainer.children[i].innerHTML = this.wrapNumber(wordCountPlugin.wordCountContainer.children[i].innerHTML)
    }
    this.wordCountWrapper.appendChild(wordCountPlugin.wordCountContainer);
  }

  afterInit() {
    if (this.isRevHistoryEnabled) {
      return;
    }
    const wordCountPlugin = this.editor.plugins.get( 'WordCount' );
    const wordCount = this.wordCountWrapper.querySelector('.ck-word-count__words span');
    const characterCount = this.wordCountWrapper.querySelector('.ck-word-count__characters span');
    wordCountPlugin.on( 'update', ( evt, stats ) => {
      if (wordCount) {
        wordCount.innerText = stats.words;
      }
      if (characterCount) {
        characterCount.innerText = stats.characters;
      }
    });

    if (this.editor.plugins.has('SourceEditing')) {
      const sourceEditing = this.editor.plugins.get('SourceEditing')
      sourceEditing.on('change:isSourceEditingMode', (eventInfo, name, value) => {
        if (value === true) {
          this.wordCountWrapper.classList.add('ck-word-count-hide-element');
        } else {
          this.wordCountWrapper.classList.remove('ck-word-count-hide-element');
        }
      })
    }
  }

  wrapNumber(str) {
    const regex = /(\d+)/ig;
    return str.replace(regex, '<span>$1</span>')
  }

}

export default WordCountAdapter;
