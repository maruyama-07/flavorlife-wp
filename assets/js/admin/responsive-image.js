(function() {
    tinymce.PluginManager.add('responsive_image', function(editor, url) {
        editor.addButton('responsive_image', {
            text: 'レスポンシブ画像',
            icon: false,
            onclick: function() {
                if (typeof wp === 'undefined' || !wp.media) {
                    editor.windowManager.alert('メディアライブラリを読み込めませんでした。');
                    return;
                }

                var pcId = '';
                var spId = '';

                var openMedia = function(title, callback) {
                    var frame = wp.media({
                        title: title,
                        library: { type: 'image' },
                        multiple: false,
                        button: { text: '選択' }
                    });
                    frame.on('select', function() {
                        var attachment = frame.state().get('selection').first().toJSON();
                        if (attachment && attachment.id && callback) {
                            callback(attachment.id);
                        }
                    });
                    frame.open();
                };

                openMedia('PC用画像を選択してください', function(id) {
                    pcId = id;
                    openMedia('SP用画像を選択してください', function(id) {
                        spId = id;
                        var content = '[responsive_image pc="' + pcId + '" sp="' + spId + '"]';
                        editor.insertContent(content);
                    });
                });
            }
        });
    });
})();
