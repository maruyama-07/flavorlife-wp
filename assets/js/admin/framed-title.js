(function() {
    function escapeShortcodeAttr(str) {
        if (!str) {
            return '';
        }
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    tinymce.PluginManager.add('framed_title', function(editor) {
        editor.addButton('framed_title', {
            text: '枠付き見出し',
            tooltip: '枠付きセクション見出し（日本語＋英語サブ）',
            onclick: function() {
                editor.windowManager.open({
                    title: '枠付き見出しを挿入',
                    width: 480,
                    body: [
                        {
                            type: 'textbox',
                            name: 'main',
                            label: 'メイン見出し（日本語など）',
                            placeholder: '例：資格取得'
                        },
                        {
                            type: 'textbox',
                            name: 'sub',
                            label: 'サブ（英語など・小さめ表示）',
                            placeholder: '例：career advancement'
                        }
                    ],
                    onsubmit: function(e) {
                        var main = (e.data.main || '').trim();
                        var sub = (e.data.sub || '').trim();
                        if (!main && !sub) {
                            return;
                        }
                        var shortcode = '[framed_title';
                        if (main) {
                            shortcode += ' main="' + escapeShortcodeAttr(main) + '"';
                        }
                        if (sub) {
                            shortcode += ' sub="' + escapeShortcodeAttr(sub) + '"';
                        }
                        shortcode += ']';
                        editor.insertContent(shortcode);
                    }
                });
            }
        });
    });
})();
