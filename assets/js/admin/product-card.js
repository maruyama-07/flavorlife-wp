(function() {
    function escapeShortcodeAttr(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    tinymce.PluginManager.add('product_card', function(editor, url) {
        editor.addButton('product_card', {
            text: '商品カード',
            icon: false,
            onclick: function() {
                if (typeof wp === 'undefined' || !wp.media) {
                    editor.windowManager.alert('メディアライブラリを読み込めませんでした。');
                    return;
                }

                var frame = wp.media({
                    title: '画像を選択してください',
                    library: { type: 'image' },
                    multiple: false,
                    button: { text: '選択' }
                });
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    if (!attachment || !attachment.id) return;

                    var imageId = attachment.id;

                    editor.windowManager.open({
                        title: '商品カードを挿入',
                        width: 450,
                        body: [
                            {
                                type: 'container',
                                html: '<p style="margin:0 0 12px;color:#666">画像ID: ' + imageId + '</p>'
                            },
                            {
                                type: 'textbox',
                                name: 'title',
                                label: 'タイトル',
                                placeholder: '例：ブルーミングリモーネの香り'
                            },
                            {
                                type: 'textbox',
                                name: 'description',
                                label: '説明文',
                                multiline: true,
                                minHeight: 80,
                                placeholder: '例：ネロリやレモン、シダーウッドなどをあわせたオリジナルブレンド'
                            },
                            {
                                type: 'listbox',
                                name: 'border',
                                label: '枠の色',
                                values: [
                                    { text: 'なし', value: '' },
                                    { text: 'ゴールド', value: 'gold' },
                                    { text: 'パープル', value: 'purple' }
                                ],
                                value: ''
                            }
                        ],
                        onsubmit: function(e) {
                            var title = e.data.title || '';
                            var description = (e.data.description || '').trim();
                            var border = e.data.border || '';

                            var content = '[product_card image="' + imageId + '" title="' + escapeShortcodeAttr(title) + '"';
                            if (border) content += ' border="' + escapeShortcodeAttr(border) + '"';
                            content += ']';
                            if (description) {
                                content += description.replace(/\]/g, '\\]');
                            }
                            content += '[/product_card]';

                            editor.insertContent(content);
                        }
                    });
                });
                frame.open();
            }
        });
    });
})();
