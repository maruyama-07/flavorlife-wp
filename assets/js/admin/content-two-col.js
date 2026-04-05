(function() {
    tinymce.PluginManager.add('content_two_col', function(editor) {
        editor.addButton('content_two_col', {
            text: '2カラム',
            tooltip: '左右45%の2カラム（SPは縦並び）',
            onclick: function() {
                var html =
                    '<div class="c-content-two-col">' +
                    '<div class="c-content-two-col__col">' +
                    '<p><br></p>' +
                    '</div>' +
                    '<div class="c-content-two-col__col">' +
                    '<p><br></p>' +
                    '</div>' +
                    '</div>';
                editor.insertContent(html);
            }
        });
    });
})();
