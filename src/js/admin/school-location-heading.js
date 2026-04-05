(function() {
    function escText(s) {
        return String(s).replace(/[&<>"']/g, function(ch) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[ch];
        });
    }

    function normalizeHex(input, fallbackHex) {
        var fb = fallbackHex || '#042C1B';
        var s = String(input || '').trim();
        if (!s) {
            return fb;
        }
        if (s.charAt(0) !== '#') {
            s = '#' + s;
        }
        if (/^#[0-9A-Fa-f]{6}$/.test(s)) {
            return s;
        }
        if (/^#[0-9A-Fa-f]{3}$/.test(s)) {
            return (
                '#' +
                s.charAt(1) + s.charAt(1) +
                s.charAt(2) + s.charAt(2) +
                s.charAt(3) + s.charAt(3)
            );
        }
        return fb;
    }

    function openHeadingModal(editor, withBottomLine) {
        var sel = editor.selection.getContent({ format: 'text' });
        var defaultText = sel && sel.trim() ? sel.trim() : 'LOCATION';

        editor.windowManager.open({
            title: withBottomLine ? '横線＋下線の見出し' : '横線見出し（下線なし）',
            width: 420,
            body: [
                {
                    type: 'textbox',
                    name: 'text',
                    label: '見出しテキスト',
                    value: defaultText
                },
                {
                    type: 'textbox',
                    name: 'color',
                    label: '左線・下線・文字色（#＋6桁の16進）',
                    value: '#042C1B'
                }
            ],
            onsubmit: function(e) {
                var text = (e.data.text || '').trim();
                if (!text) {
                    text = 'LOCATION';
                }
                var hex = normalizeHex(e.data.color, '#042C1B');
                var cls = 'c-school-heading';
                if (!withBottomLine) {
                    cls += ' c-school-heading--no-underline';
                }
                /** color + currentColor（WP の style サニタイズでも残りやすい） */
                var style = 'color: ' + hex + ';';
                editor.insertContent(
                    '<h2 class="' + cls + '" style="' + style + '">' + escText(text) + '</h2>'
                );
            }
        });
    }

    function openBarHeadingModal(editor) {
        var sel = editor.selection.getContent({ format: 'text' });
        var defaultText = sel && sel.trim() ? sel.trim() : '見出し';

        editor.windowManager.open({
            title: '中央下線バー見出し',
            width: 420,
            body: [
                {
                    type: 'textbox',
                    name: 'text',
                    label: '見出しテキスト',
                    value: defaultText
                },
                {
                    type: 'textbox',
                    name: 'color',
                    label: 'バー色（#＋6桁の16進・文字は黒固定）',
                    value: '#CDB030'
                }
            ],
            onsubmit: function(e) {
                var text = (e.data.text || '').trim();
                if (!text) {
                    text = '見出し';
                }
                var hex = normalizeHex(e.data.color, '#CDB030');
                editor.insertContent(
                    '<h2 class="c-school-heading-bar" style="--c-school-heading-bar-rule: ' +
                        hex +
                        ';">' +
                        escText(text) +
                        '<span class="c-school-heading-bar__rule" aria-hidden="true"></span></h2>'
                );
            }
        });
    }

    tinymce.PluginManager.add('school_location_heading', function(editor) {
        editor.addButton('school_location_heading', {
            text: '横線+下線見出し',
            tooltip: '左線＋下線付き見出し（色指定可）',
            onclick: function() {
                openHeadingModal(editor, true);
            }
        });
        editor.addButton('school_location_heading_plain', {
            text: '横線見出し',
            tooltip: '左線のみの見出し（下線なし・色指定可）',
            onclick: function() {
                openHeadingModal(editor, false);
            }
        });
        editor.addButton('school_heading_bar', {
            text: '中央下線バー',
            tooltip: '文字は黒・中央バーは色指定可',
            onclick: function() {
                openBarHeadingModal(editor);
            }
        });
    });
})();
