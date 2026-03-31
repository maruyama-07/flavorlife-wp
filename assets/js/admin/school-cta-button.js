(function() {
    function escHtml(s) {
        return String(s).replace(/[&<>"']/g, function(ch) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[ch];
        });
    }

    function escAttr(s) {
        return escHtml(s);
    }

    tinymce.PluginManager.add('school_cta_button', function(editor) {
        editor.addButton('school_cta_button', {
            text: 'ボタン',
            tooltip: 'ヘッダーと同じピル型ボタンを挿入（リンク・文言・タブ切替を指定）',
            onclick: function() {
                var defaultHref = (typeof window.schoolTinymceCtaHref === 'string' && window.schoolTinymceCtaHref)
                    ? window.schoolTinymceCtaHref
                    : '/school/contact/';
                var defaultLabel = editor.selection.getContent({ format: 'text' });
                if (!defaultLabel || !defaultLabel.trim()) {
                    defaultLabel = 'お申し込みはこちら';
                } else {
                    defaultLabel = defaultLabel.trim();
                }

                editor.windowManager.open({
                    title: 'ボタンを挿入',
                    width: 480,
                    height: 420,
                    body: [
                        {
                            type: 'textbox',
                            name: 'href',
                            label: 'リンクURL',
                            value: defaultHref,
                            placeholder: 'https:// または / から始まるパス'
                        },
                        {
                            type: 'textbox',
                            name: 'label',
                            label: '表示テキスト',
                            value: defaultLabel
                        },
                        {
                            type: 'checkbox',
                            name: 'new_tab',
                            label: '新しいタブで開く',
                            checked: true
                        }
                    ],
                    onsubmit: function(e) {
                        var hrefVal = (e.data.href || '').trim();
                        if (!hrefVal) {
                            hrefVal = defaultHref;
                        }
                        var labelVal = (e.data.label || '').trim() || 'お申し込みはこちら';
                        var attrs = 'class="l-header-school__cta" href="' + escAttr(hrefVal) + '"';
                        if (e.data.new_tab) {
                            attrs += ' target="_blank" rel="noopener noreferrer"';
                        }
                        editor.insertContent(
                            '<p class="p-school-content-cta-wrap"><a ' + attrs + '>' + escHtml(labelVal) + '</a></p>'
                        );
                    }
                });
            }
        });
    });
})();
