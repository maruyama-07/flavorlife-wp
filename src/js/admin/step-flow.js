(function() {
    tinymce.PluginManager.add('step_flow', function(editor, url) {
        editor.addButton('step_flow', {
            text: 'ステップフロー',
            icon: false,
            onclick: function() {
                var template = '[step num="1"]テキストを入力してください[/step]\n[step num="2"]テキストを入力してください[/step]\n[step num="3"]テキストを入力してください[/step]';
                editor.insertContent(template);
            }
        });
    });
})();
