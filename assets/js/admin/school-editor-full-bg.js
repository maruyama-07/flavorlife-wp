(function() {
    var DEFAULT_HEX = '#f9f5f2';

    function sanitizeHex(raw) {
        var s = (raw || '').trim();
        if (!s) {
            return '';
        }
        if (s.charAt(0) !== '#') {
            s = '#' + s;
        }
        if (/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(s)) {
            return s.toLowerCase();
        }
        return null;
    }

    function expandShortHex(hex) {
        if (!hex || hex.length !== 4) {
            return hex;
        }
        return (
            '#' +
            hex.charAt(1) +
            hex.charAt(1) +
            hex.charAt(2) +
            hex.charAt(2) +
            hex.charAt(3) +
            hex.charAt(3)
        ).toLowerCase();
    }

    function findFullBgBlock(editor, node) {
        return editor.dom.getParent(node, 'div.c-school-editor-full-bg', editor.getBody());
    }

    /** 旧マークアップ（l-inner / __inner ラッパー）を外して直下ブロックにする */
    function unwrapLegacyInner(editor, outer) {
        var first = outer.firstElementChild;
        if (
            !first ||
            (!editor.dom.hasClass(first, 'l-inner') &&
                !editor.dom.hasClass(first, 'c-school-editor-full-bg__inner'))
        ) {
            return;
        }
        editor.undoManager.transact(function() {
            outer.innerHTML = first.innerHTML;
        });
    }

    /** style から background-color を読み、既定色と同一なら preset default */
    function readBgState(div) {
        if (!div) {
            return { preset: 'default', custom: '' };
        }
        var st = div.getAttribute('style') || '';
        var m = st.match(/background(?:-color)?\s*:\s*([^;]+)/i);
        if (!m) {
            return { preset: 'default', custom: '' };
        }
        var v = m[1]
            .trim()
            .split(/\s+/)[0]
            .toLowerCase();
        var hex = sanitizeHex(v);
        if (hex === null) {
            return { preset: 'default', custom: '' };
        }
        if (hex.length === 4) {
            hex = expandShortHex(hex);
        }
        if (hex === DEFAULT_HEX) {
            return { preset: 'default', custom: '' };
        }
        return { preset: 'custom', custom: hex };
    }

    function openFullBgModal(editor, existingDiv) {
        var isEdit = !!existingDiv;
        if (existingDiv) {
            unwrapLegacyInner(editor, existingDiv);
        }
        var state = readBgState(existingDiv);

        var vw =
            window.parent && window.parent.innerWidth ? window.parent.innerWidth : window.innerWidth || 1024;
        var modalW = Math.min(440, Math.max(300, vw - 40));

        editor.windowManager.open({
            title: isEdit ? '全幅背景ブロックを編集' : '全幅背景ブロックを挿入',
            width: modalW,
            body: [
                {
                    type: 'listbox',
                    name: 'bg_preset',
                    label: '背景色',
                    values: [
                        { text: '既定の薄ベージュ（#F9F5F2）', value: 'default' },
                        { text: 'カスタム（下欄で # を入力）', value: 'custom' }
                    ],
                    value: state.preset
                },
                {
                    type: 'textbox',
                    name: 'bg_custom',
                    label: 'カスタム色（# ＋ 3桁または6桁・例: #eeddcc）',
                    value: state.custom
                },
                {
                    type: 'container',
                    html:
                        '<p style="margin:8px 0 0;font-size:12px;color:#646970;line-height:1.5">' +
                        '既定色では style 属性を付けず、テーマのCSSが適用されます。カスタム時のみ background-color が付きます。' +
                        '</p>'
                }
            ],
            onsubmit: function(e) {
                var preset = (e.data.bg_preset || 'default').toString();
                var hex = '';
                if (preset === 'custom') {
                    var raw = (e.data.bg_custom || '').trim();
                    hex = sanitizeHex(raw);
                    if (raw !== '' && hex === null) {
                        editor.windowManager.alert(
                            '色の形式が正しくありません。#で始まる3桁または6桁の16進数にしてください。'
                        );
                        return;
                    }
                    if (!hex) {
                        editor.windowManager.alert(
                            '「カスタム」を選んだときは、# で始まる色を入力してください。'
                        );
                        return;
                    }
                }

                var attr = hex ? ' style="background-color: ' + hex + ';"' : '';

                if (isEdit) {
                    editor.undoManager.transact(function() {
                        editor.dom.setAttrib(existingDiv, 'class', 'c-school-editor-full-bg');
                        if (hex) {
                            editor.dom.setAttrib(existingDiv, 'style', 'background-color: ' + hex + ';');
                        } else {
                            editor.dom.setAttrib(existingDiv, 'style', null);
                        }
                    });
                    editor.nodeChanged();
                    editor.selection.select(existingDiv);
                    return;
                }

                var inner = editor.selection.getContent();
                var bodyHtml = inner ? inner : '<p>ここに本文を入力してください</p>';
                editor.insertContent(
                    '<div class="c-school-editor-full-bg"' + attr + '>' + bodyHtml + '</div>'
                );
            }
        });
    }

    tinymce.PluginManager.add('school_editor_full_bg', function(editor) {
        editor.addButton('school_editor_full_bg', {
            text: '全幅背景',
            tooltip: '全幅の背景ブロック（モーダルで色を設定・ブロック内で押すと編集）',
            onclick: function() {
                var node = editor.selection.getNode();
                var block = findFullBgBlock(editor, node);
                openFullBgModal(editor, block || null);
            }
        });
    });
})();
