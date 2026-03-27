(function() {
    tinymce.PluginManager.add('image_grid', function(editor, url) {
        editor.addButton('image_grid', {
            text: '3画像グリッド',
            icon: false,
            onclick: function() {
                if (typeof wp === 'undefined' || !wp.media) {
                    editor.windowManager.alert('メディアライブラリを読み込めませんでした。');
                    return;
                }
                var frame = wp.media({
                    title: '画像を3枚選択してください',
                    multiple: true,
                    library: { type: 'image' },
                    button: { text: '挿入' }
                });
                frame.on('select', function() {
                    var attachments = frame.state().get('selection').toJSON();
                    var ids = attachments.slice(0, 3).map(function(a) { return a.id; }).join(',');
                    if (ids) {
                        editor.insertContent('[image_grid ids="' + ids + '"]');
                    }
                });
                frame.open();
            }
        });
    });
})();
