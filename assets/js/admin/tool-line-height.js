(function() {
    /** style 属性から line-height の生の値だけ取得（computed の px 化を避ける） */
    function readLineHeightRaw(block) {
        if (!block || !block.getAttribute) {
            return '';
        }
        var st = block.getAttribute('style') || '';
        var m = st.match(/(?:^|;)\s*line-height\s*:\s*([^;]+)/i);
        return m ? m[1].replace(/!important/gi, '').trim() : '';
    }

    function validateLineHeight(v) {
        if (!v || !String(v).trim()) {
            return true;
        }
        var s = String(v)
            .trim()
            .toLowerCase()
            .replace(/\s/g, '');
        if (s === 'normal' || s === 'inherit' || s === 'initial' || s === 'unset') {
            return true;
        }
        // 1.5 / 2.2 / 32px / 1.2em / 180% など
        return /^[\d.]+(px|em|rem|%|pt)?$/.test(s);
    }

    function stripLhUtilityClasses(editor, block) {
        var cls = editor.dom.getAttrib(block, 'class') || '';
        if (!cls) {
            return;
        }
        var next = cls
            .split(/\s+/)
            .filter(function(c) {
                return c && !/^u-lh-/.test(c);
            })
            .join(' ');
        if (next) {
            editor.dom.setAttrib(block, 'class', next);
        } else {
            editor.dom.removeAttrib(block, 'class');
        }
    }

    tinymce.PluginManager.add('tool_line_height', function(editor) {
        editor.addButton('tool_line_height', {
            text: '行間',
            tooltip: '選択中ブロックの行の高さ（line-height）を数値で指定・解除',
            onclick: function() {
                var node = editor.selection.getNode();
                var block = editor.dom.getParent(
                    node,
                    'p,h1,h2,h3,h4,h5,h6,div,li,blockquote',
                    editor.getBody()
                );
                if (!block) {
                    editor.windowManager.alert(
                        '段落・見出し・リスト項目などにカーソルを置いてください。'
                    );
                    return;
                }

                var initial = readLineHeightRaw(block);
                var vw =
                    window.parent && window.parent.innerWidth
                        ? window.parent.innerWidth
                        : window.innerWidth || 1024;
                var vh =
                    window.parent && window.parent.innerHeight
                        ? window.parent.innerHeight
                        : window.innerHeight || 600;
                var modalW = Math.min(440, Math.max(300, vw - 40));

                editor.windowManager.open({
                    title: '行の高さ（line-height）',
                    classes: 'mce-tool-line-height-modal',
                    width: modalW,
                    height: Math.min(280, Math.max(220, Math.floor(vh * 0.35))),
                    body: [
                        {
                            type: 'textbox',
                            name: 'lh',
                            label:
                                '値（例: 1.5 / 2.2 / 32px / 1.75em / 180% / normal）',
                            value: initial,
                            placeholder: '空欄で line-height を解除'
                        },
                        {
                            type: 'container',
                            html:
                                '<p style="margin:8px 0 0;font-size:12px;color:#646970;line-height:1.5">' +
                                '無単位は倍率（1.5 = 行の1.5倍）。スタイルの「行間」クラスと併用した場合は、こちらを優先するためクラスを外します。' +
                                '</p>'
                        }
                    ],
                    onsubmit: function(e) {
                        var v = (e.data.lh || '')
                            .replace(/!important/gi, '')
                            .trim();
                        if (v && !validateLineHeight(v)) {
                            editor.windowManager.alert(
                                '入力形式を確認してください（例: 1.5 / 24px / 1.6em / 150% / normal）。'
                            );
                            return;
                        }
                        editor.undoManager.transact(function() {
                            stripLhUtilityClasses(editor, block);
                            if (!v) {
                                editor.dom.setStyle(block, 'line-height', '');
                            } else {
                                editor.dom.setStyle(block, 'line-height', v);
                            }
                        });
                        editor.nodeChanged();
                    }
                });
            }
        });
    });
})();
