(function() {
    function escapeShortcodeAttr(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function openMediaPicker(callback) {
        if (typeof wp === 'undefined' || !wp.media) {
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
            callback(attachment && attachment.id ? attachment.id : 0);
        });
        frame.open();
    }

    tinymce.PluginManager.add('two_column_cards', function(editor, url) {
        editor.addButton('two_column_cards', {
            text: '2カラムカード',
            icon: false,
            onclick: function() {
                if (typeof wp === 'undefined' || !wp.media) {
                    editor.windowManager.alert('メディアライブラリを読み込めませんでした。');
                    return;
                }

                var image1Id = 0;
                var image2Id = 0;

                openMediaPicker(function(id) {
                    image1Id = id || 0;
                    openMediaPicker(function(id2) {
                        image2Id = id2 || 0;

                        editor.windowManager.open({
                            title: '2カラムカードを挿入',
                            width: 500,
                            body: [
                                {
                                    type: 'container',
                                    html: '<p style="margin:0 0 16px;color:#666;font-weight:bold">左カラム</p>'
                                },
                                {
                                    type: 'container',
                                    html: '<p style="margin:0 0 4px;color:#999;font-size:12px">画像ID: ' + image1Id + '</p>'
                                },
                                {
                                    type: 'textbox',
                                    name: 'text1',
                                    label: 'テキスト',
                                    placeholder: '例：コンシューマー向けブランド卸販売'
                                },
                                {
                                    type: 'textbox',
                                    name: 'link1',
                                    label: 'ボタンリンクURL',
                                    placeholder: '例：/service/consumer'
                                },
                                {
                                    type: 'checkbox',
                                    name: 'new_tab1',
                                    label: '新規タブで開く',
                                    checked: false
                                },
                                {
                                    type: 'container',
                                    html: '<p style="margin:16px 0 16px;color:#666;font-weight:bold">右カラム</p>'
                                },
                                {
                                    type: 'container',
                                    html: '<p style="margin:0 0 4px;color:#999;font-size:12px">画像ID: ' + image2Id + '</p>'
                                },
                                {
                                    type: 'textbox',
                                    name: 'text2',
                                    label: 'テキスト',
                                    placeholder: '例：OEM/空間芳香'
                                },
                                {
                                    type: 'textbox',
                                    name: 'link2',
                                    label: 'ボタンリンクURL',
                                    placeholder: '例：/service/oem'
                                },
                                {
                                    type: 'checkbox',
                                    name: 'new_tab2',
                                    label: '新規タブで開く',
                                    checked: false
                                }
                            ],
                            onsubmit: function(e) {
                                var text1 = (e.data.text1 || '').trim();
                                var link1 = (e.data.link1 || '').trim();
                                var newTab1 = e.data.new_tab1 ? '1' : '';
                                var text2 = (e.data.text2 || '').trim();
                                var link2 = (e.data.link2 || '').trim();
                                var newTab2 = e.data.new_tab2 ? '1' : '';

                                var attrs = [
                                    'image1="' + image1Id + '"',
                                    'text1="' + escapeShortcodeAttr(text1) + '"',
                                    'link1="' + escapeShortcodeAttr(link1) + '"',
                                    (newTab1 ? 'new_tab1="1"' : ''),
                                    'image2="' + image2Id + '"',
                                    'text2="' + escapeShortcodeAttr(text2) + '"',
                                    'link2="' + escapeShortcodeAttr(link2) + '"',
                                    (newTab2 ? 'new_tab2="1"' : '')
                                ];
                                var content = '[two_column_cards ' + attrs.filter(Boolean).join(' ') + ']';
                                editor.insertContent(content);
                            }
                        });
                    });
                });
            }
        });
    });
})();
