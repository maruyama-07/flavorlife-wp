(function() {
    tinymce.PluginManager.add('school_editor_banner', function(editor) {
        editor.addButton('school_editor_banner', {
            text: '茶色バナー',
            tooltip: '茶色バナー（中央・白文字）',
            onclick: function() {
                var html = editor.selection.getContent();
                if (html) {
                    editor.insertContent(
                        '<div class="c-school-editor-banner">' + html + '</div>'
                    );
                } else {
                    editor.insertContent(
                        '<div class="c-school-editor-banner"><p>テキストを入力してください</p></div>'
                    );
                }
            }
        });
    });
})();
